<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $table = 'queries'; // your table name

    protected $fillable = [
        'name',
        'EmailId',
        'ContactNo',
        'message',
        'status',
        'posting_date'
    ];

    public $timestamps = true; // your table has created_at and updated_at
}
