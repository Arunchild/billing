<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'return_number', 'customer_id', 'return_date', 'invoice_id',
        'sub_total', 'tax_total', 'cess_total', 'discount', 'total',
        'status', 'type', 'place_of_supply', 'credit_period', 'bill_to_type',
        'contact_no', 'customer_name', 'customer_address', 'customer_gstin',
        'sold_by', 'delivery_terms', 'remarks',
        'shipping_charges', 'reference_number', 'balance_amount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}
