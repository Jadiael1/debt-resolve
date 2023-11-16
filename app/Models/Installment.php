<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;
    protected $fillable = [
        'value', 'installment_number', 'due_date', 'paid'
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}
