<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="ChargeInvitation",
 *     title="Charge Invitation",
 *     description="Charge Invitation model",
 *     @OA\Property(property="id", type="integer", description="Charge Invitation ID"),
 *     @OA\Property(property="email", type="string", description="Email of the invitee"),
 *     @OA\Property(property="token", type="string", description="Invitation token"),
 *     @OA\Property(property="charge_id", type="integer", description="ID of the associated charge"),
 *     @OA\Property(property="user_id", type="integer", description="ID of the inviting user"),
 *     @OA\Property(property="is_valid", type="boolean", description="Indicates whether the invitation is valid")
 * )
 */
class ChargeInvitation extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'token',
        'charge_id',
        'user_id',
        'is_valid'
    ];
}
