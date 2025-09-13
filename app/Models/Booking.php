<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'status',
        'booking_no',
        'useremail',
        'message',
        'receipt',
        'paid_amount',
        'total_days',
        'total_amount',
        'remaining_amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($booking) {
            if ($booking->start_date && $booking->end_date) {
                $booking->total_days = Carbon::parse($booking->start_date)
                    ->diffInDays(Carbon::parse($booking->end_date));
            }

            if ($booking->total_days && $booking->car_id) {
                $car = Car::find($booking->car_id);
                if ($car && $car->PricePerDay) {
                    $booking->total_amount = $booking->total_days * $car->PricePerDay;
                }
            }

            if ($booking->total_amount !== null) {
                $booking->remaining_amount = $booking->total_amount - ($booking->paid_amount ?? 0);
            }
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function car() {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
