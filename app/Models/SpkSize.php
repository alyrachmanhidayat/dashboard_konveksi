<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkSize extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }
}
