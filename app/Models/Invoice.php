<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;


class Invoice extends Model
{
    protected $guarded = [];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    //accessor untuk hitung sisa piutang/tagihan
    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->total_amount - $this->getPaidAmount(),
        );
    }

    // Method to calculate paid amount from related payments
    public function getPaidAmount(): float
    {
        // First check if paid_amount field is available in the model (from database)
        if (isset($this->attributes['paid_amount']) && !is_null($this->attributes['paid_amount'])) {
            return (float) $this->attributes['paid_amount'];
        }
        
        // Otherwise calculate from payments relationship
        if (isset($this->relations['payments'])) {
            return $this->payments->sum('amount') ?? 0;
        } else {
            return $this->payments()->sum('amount') ?? 0;
        }
    }
}
