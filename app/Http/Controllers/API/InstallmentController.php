<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function generatePayment($installment_id)
    {
        $installment = Installment::findOrFail($installment_id);
        return response()->json(['message' => 'Pagamento gerado com sucesso']);
    }

    public function proofUpload($installment_id)
    {
        $installment = Installment::findOrFail($installment_id);
        return response()->json(['message' => 'Comprovante enviado com sucesso']);
    }
}
