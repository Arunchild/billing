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

    public function invoices()
    {
        return $this->hasMany(Invoice::class)->latest('invoice_date');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class)->latest('quotation_date');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class)->latest('receipt_date');
    }

    public function remarks()
    {
        return $this->hasMany(CustomerRemark::class)->orderByDesc('remark_date')->orderByDesc('id');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class)->latest('return_date');
    }

    public function getTotalInvoicedAttribute()
    {
        return (float) $this->invoices()->sum('total');
    }

    public function getTotalReceivedAttribute()
    {
        return (float) $this->receipts()->sum('amount');
    }

    public function getBalanceDueAttribute()
    {
        return round($this->total_invoiced - $this->total_received, 2);
    }

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
