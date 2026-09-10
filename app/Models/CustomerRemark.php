<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRemark extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'remark_date', 'purpose', 'solution'
    ];

    protected $casts = [
        'remark_date' => 'date:Y-m-d',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
