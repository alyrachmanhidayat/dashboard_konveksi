<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Invoice extends Model
{
    protected $guarded = [];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
