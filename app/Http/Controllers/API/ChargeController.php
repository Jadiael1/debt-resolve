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
     * @OA\Get(
     *     path="/api/v1/charges",
     *     security={{"bearerAuth": {}}},
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
     *     path="/api/v1/charges/{charge}",
     *     security={{"bearerAuth": {}}},
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
     *     path="/api/v1/charges",
     *     security={{"bearerAuth": {}}},
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
}
