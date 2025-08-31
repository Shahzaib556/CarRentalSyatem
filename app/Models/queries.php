<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class queries extends Model
{
    use HasFactory;

    protected $table = 'tblcontactusinfo'; // custom table name

    protected $fillable = [
        'Address',
        'EmailId',
        'ContactNo',
    ];
}
