<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::where('enable_tracking', true)
            ->orderBy('current_stock', 'asc')
            ->paginate(50);
        
        $lowStockProducts = \App\Models\Product::whereColumn('current_stock', '<=', 'low_level_limit')
            ->where('low_level_limit', '>', 0)
            ->count();
        
        $outOfStockProducts = \App\Models\Product::where('current_stock', '<=', 0)
            ->where('enable_tracking', true)
            ->count();
        
        $totalProducts = \App\Models\Product::where('enable_tracking', true)->count();
        $totalStockValue = \App\Models\Product::selectRaw('SUM(current_stock * purchase_price) as total')
            ->where('enable_tracking', true)
            ->first()
            ->total ?? 0;
        
        return view('inventory.index', compact('products', 'lowStockProducts', 'outOfStockProducts', 'totalProducts', 'totalStockValue'));
    }

    public function adjust(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        switch ($request->adjustment_type) {
            case 'add':
                $product->current_stock += $request->quantity;
                break;
            case 'subtract':
                $product->current_stock -= $request->quantity;
                break;
            case 'set':
                $product->current_stock = $request->quantity;
                break;
        }

        $product->save();

        return redirect()->route('inventory.index')->with('success', 'Stock adjusted successfully.');
    }
}
