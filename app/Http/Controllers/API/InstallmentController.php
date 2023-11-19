<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index()
    {
        // return auth()->user()->installments;
        return Installment::all();
    }
}
