<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'account_id', 'type', 'amount', 'transaction_date', 'reference_number',
        'category', 'description', 'payment_method', 'related_invoice_id', 'related_expense_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'related_invoice_id');
    }

    public function expense()
    {
        return $this->belongsTo(\App\Models\Expense::class, 'related_expense_id');
    }
}
