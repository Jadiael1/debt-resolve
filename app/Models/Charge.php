<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Charge",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the charge",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the charge",
 *         example="Monthly rent"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the charge",
 *         example="Rent payment for the month"
 *     ),
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         description="Total amount of the charge",
 *         example=500.00
 *     ),
 *     @OA\Property(
 *         property="payment_information",
 *         type="string",
 *         description="Payment Information",
 *         example="pix key: 0159487541"
 *     ),
 *     @OA\Property(
 *         property="installments_number",
 *         type="integer",
 *         description="Number of installments for the charge",
 *         example=12
 *     ),
 *     @OA\Property(
 *         property="due_day",
 *         type="integer",
 *         description="Due day for payment",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="collector_id",
 *         type="integer",
 *         description="ID of the user who is the collector for this charge",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="debtor_id",
 *         type="integer",
 *         description="ID of the user who is the debtor for this charge",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="collector",
 *         ref="#/components/schemas/User",
 *         description="Collector user information"
 *     ),
 *     @OA\Property(
 *         property="debtor",
 *         ref="#/components/schemas/User",
 *         description="Debtor user information"
 *     ),
 *     @OA\Property(
 *         property="installments",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Installment"),
 *         description="List of installments associated with this charge"
 *     ),
 * )
 */
class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'payment_information',
        'installments_number',
        'due_day',
        'collector_id',
        'debtor_id',
    ];

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function debtor()
    {
        return $this->belongsTo(User::class, 'debtor_id');
    }

    public function installments()
    {
        return $this->hasMany(Installment::class, 'charge_id');
    }
}
