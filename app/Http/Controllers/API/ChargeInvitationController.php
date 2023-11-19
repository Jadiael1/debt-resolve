<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChargeInvitation;
use Illuminate\Http\Request;

class ChargeInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Invitations listed successfully', 'data' => ['invitations' => ChargeInvitation::all()]], 200);
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
    public function show(ChargeInvitation $chargeInvitation)
    {
        return response()->json(['status' => 'success', 'message' => 'Invitations successfully found', 'data' => ['invitation' => $chargeInvitation]], 200);
    }

    public function getByEmail(string $email){
        $chargeInvitation = ChargeInvitation::where('email', $email)->get();
        return response()->json(['status' => 'success', 'message' => 'Invitations successfully found by email', 'data' => ['invitation' => $chargeInvitation]], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChargeInvitation $chargeInvitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChargeInvitation $chargeInvitation)
    {
        //
    }
}
