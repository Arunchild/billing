<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'phone', 'email', 'role', 'user_id', 'permissions'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getAvailableMenus()
    {
        return [
            'dashboard' => 'Dashboard',
            'invoice' => 'Invoice',
            'sale_return' => 'Sale Return',
            'quotation' => 'Quotation',
            'purchase_bill' => 'Purchase Bill',
            'purchase_return' => 'Purchase Return',
            'purchase_order' => 'Purchase Order',
            'debit_note' => 'Debit Note',
            'credit_note' => 'Credit Note',
            'supplier' => 'Supplier',
            'inventory' => 'Inventory',
            'accounts' => 'Accounts',
            'expense' => 'Expense',
            'customer' => 'Customer',
            'reports' => 'Reports',
            'staff' => 'Staff',
            'tools' => 'Tools',
            'master' => 'Master',
            'settings' => 'Settings',
        ];
    }
}
