<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $table = 'tenants';

    protected $fillable = [
        'user_id',
        'age',
        'contact_no',
        'address',
        'leaseagreement',
        'img_path',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function stalls() {
        return $this->belongsToMany(Stall::class, 'tenants_stalls');
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }
}
