<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function installmentList()
    {
        return response()->json(["installments" => auth()->user()->installments], 200);
    }

    public function chargeList()
    {
        $collectors = auth()->user()->collectors;
        $debtors = auth()->user()->debtors;
        return response()->json(["debtors" => $collectors, "collectors" => $debtors], 200);
        // $mergedResult = $collectors->merge($debtors);
        // return $mergedResult;

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(["users" => User::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
