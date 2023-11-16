<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Charge;

class ChargeController extends Controller
{


    public function listInstallments($chargeId)
    {
        $charge = Charge::findOrFail($chargeId);
        $installments = $charge->installments_number; // Supondo que existe um relacionamento 'parcelas' na sua model
        return response()->json(['Installments' => $installments]);
    }


    public function store(Request $request)
    {
        $charge = new Charge();
        $charge->collector_id = auth()->id(); // ID do usuário cobrador
        $charge->debtor_id = $request->debtor_id; // ID do usuário devedor enviado pelo formulário
        $charge->amount = $request->amount; // Valor total da dívida
        $charge->installments_number = $request->installments_number; // Número de parcelas
        // Lógica para calcular e salvar o valor de cada parcela
        $installment_value = $charge->amount / $charge->installments_number;
        // Lógica para definir o dia de vencimento de cada parcela (exemplo: dia 10 de cada mês)
        $charge->due_day = $request->due_day;
        // Salvar a cobrança
        $charge->save();
        return response()->json(['message' => 'Charge successfully created']);
    }

}
