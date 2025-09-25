<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Casts\Attribute;


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

    //accessor untuk hitung sisa piutang/tagihan
    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->total_amount - $this->paid_amount,
        );
    }
}
