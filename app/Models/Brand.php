<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'tblbrands';

    protected $fillable = [
        'BrandName',
    ];

    public $timestamps = false; // we are handling dates manually

    // Relation: One brand has many cars
    public function cars()
    {
        return $this->hasMany(Car::class, 'CarBrand');
    }
}
