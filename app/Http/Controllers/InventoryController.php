<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::where('enable_tracking', true);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('print_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }

        $lowStockProducts = (clone $query)->whereColumn('current_stock', '<=', 'low_level_limit')
            ->where('low_level_limit', '>', 0)
            ->count();
        
        $outOfStockProducts = (clone $query)->where('current_stock', '<=', 0)
            ->count();
        
        $totalProducts = (clone $query)->count();
        $totalStockValue = (clone $query)->selectRaw('SUM(current_stock * purchase_price) as total')
            ->first()
            ->total ?? 0;

        $products = $query->orderBy('current_stock', 'asc')
            ->paginate(50)
            ->withQueryString();
        
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
