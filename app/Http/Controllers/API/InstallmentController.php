<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index()
    {
        return response()->json(["installments" => Installment::all()], 200);
    }

    public function show(Installment $installment)
    {
        return response()->json(["installment" => $installment], 200);
    }
}
