<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Installment",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the installment",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="number",
 *         description="Value of the installment",
 *         example=100.00
 *     ),
 *     @OA\Property(
 *         property="installment_number",
 *         type="integer",
 *         description="Number of the installment",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         description="Due date for payment",
 *         example="2023-11-30"
 *     ),
 *     @OA\Property(
 *         property="paid",
 *         type="boolean",
 *         description="Indicates if the installment is paid or not",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user associated with the installment",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="awaiting_approval",
 *         type="boolean",
 *         description="Indicates if the installment is awaiting approval",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="payment_proof",
 *         type="string",
 *         description="URL or path to the payment proof file",
 *         example="https://example.com/payment-proof.jpg"
 *     ),
 *     @OA\Property(
 *         property="charge",
 *         ref="#/components/schemas/Charge",
 *         description="Charge associated with this installment"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         description="User associated with this installment"
 *     ),
 * )
 */
class Installment extends Model
{
    use HasFactory;
    protected $fillable = [
        'value', 'installment_number', 'due_date', 'paid', 'user_id', 'awaiting_approval', 'payment_proof'
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
