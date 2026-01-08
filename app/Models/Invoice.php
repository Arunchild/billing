<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number', 'customer_id', 'invoice_date', 'due_date',
        'sub_total', 'tax_total', 'cess_total', 'discount', 'total',
        'status', 'type', 'place_of_supply', 'credit_period', 'bill_to_type',
        'contact_no', 'customer_name', 'customer_address', 'customer_gstin',
        'sold_by', 'delivery_terms', 'remarks',
        'payment_1_date', 'payment_1_mode', 'payment_1_txn_id', 'payment_1_amount',
        'payment_2_date', 'payment_2_mode', 'payment_2_txn_id', 'payment_2_amount',
        'shipping_charges', 'reference_number', 'balance_amount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
