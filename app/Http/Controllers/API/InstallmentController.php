<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/your-endpoint-here/installments",
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
     *     path="/your-endpoint-here/installments/{installment}",
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
}
