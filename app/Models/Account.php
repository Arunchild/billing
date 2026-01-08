<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'type', 'account_number', 'bank_name', 'ifsc_code',
        'opening_balance', 'current_balance', 'is_active', 'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }
}
