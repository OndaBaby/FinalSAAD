<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;
    protected $table = 'parkings';

    protected $fillable = [
        'plate_number',
        'parking_start',
        'parking_end',
        'charge',
    ];
}
