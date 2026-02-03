<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_number', 'customer_id', 'quotation_date', 'valid_until',
        'sub_total', 'tax_total', 'cess_total', 'discount', 'total',
        'status', 'type', 'place_of_supply', 'credit_period', 'bill_to_type',
        'contact_no', 'customer_name', 'customer_address', 'customer_gstin',
        'sold_by', 'delivery_terms', 'remarks',
        'shipping_charges', 'reference_number'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
