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
        return response()->json(["invitations" => ChargeInvitation::all()]);
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
        return response()->json(["invitation" => $chargeInvitation]);
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
