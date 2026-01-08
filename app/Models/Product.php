<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'description', 'hsn_code', 'price', 'stock',
        'group', 'brand', 'item_code', 'print_name',
        'purchase_price', 'sale_price', 'min_sale_price', 'mrp',
        'unit', 'opening_stock', 'opening_stock_value', 'current_stock',
        'hsn_sac_code', 'cgst_rate', 'sgst_rate', 'igst_rate', 'cess_rate',
        'sale_discount', 'low_level_limit', 'product_type', 'location_rack',
        'serial_no', 'product_description',
        'print_description', 'print_serial_no', 'one_click_sale',
        'not_for_sale', 'enable_tracking'
    ];

    protected $casts = [
        'print_description' => 'boolean',
        'print_serial_no' => 'boolean',
        'one_click_sale' => 'boolean',
        'not_for_sale' => 'boolean',
        'enable_tracking' => 'boolean',
    ];
}
