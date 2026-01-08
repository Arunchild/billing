<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'gst_number',
        'reg_no', 'age', 'gender', 'city', 'barcode',
        'date_of_birth', 'pincode'
    ];

    public static function generateRegNo()
    {
        $lastCustomer = self::orderBy('id', 'desc')->first();
        $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;
        return str_pad($nextId, 6, '0', STR_PAD_LEFT) . '/' . date('Y');
    }

    public static function generateBarcode()
    {
        return '82700' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
