<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class Spk extends Model
{
    use HasFactory;

    protected $guarded = []; // Atau tentukan kolom yang bisa diisi massal

    public function spkSizes()
    {
        return $this->hasMany(SpkSize::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
