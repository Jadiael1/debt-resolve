<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function installmentList()
    {
        return response()->json(['status' => 'success', 'message' => 'List of installments for this user', 'data' => ["installments" => auth()->user()->installments]], 200);
    }

    public function chargeList()
    {
        $collectors = auth()->user()->collectors;
        $debtors = auth()->user()->debtors;
        return response()->json(['status' => 'success', 'message' => 'Charges associated with this user', 'data' => ["debtors" => $collectors, "collectors" => $debtors]], 200);
        // $mergedResult = $collectors->merge($debtors);
        // return $mergedResult;

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Users successfully listed', 'data' => ["users" => User::all()]], 200);
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
