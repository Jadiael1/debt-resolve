<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/installments",
     *     security={{"bearerAuth": {}}},
     *     summary="List Installments",
     *     description="Retrieve a list of installments.",
     *     tags={"Installments"},
     *     @OA\Response(
     *         response=200,
     *         description="Installments listed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Installments listed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="installments",
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
        return response()->json(['status' => 'success', 'message' => 'Installments listed successfully', 'data' => ["installments" => Installment::all()]], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/installments/{installment}/installment",
     *     security={{"bearerAuth": {}}},
     *     summary="Show Installment",
     *     description="Retrieve a specific installment by ID.",
     *     tags={"Installments"},
     *     @OA\Parameter(
     *         name="installment",
     *         in="path",
     *         required=true,
     *         description="ID of the installment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Installment found successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Installment found successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="installment",
     *                     type="object"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Installment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Installment not found"),
     *             @OA\Property(property="errors", type="null")
     *         )
     *     )
     * )
     */
    public function show(Installment $installment)
    {
        return response()->json(['status' => 'success', 'message' => 'Installment found successfully', 'data' => ["installment" => $installment]], 200);
    }

    /**
     * List installments for a specific charge.
     *
     * @OA\Get(
     *      path="/api/v1/installments/charge/{charge_id}/",
     *      security={{"bearerAuth": {}}},
     *      operationId="listInstallments",
     *      tags={"Installments"},
     *      summary="List installments for a specific charge",
     *      description="Retrieve a list of installments associated with a specific charge.",
     *      @OA\Parameter(
     *          name="charge",
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
     *              @OA\Property(property="message", type="string", example="Charge installment listing completed successfully"),
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
    public function listInstallments(Charge $charge)
    {
        // $charge = Charge::findOrFail($chargeId);
        $installments = $charge->installments()->get();
        return response()->json(['status' => 'success', 'message' => 'Charge installment listing completed successfully', 'data' => ['Installments' => $installments]], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/installments/upload-receipt/{installment}",
     *     security={{"bearerAuth": {}}},
     *     summary="Upload Receipt",
     *     description="Upload a receipt image for a specific installment.",
     *     tags={"Installments"},
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
     *     path="/api/v1/installments/send-payment/{installment}",
     *     security={{"bearerAuth": {}}},
     *     summary="Send Payment for Approval",
     *     description="Send payment for approval of a specific installment.",
     *     tags={"Installments"},
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
     * @OA\Get(
     *     path="/api/v1/installments/get-payments-for-approval/{charge}/charge",
     *     security={{"bearerAuth": {}}},
     *     summary="Get Payments for Approval",
     *     description="Retrieve payments awaiting approval for a particular charge.",
     *     tags={"Installments"},
     *     @OA\Parameter(
     *         name="charge",
     *         in="path",
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
     *             @OA\Property(property="message", type="string", example="Successful listing of payments for a given charge under approval"),
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
    public function getPaymentsForApproval(Charge $charge)
    {
        // $request->validate([
        //     'charge_id' => 'required|integer'
        // ]);
        if ($charge->collector_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You are not the collector of this charge, so you cannot get payments on approval', 'errors' => null], 403);
        }
        $installments = $charge->installments()->where('awaiting_approval', true)->get();
        return response()->json(['status' => 'success', 'message' => 'Successful listing of payments for a given charge under approval', 'data' => ['installments' => $installments]], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/installments/{installment}/charge/{charge}/accept-payment-approval-by-collector",
     *     security={{"bearerAuth": {}}},
     *     summary="Accept Payment Approval by Collector",
     *     description="Accept payment approval for a specific installment by the collector.",
     *     tags={"Installments"},
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
    public function acceptPaymentApprovalByCollector(Installment $installment, Charge $charge)
    {
        if ($charge->collector_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'You are not the collector of this charge and therefore cannot approve payments for this charge.', 'errors' => null], 403);
        }
        $installment->update(['paid' => true, 'user_id' => $charge->debtor_id]);
        return response()->json(['status' => 'success', 'message' => 'Payment marked as paid successfully', 'data' => null], 200);
    }


}
