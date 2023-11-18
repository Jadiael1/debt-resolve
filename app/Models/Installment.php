<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
