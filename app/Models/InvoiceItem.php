<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'product_id', 'product_name', 'quantity', 'price',
        'tax_rate', 'tax_amount', 'total', 'serial_no', 'unit',
        'sale_price', 'mrp', 'discount_percentage', 'tax_percentage',
        'cess_percentage', 'item_tag', 'item_code', 'item_description',
        'net_price', 'amount_before_tax', 'discount_amount', 'cess_amount'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
