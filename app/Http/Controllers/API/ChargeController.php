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
        return response()->json(['status' => 'success', 'message' => 'Installment listing completed successfully', 'data' => ['Installments' => $installments]], 200);
    }

    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Charge listing completed successfully', 'data' => ['charges' => Charge::all()]], 200);
    }

    public function show(Charge $charge)
    {
        return response()->json(['status' => 'success', 'message' => 'Charge found successfully', 'data' => ['charge' => $charge]], 200);
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
            return response()->json(['status' => 'error', 'message' => 'You need to choose whether you are the collector or the debtor of the charge', 'errors' => null], 422);
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
            return response()->json(['status' => 'error', 'message' => 'This charge already exists', 'errors' => null], 409);
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
            return response()->json(['status' => 'error', 'message' => 'Unexpected error when creating resource', 'errors' => null], 500);
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
            return response()->json(['status' => 'error', 'message' => 'Error occurred while saving models to the database.', 'errors' => null], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Charge successfully created', 'data' => ['charge' => $charge, 'installments' => $installments]], 201);
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
                return response()->json(['status' => 'error', 'message' => 'You already participate in this charge', 'errors' => null], 400);
            }
            $token = Str::random(40);

            $chargeInvitation = ChargeInvitation::where([
                ['email', $request->email],
                ['charge_id', $request->charge_id],
                ['user_id', auth()->id()]
            ])->first();
            $currentDate = Carbon::now();
            if ($chargeInvitation && $chargeInvitation->updated_at->diffInDays($currentDate) < 7) {
                return response()->json(['status' => 'error', 'message' => 'Invitation limit for this email and billing exceeded. Wait a week before sending a new invitation', 'errors' => null], 429);
            }
            if (!$chargeInvitation) {
                $chargeInvitation = ChargeInvitation::create([
                    'email' => $request->email,
                    'token' => $token,
                    'charge_id' => $request->charge_id,
                    'user_id' => auth()->id()
                ]);
                if ($chargeInvitation === null) {
                    return response()->json(['status' => 'error', 'message' => 'Unexpected error when creating resource', 'errors' => null], 500);
                }
            }
            // $chargeInvitation->update(['updated_at' => now()]);
            DB::table('charge_invitations')->where('id', $chargeInvitation->id)->update(['updated_at' => DB::raw('CURRENT_TIMESTAMP')]);
            $this->sendChargeInvitationNotification($token, $charge);
            return response()->json(['status' => 'success', 'message' => 'Invitation to register and participate in billing sent successfully', 'data' => ['charge' => $charge, 'chargeInvitation' => $chargeInvitation]], 201);
        }
    }

    public function processInvitations($token)
    {
        $chargeInvitation = ChargeInvitation::where('token', $token)->first();
        $currentDate = Carbon::now();
        if ($chargeInvitation && $chargeInvitation->updated_at->diffInDays($currentDate) >= 7) {
            return response()->json(['status' => 'error', 'message' => 'This invite link has expired or does not exist', 'errors' => null], 410);
        }
        if (!$chargeInvitation->is_valid) {
            return response()->json(['status' => 'error', 'message' => 'This invitation link has already been used and is no longer valid', 'errors' => null], 410);
        }
        $charge = Charge::where('id', $chargeInvitation->charge_id)->first();
        if ($charge->collector_id && $charge->debtor_id) {
            return response()->json(['status' => 'error', 'message' => 'It is not possible to participate in this charge, there is already a collector and the debtor', 'errors' => null], 409);
        }
        $side1 = $charge->collector_id === $chargeInvitation->user_id ? 'debtor_id' : ($charge->debtor_id === $chargeInvitation->user_id ? 'collector_id' : '');
        $side2 = $side1 == 'collector_id' ? 'debtor_id' : 'collector_id';
        if ($charge[$side2] === auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You cannot play the role of collector and debtor at the same time in a charge', 'errors' => null], 422);
        }
        $charge->update([$side1 => auth()->id()]);
        return response()->json(['status' => 'success', 'message' => 'Congratulations now you participate in this charge', 'data' => ['charge' => $charge, 'chargeInvitation' => $chargeInvitation]], 200);
    }

    public function getPaymentsForApproval(Request $request, Charge $charge)
    {
        $request->validate([
            'charge_id' => 'required|integer'
        ]);
        if ($charge->collector_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You are not the collector of this charge, so you cannot get payments on approval', 'errors' => null], 403);
        }
        $installments = $charge->installments()->where('awaiting_approval', true)->get();
        return response()->json(['status' => 'success', 'message' => 'Listing of payments under approval listed successfully', 'data' => ['installments' => $installments]], 200);
    }

    public function uploadReceipt(Installment $installment, Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'charge_id' => 'required|integer'
        ]);
        if (!$request->hasFile('image')) {
            return response()->json(['status' => 'error', 'message' => 'This installment is already under payment approval analysis', 'errors' => null], 400);
        }
        $charge = $installment->charge()->first();
        if (!$charge->debtor_id || $charge->debtor_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You cannot send payment for an installment of a charge for which you are not the debtor', 'errors' => null], 403);
        }
        $image = $request->file('image');
        $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $newFileName = $filename . "_" . time() . "." . $extension;
        $path = $image->storeAs("public/receipts/{$charge->id}/", $newFileName);
        $installment->update(['payment_proof' => json_encode([
            "originalFileName" => $filename . "." . $extension,
            "newFileName" => $newFileName,
            "path" => $path
        ])]);
        return response()->json(['status' => 'success', 'message' => 'Proof sent successfully', 'data' => ['path' => $path]], 200);
    }

    public function sendPayment(Installment $installment, Request $request)
    {
        $request->validate([
            'charge_id' => 'required|integer'
        ]);
        if ($installment->awaiting_approval) {
            return response()->json(['status' => 'error', 'message' => 'This installment is already under payment approval analysis', 'errors' => null], 409);
        }
        if (empty($installment->payment_proof)) {
            return response()->json(['status' => 'error', 'message' => 'Before sending the payment for analysis you need to send proof of payment', 'errors' => null], 422);
        }
        $charge = $installment->charge()->first();
        if (!$charge->debtor_id || $charge->debtor_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You cannot send payment for an installment of a charge for which you are not the debtor', 'errors' => null], 403);
        }
        $installment->update(['awaiting_approval' => true]);
        return response()->json(['status' => 'success', 'message' => 'Payment of the installment of the charge sent for analysis successfully', 'data' => null], 200);
    }

    public function acceptPaymentApprovalByCollector(Request $request, Charge $charge, Installment $installment)
    {
        if ($charge->collector_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You are not the collector of this charge and therefore cannot approve payments for this charge.', 'errors' => null], 403);
        }
        $installment->update(['paid' => true, 'user_id' => $charge->debtor_id]);
        return response()->json(['status' => 'success', 'message' => 'Payment marked as paid successfully', 'data' => null], 200);
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
