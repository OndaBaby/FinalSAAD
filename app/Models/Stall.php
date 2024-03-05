<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stall extends Model
{
    use HasFactory;
    protected $table = 'stalls';

    protected $fillable = [
        'codename',
        'description',
        'status',
        'rental_rate',
        'leaseagreement',
        'img_path',
    ];

    public function tenant() {
        return $this->belongsToMany(Tenant::class, 'tenants_stalls');
    }
}
