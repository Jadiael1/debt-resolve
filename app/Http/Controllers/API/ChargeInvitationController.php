<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\ChargeInvitation;
use App\Models\User;
use App\Notifications\DefaultNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChargeInvitationController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/charge-invitations/invitations",
     *     security={{"bearerAuth": {}}},
     *     summary="Send charge invitation",
     *     description="Sends an invitation to participate in a charge",
     *     tags={"Charge Invitations"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Charge invitation data",
     *         @OA\JsonContent(
     *             required={"email", "charge_id"},
     *             @OA\Property(property="email", type="string", description="Email of the invitee"),
     *             @OA\Property(property="charge_id", type="integer", description="ID of the charge")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invitation sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="success"),
     *             @OA\Property(property="message", type="string", description="Message about the operation status", example="Invitation to register and participate in billing sent successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Data containing the charge and invitation details",
     *                 @OA\Property(property="charge", ref="#/components/schemas/Charge"),
     *                 @OA\Property(property="chargeInvitation", ref="#/components/schemas/ChargeInvitation")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - User already participates in this charge",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="error"),
     *             @OA\Property(property="message", type="string", description="Message about the error", example="You already participate in this charge"),
     *             @OA\Property(property="errors", type="object", description="Detailed error messages")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Requests - Invitation limit for this email and billing exceeded",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="error"),
     *             @OA\Property(property="message", type="string", description="Message about the error", example="Invitation limit for this email and billing exceeded. Wait a week before sending a new invitation"),
     *             @OA\Property(property="errors", type="object", description="Detailed error messages")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error when creating resource",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="error"),
     *             @OA\Property(property="message", type="string", description="Message about the error", example="Unexpected error when creating resource"),
     *             @OA\Property(property="errors", type="object", description="Detailed error messages")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/charge-invitations/process-charge-invitations/{token}",
     *     security={{"bearerAuth": {}}},
     *     summary="Process Charge Invitations",
     *     description="Process the charge invitations based on the provided token.",
     *     tags={"Charge Invitations"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token associated with the charge invitation",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful participation in the charge",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Congratulations! Now you participate in this charge"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="charge", type="object", ref="#/components/schemas/Charge"),
     *                 @OA\Property(property="chargeInvitation", type="object", ref="#/components/schemas/ChargeInvitation")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="Expired or invalid invitation link",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="This invite link has expired or does not exist")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Unable to participate in the charge",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="It is not possible to participate in this charge")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid request - trying to play both collector and debtor roles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You cannot play the role of collector and debtor at the same time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unexpected error occurred when processing the invitation")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/charge-invitations/",
     *     security={{"bearerAuth": {}}},
     *     summary="List Invitations",
     *     description="Retrieve a list of all invitations.",
     *     tags={"Charge Invitations"},
     *     @OA\Response(
     *         response=200,
     *         description="Invitations listed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invitations listed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="invitations",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Invitations listed successfully', 'data' => ['charge-invitations' => ChargeInvitation::all()]], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/charge-invitations/{chargeInvitation}/charge-invitation",
     *     security={{"bearerAuth": {}}},
     *     summary="Get Invitation by ID",
     *     description="Retrieve a specific invitation by its ID.",
     *     tags={"Charge Invitations"},
     *     @OA\Parameter(
     *         name="chargeInvitation",
     *         in="path",
     *         required=true,
     *         description="ID of the invitation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation found successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invitation found successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="invitation",
     *                     type="object"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(int $chargeInvitation)
    {
        $chargeInvitation = \App\Models\ChargeInvitation::where('id', $chargeInvitation)->first();
        return response()->json(['status' => 'success', 'message' => 'Invitations successfully found', 'data' => ['charge-invitation' => $chargeInvitation]], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/charge-invitations/{email}/email",
     *     security={{"bearerAuth": {}}},
     *     summary="Get Invitations by Email",
     *     description="Retrieve invitations by email.",
     *     tags={"Charge Invitations"},
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Email to search for invitations",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitations found successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invitations found successfully by email"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="invitation",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getByEmail(string $email)
    {
        $chargeInvitation = ChargeInvitation::where('email', $email)->get();
        return response()->json(['status' => 'success', 'message' => 'Invitations successfully found by email', 'data' => ['charge-invitation' => $chargeInvitation]], 200);
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
