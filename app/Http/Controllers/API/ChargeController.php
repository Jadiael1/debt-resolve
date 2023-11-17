<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Charge;

use function PHPUnit\Framework\isEmpty;

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
        $mergedResult = $collectors->merge($debtors);
        return $mergedResult;
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

        return response()->json(['message' => 'Charge successfully created'], 201);
    }
}
