<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    // Accessor for progress percentage
    public function getProgressPercentageAttribute()
    {
        $completedSteps = 0;
        $totalSteps = 4; // design, print, press, delivery

        if ($this->is_design_done) $completedSteps++;
        if ($this->is_print_done) $completedSteps++;
        if ($this->is_press_done) $completedSteps++;
        if ($this->is_delivery_done) $completedSteps++;

        return $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
    }

    // Accessor for progress bar color based on progress
    public function getProgressBarColorAttribute()
    {
        $percentage = $this->progressPercentage;

        if ($percentage >= 75) {
            return 'bg-success'; // Green for 75%+
        } elseif ($percentage >= 50) {
            return 'bg-info'; // Blue for 50-74%
        } elseif ($percentage >= 25) {
            return 'bg-warning'; // Yellow for 25-49%
        } else {
            return 'bg-secondary'; // Gray for <25%
        }
    }

    // Accessor for background color based on delivery date
    public function getBgColorAttribute()
    {
        $deliveryDate = Carbon::parse($this->delivery_date);
        $today = Carbon::today();
        $daysUntilDelivery = $today->diffInDays($deliveryDate, false); // false to get negative if past

        // Define color based on specific deadline ranges:
        // Within 8 days (including today): Red (critical)
        // Above 8 days but within 10 days: Yellow (moderate)
        // Above 10 days but within 12 days: Green (not critical)
        if ($daysUntilDelivery <= 8) {
            return 'bg-danger'; // Red for within 8 days (critical)
        } elseif ($daysUntilDelivery <= 10) {
            return 'bg-warning'; // Yellow for 9-10 days (moderate)
        } elseif ($daysUntilDelivery <= 12) {
            return 'bg-success'; // Green for 11-12 days (not critical)
        } else {
            return 'bg-primary'; // Blue for more than 12 days (normal)
        }
    }

    // Accessor for formatted delivery date
    public function getFormattedDeliveryDateAttribute()
    {
        return Carbon::parse($this->delivery_date)->format('d M Y');
    }
}
