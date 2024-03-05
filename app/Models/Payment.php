<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';

    protected $fillable = [
        'tenant_id',
        'amount_to_be_paid',
        'amount_paid',
        'balance',
        'remarks',
    ];

    public function tenants() {
        return $this->belongsTo(Tenant::class);
    }
}
