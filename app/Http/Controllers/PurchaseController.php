<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $purchases = $query->latest()->paginate(20);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $nextId = Purchase::max('id') + 1;
        $purchaseNumber = 'PUR-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        return view('purchases.create', compact('suppliers', 'products', 'purchaseNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'purchase_number' => 'required|unique:purchases,purchase_number',
            'sub_total' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $purchase = null;

        DB::transaction(function () use ($validated, $request, &$purchase) {
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_number' => $validated['purchase_number'],
                'purchase_date' => $validated['purchase_date'],
                'due_date' => $request->due_date,
                'sub_total' => $validated['sub_total'],
                'tax_total' => $validated['tax_total'],
                'discount' => $request->discount ?? 0,
                'total' => $validated['total'],
                'status' => 'received',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
             return response()->json([
                'success' => true,
                'message' => 'Purchase created successfully!',
                'purchase_id' => $purchase->id,
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase created successfully!');
    }

    public function show(Purchase $purchase)
    {
        return view('purchases.print', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('items');
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchaseNumber = $purchase->purchase_number;
        return view('purchases.create', compact('suppliers', 'products', 'purchaseNumber', 'purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $purchase) {
            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'due_date' => $request->due_date,
                'sub_total' => $request->sub_total,
                'tax_total' => $request->tax_total,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
                'notes' => $request->notes,
            ]);

            $purchase->items()->delete();

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
             return response()->json([
                'success' => true,
                'message' => 'Purchase updated successfully!',
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully!');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully!');
    }

    public function getItems($id)
    {
        $purchase = Purchase::with(['items', 'items.product', 'supplier'])->findOrFail($id);
        return response()->json($purchase);
    }
}
