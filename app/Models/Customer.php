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
        $suffix = '/' . date('Y');

        // Zero-padded sequence means string ordering matches numeric ordering.
        // withTrashed() matters: soft-deleted rows still hold the unique reg_no.
        $lastRegNo = self::withTrashed()
            ->where('reg_no', 'like', '%' . $suffix)
            ->orderBy('reg_no', 'desc')
            ->value('reg_no');

        $next = $lastRegNo ? ((int) explode('/', $lastRegNo)[0]) + 1 : 1;

        do {
            $regNo = str_pad($next, 6, '0', STR_PAD_LEFT) . $suffix;
            $next++;
        } while (self::withTrashed()->where('reg_no', $regNo)->exists());

        return $regNo;
    }

    public static function generateBarcode()
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $barcode = '82700' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);

            if (!self::withTrashed()->where('barcode', $barcode)->exists()) {
                return $barcode;
            }
        }

        // Pool exhausted or heavy contention: fall back to a wider unique value.
        do {
            $barcode = '82700' . random_int(100000, 999999);
        } while (self::withTrashed()->where('barcode', $barcode)->exists());

        return $barcode;
    }
}
