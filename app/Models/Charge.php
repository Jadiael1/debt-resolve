<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
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
