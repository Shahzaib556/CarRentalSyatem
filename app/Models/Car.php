<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'tblcars';

    protected $fillable = [
        'CarTitle',
        'CarBrand',
        'CarOverview',
        'PricePerDay',
        'FuelType',
        'ModelYear',
        'SeatingCapacity',
        'Image1', 'Image2', 'Image3', 'Image4', 'Image5',
        'AirConditioner', 'PowerDoorLocks', 'AntiLockBrakingSystem', 'BrakeAssist',
        'PowerSteering', 'DriverAirbag', 'PassengerAirbag', 'PowerWindows', 'CDPlayer',
        'CentralLocking', 'CrashSensor', 'LeatherSeats',
    ];

    public $timestamps = false; // Using RegDate & UpdationDate instead

    // Relation: One car belongs to one brand
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'CarBrand');
    }

    public function bookings() {
    return $this->hasMany(Booking::class);
    }

}
