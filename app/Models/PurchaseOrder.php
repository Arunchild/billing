<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    // Assuming PO also has items, we might need a PurchaseOrderItem model or reuse something.
    // For now simplistic approach or if user wants items.
    // "Purchase Note" usually implies items.
    // Let's reuse 'purchase_items' table? No, that's linked to 'purchase_id'.
    // We should probably create PurchaseOrderItems if we want full detail.
    // But for now let's just use JSON or simpler relation if not specified.
    // Wait, the user said "do this carefully all functionality should be added".
    // I should create PurchaseOrderItem model.
    // But I didn't create the migration for items.
    // Let's proceed with valid 'PurchaseOrder' logic.
    // I'll skip items for PO unless strictly needed, or maybe I should add a JSON column for items?
    // Or just create the table now.
    
    // Actually, I'll create a simple items table for PO as well.
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function items() {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
