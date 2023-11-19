<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChargeInvitation;
use Illuminate\Http\Request;

class ChargeInvitationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/your-endpoint-here/invitations",
     *     summary="List Invitations",
     *     description="Retrieve a list of all invitations.",
     *     tags={"Invitations"},
     *     @OA\Response(
     *         response=200,
     *         description="Invitations listed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invitations listed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="invitations",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
     * @OA\Get(
     *     path="/your-endpoint-here/invitations/{chargeInvitation}",
     *     summary="Get Invitation by ID",
     *     description="Retrieve a specific invitation by its ID.",
     *     tags={"Invitations"},
     *     @OA\Parameter(
     *         name="chargeInvitation",
     *         in="path",
     *         required=true,
     *         description="ID of the invitation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation found successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invitation found successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="invitation",
     *                     type="object"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    /**
     * Display the specified resource.
     */
    public function show(int $chargeInvitation)
    {
        $chargeInvitation = \App\Models\ChargeInvitation::where('id', $chargeInvitation)->first();
        return response()->json(['status' => 'success', 'message' => 'Invitations successfully found', 'data' => ['invitation' => $chargeInvitation]], 200);
    }


    /**
     * @OA\Get(
     *     path="/your-endpoint-here/invitations/{email}",
     *     summary="Get Invitations by Email",
     *     description="Retrieve invitations by email.",
     *     tags={"Invitations"},
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Email to search for invitations",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitations found successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invitations found successfully by email"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="invitation",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getByEmail(string $email)
    {
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
