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
    /**
     * List installments for a specific charge.
     *
     * @OA\Get(
     *      path="/api/v1/charges/{chargeId}/installments",
     *      operationId="listInstallments",
     *      tags={"Installments"},
     *      summary="List installments for a specific charge",
     *      description="Retrieve a list of installments associated with a specific charge.",
     *      @OA\Parameter(
     *          name="chargeId",
     *          in="path",
     *          required=true,
     *          description="ID of the charge to retrieve installments",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success: Installments listed successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Installment listing completed successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="Installments", type="array",
     *                      @OA\Items(ref="#/components/schemas/Installment")
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Error: Charge not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Charge not found"),
     *              @OA\Property(property="data", type="null"),
     *          ),
     *      ),
     * )
     */
    public function listInstallments($chargeId)
    {
        $charge = Charge::findOrFail($chargeId);
        $installments = $charge->installments()->get();
        return response()->json(['status' => 'success', 'message' => 'Installment listing completed successfully', 'data' => ['Installments' => $installments]], 200);
    }


    /**
     * @OA\Get(
     *     path="/charges",
     *     summary="List all charges",
     *     description="Returns a list of all charges",
     *     tags={"Charges"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="Status of the response",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message about the operation status",
     *                 example="Charge listing completed successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Data containing the list of charges",
     *                 @OA\Property(
     *                     property="charges",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Charge")
     *                 )
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Charge listing completed successfully', 'data' => ['charges' => Charge::all()]], 200);
    }

    /**
     * @OA\Get(
     *     path="/charges/{charge}",
     *     summary="Get a specific charge",
     *     description="Returns a specific charge by ID",
     *     tags={"Charges"},
     *     @OA\Parameter(
     *         name="charge",
     *         in="path",
     *         description="ID of the charge to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="Status of the response",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message about the operation status",
     *                 example="Charge found successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Data containing the specific charge",
     *                 @OA\Property(
     *                     property="charge",
     *                     ref="#/components/schemas/Charge"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Charge not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="Status of the response",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message about the error",
     *                 example="Charge not found"
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Charge $charge)
    {
        return response()->json(['status' => 'success', 'message' => 'Charge found successfully', 'data' => ['charge' => $charge]], 200);
    }


    /**
     * @OA\Post(
     *     path="/charges",
     *     summary="Create a new charge",
     *     description="Creates a new charge and related installments",
     *     tags={"Charges"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Charge data",
     *         @OA\JsonContent(
     *             required={"name", "description", "amount", "installments_number", "due_day", "collector_id", "debtor_id"},
     *             @OA\Property(property="name", type="string", description="Name of the charge"),
     *             @OA\Property(property="description", type="string", description="Description of the charge"),
     *             @OA\Property(property="amount", type="integer", description="Total debt amount"),
     *             @OA\Property(property="installments_number", type="integer", description="Number of installments"),
     *             @OA\Property(property="due_day", type="integer", description="Due day"),
     *             @OA\Property(property="collector_id", type="integer", description="Collector user ID"),
     *             @OA\Property(property="debtor_id", type="integer", description="Debtor user ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Charge successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="success"),
     *             @OA\Property(property="message", type="string", description="Message about the operation status", example="Charge successfully created"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Data containing the created charge and its installments",
     *                 @OA\Property(
     *                     property="charge",
     *                     ref="#/components/schemas/Charge"
     *                 ),
     *                 @OA\Property(
     *                     property="installments",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Installment")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="error"),
     *             @OA\Property(property="message", type="string", description="Message about the error", example="You need to choose whether you are the collector or the debtor of the charge"),
     *             @OA\Property(property="errors", type="object", description="Detailed error messages")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - Charge already exists",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", description="Status of the response", example="error"),
     *             @OA\Property(property="message", type="string", description="Message about the error", example="This charge already exists"),
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
        return response()->json(['status' => 'success', 'message' => 'Charge successfully created', 'data' => ['charge' => $charge]], 201);
    }


    /**
     * @OA\Post(
     *     path="/charge-invitations",
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
     *     path="/your-endpoint-here/process-invitations/{token}",
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
     *     path="/your-endpoint-here/get-payments-for-approval/{charge}",
     *     summary="Get Payments for Approval",
     *     description="Retrieve payments awaiting approval for a particular charge.",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="charge",
     *         in="path",
     *         required=true,
     *         description="ID of the charge",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="charge_id",
     *         in="query",
     *         required=true,
     *         description="ID of the charge",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved payments awaiting approval",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Listing of payments awaiting approval completed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="installments", type="array", @OA\Items(ref="#/components/schemas/Installment"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not the collector of this charge",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You are not the collector of this charge, so you cannot get payments on approval")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/your-endpoint-here/upload-receipt/{installment}",
     *     summary="Upload Receipt",
     *     description="Upload a receipt image for a specific installment.",
     *     tags={"Receipts"},
     *     @OA\Parameter(
     *         name="installment",
     *         in="path",
     *         required=true,
     *         description="ID of the installment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Receipt image to upload",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image",
     *                     description="Receipt image file (JPEG, PNG, JPG, GIF - Max 2MB)",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(
     *                     property="charge_id",
     *                     description="ID of the charge",
     *                     type="integer"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receipt uploaded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Proof sent successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="path", type="string", example="/path/to/uploaded/receipt"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Installment already under payment approval analysis",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="This installment is already under payment approval analysis")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - You cannot send payment for an installment of a charge for which you are not the debtor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You cannot send payment for an installment of a charge for which you are not the debtor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity - Missing or invalid parameters",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example="{ 'image': ['The image field is required.'] }")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/your-endpoint-here/send-payment/{installment}",
     *     summary="Send Payment for Approval",
     *     description="Send payment for approval of a specific installment.",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="installment",
     *         in="path",
     *         required=true,
     *         description="ID of the installment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body with charge ID",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="charge_id",
     *                     description="ID of the charge",
     *                     type="integer"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment sent for approval successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment of the installment of the charge sent for analysis successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - You cannot send payment for an installment of a charge for which you are not the debtor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You cannot send payment for an installment of a charge for which you are not the debtor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - This installment is already under payment approval analysis",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="This installment is already under payment approval analysis")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity - Before sending the payment for analysis you need to send proof of payment",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Before sending the payment for analysis you need to send proof of payment")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/your-endpoint-here/accept-payment-approval/{charge}/{installment}",
     *     summary="Accept Payment Approval by Collector",
     *     description="Accept payment approval for a specific installment by the collector.",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="charge",
     *         in="path",
     *         required=true,
     *         description="ID of the charge",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="installment",
     *         in="path",
     *         required=true,
     *         description="ID of the installment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Request body",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment marked as paid successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment marked as paid successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - You are not the collector of this charge and therefore cannot approve payments for this charge",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You are not the collector of this charge and therefore cannot approve payments for this charge")
     *         )
     *     )
     * )
     */
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
