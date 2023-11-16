<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function debtor()
    {
        return $this->belongsTo(User::class, 'debtor_id');
    }



}
