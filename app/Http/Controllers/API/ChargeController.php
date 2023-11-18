<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Charge;
use App\Models\ChargeInvitation;
use App\Models\Installment;
use App\Models\User;
use App\Notifications\DefaultNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChargeController extends Controller
{
    public function listInstallments($chargeId)
    {
        $charge = Charge::findOrFail($chargeId);
        $installments = $charge->installments()->get();
        return response()->json(['Installments' => $installments]);
    }


    public function index()
    {
        $collectors = auth()->user()->collectors;
        $debtors = auth()->user()->debtors;
        return ["debtors" => $collectors, "collectors" => $debtors];
        // $mergedResult = $collectors->merge($debtors);
        // return $mergedResult;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|integer',
            'installments_number' => 'required|integer',
            'due_day' => 'required|integer',
            'collector_id' => 'integer',
            'debtor_id' => 'integer'
        ]);
        if (!$request->collector_id && !$request->debtor_id) {
            return response()->json(['message' => 'You need to choose whether you are the collector or the debtor of the charge'], 422);
        }

        $charge = Charge::where(
            [
                ['name', $request->name],
                ['description', $request->description],
                ['amount', $request->amount],
                ['installments_number', $request->installments_number],
                ['due_day', $request->due_day],
                ['collector_id', $request->collector_id],
                ['debtor_id', $request->debtor_id],
            ]
        )->first();

        if ($charge !== null) {
            return response()->json(['message' => 'This charge already exists'], 409);
        }

        $charge = Charge::create([
            'name' => $request->name, // nome da cobrança
            'description' => $request->description, // descrição da cobrança
            'amount' => $request->amount, // Valor total da dívida
            'installments_number' => $request->installments_number, // Número de parcelas
            'due_day' => $request->due_day, // dia de vencimento
            'collector_id' => $request->collector_id, // ID do usuário cobrador
            'debtor_id' => $request->debtor_id // ID do usuário devedor enviado pelo formulário
        ]);

        if ($charge === null) {
            return response()->json(['message' => 'Unexpected error when creating resource'], 500);
        }

        $installments = array_map(function ($iterator) use ($charge) {
            $installment = new Installment();
            $installment->value = $charge->amount / $charge->installments_number;
            $installment->installment_number = $iterator;
            $installment->charge_id = $charge->id;
            $installment->due_date = Carbon::now()->day($charge->due_day)->addMonths($iterator);
            return $installment;
        }, range(1, $charge->installments_number));

        try {
            DB::beginTransaction();
            foreach ($installments as $installment) {
                $installment->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            response()->json(['error' => 'Error occurred while saving models to the database.'], 500);
        }

        return response()->json(['message' => 'Charge successfully created'], 201);
    }

    public function processChargeInvitations($token)
    {
        $chargeInvitation = ChargeInvitation::where('token', $token)->first();
        $currentDate = Carbon::now();
        if ($chargeInvitation && $chargeInvitation->updated_at->diffInDays($currentDate) >= 7) {
            return response()->json(['message' => 'This invite link has expired or does not exist'], 410);
        }
        if (!$chargeInvitation->is_valid) {
            return response()->json(['message' => 'This invitation link has already been used and is no longer valid'], 410);
        }
        $charge = Charge::where('id', $chargeInvitation->charge_id)->first();
        $side1 = $charge->collector_id === $chargeInvitation->user_id ? 'debtor_id' : ($charge->debtor_id === $chargeInvitation->user_id ? 'collector_id' : '');
        if ($charge->collector_id && $charge->debtor_id) {
            return response()->json(['message' => 'It is not possible to participate in this charge, there is already a collector and the debtor'], 409);
        }
        $side2 = $side1 == 'collector_id' ? 'debtor_id' : 'collector_id';
        if ($charge[$side2] === auth()->id()) {
            return response()->json(['message' => 'You cannot play the role of collector and debtor at the same time in a charge'], 422);
        }
        $charge->update([$side1 => auth()->id()]);
        return response()->json(['message' => 'Congratulations now you participate in this charge'], 200);
    }

    public function chargeInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'charge_id' => 'required|integer'
        ]);
        $charge = Charge::where('id', $request->charge_id)->first();
        if ($charge) {
            if ($request->email === auth()->user()->email) {
                return response()->json(['message' => 'You already participate in this charge!'], 400);
            }
            $token = Str::random(40);
            $chargeInvitation = ChargeInvitation::where([
                ['email', $request->email],
                ['charge_id', $request->charge_id],
                ['user_id', auth()->id()]
            ])->first();
            $currentDate = Carbon::now();
            if ($chargeInvitation && $chargeInvitation->updated_at->diffInDays($currentDate) < 7) {
                return response()->json(['message' => 'Invitation limit for this email and billing exceeded. Wait a week before sending a new invitation.'], 429);
            }
            if (!$chargeInvitation) {
                $chargeInvitation = ChargeInvitation::create([
                    'email' => $request->email,
                    'token' => $token,
                    'charge_id' => $request->charge_id,
                    'user_id' => auth()->id()
                ]);
                if ($chargeInvitation === null) {
                    return response()->json(['message' => 'Unexpected error when creating resource'], 500);
                }
            }
            DB::table('charge_invitations')->where('id', $chargeInvitation->id)->update(['updated_at' => DB::raw('CURRENT_TIMESTAMP')]);
            $this->sendChargeInvitationNotification($token, $charge);
            return response()->json(['message' => 'Invitation to register and participate in billing sent successfully'], 200);
        }
    }

    public function chargeInvitations($email)
    {
        $chargeInvitation = ChargeInvitation::where('email', $email)->get();
        return $chargeInvitation;
    }

    private function sendChargeInvitationNotification($token, $charge)
    {
        $user = User::where('id', auth()->id())->first();
        $token = $token;
        $subject = "Convite para registro e participar de cobrança(s) (" . config('app.name') . ")";
        $lines = array(
            "Você foi convidado para participar da cobrança: {$charge->name} pelo usuario {$user->name} ({$user->email})",
            "Se você não desejar participar dessa cobrança, você pode ignorar este e-mail.",
        );
        $greeting = "Olá " . ucfirst($user->name) . ",";
        $url = env('FRONT_INVITE_URL', 'https://example.com/invite/') . $token;
        $user->notify(new DefaultNotification($token, $subject, $greeting, $lines, $url));
    }
}
