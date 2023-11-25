<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for Users"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users/installments",
     *     security={{"bearerAuth": {}}},
     *     summary="List Installments for User",
     *     description="Retrieve a list of installments for the authenticated user",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of installments for the user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="List of installments for this user"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="installments",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         ref="#/components/schemas/Installment"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function installments()
    {
        return response()->json(['status' => 'success', 'message' => 'List of installments for this user', 'data' => ["installments" => auth()->user()->installments]], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users/charges",
     *     security={{"bearerAuth": {}}},
     *     summary="List Charges associated with User",
     *     description="Retrieve a list of charges associated with the authenticated user",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of charges for the user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Charges associated with this user"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="debtors",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         ref="#/components/schemas/User"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="collectors",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         ref="#/components/schemas/User"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function charges()
    {
        $collectors = auth()->user()->collectors;
        $debtors = auth()->user()->debtors;
        return response()->json(['status' => 'success', 'message' => 'Charges associated with this user', 'data' => ["debtors" => $collectors, "collectors" => $debtors]], 200);
        // $mergedResult = $collectors->merge($debtors);
        // return $mergedResult;

    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     security={{"bearerAuth": {}}},
     *     summary="List Users",
     *     description="Retrieve a list of all users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Users successfully listed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Users successfully listed"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="users",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         ref="#/components/schemas/User"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Users successfully listed', 'data' => ["users" => User::all()]], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users/{user_id}/user",
     *     security={{"bearerAuth": {}}},
     *     summary="Show User",
     *     description="Show details of a specific user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully listed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User successfully listed"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     ref="#/components/schemas/User"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="data")
     *         )
     *     )
     * )
     */
    public function show(User $user)
    {
        return response()->json(['status' => 'success', 'message' => 'User successfully listed', 'data' => ["user" => $user]], 200);
    }
}
