<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receipt_number', 'customer_id', 'invoice_id', 'receipt_date',
        'amount', 'payment_mode', 'reference_no', 'bank_name',
        'received_by', 'notes'
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public static function generateReceiptNumber()
    {
        $nextId = self::withTrashed()->max('id') + 1;
        return 'RCP-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }

    public static function paymentModes()
    {
        return ['cash' => 'Cash', 'upi' => 'UPI', 'card' => 'Card', 'cheque' => 'Cheque', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'];
    }
}
