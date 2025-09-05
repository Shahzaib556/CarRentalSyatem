<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class queries extends Model
{
    use HasFactory;

    protected $table = 'tblqueries'; // your custom table name

    protected $fillable = [
        'name',
        'EmailId',
        'ContactNo',
        'message',
        'postingdate',
        'updationdate',
        'status',
    ];

    public $timestamps = false; // disable if table has no created_at / updated_at
}
