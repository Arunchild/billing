<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseReturn::with('supplier');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $purchaseReturns = $query->latest()->paginate(20);
        return view('purchase_returns.index', compact('purchaseReturns'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchases = Purchase::latest()->get(['id', 'purchase_number', 'supplier_id']);
        $nextId = PurchaseReturn::max('id') + 1;
        $returnNumber = 'PR-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        return view('purchase_returns.create', compact('suppliers', 'products', 'purchases', 'returnNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'return_number' => 'required|unique:purchase_returns,return_number',
            'sub_total' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $purchaseReturn = null;

        DB::transaction(function () use ($validated, $request, &$purchaseReturn) {
            $purchaseReturn = PurchaseReturn::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_id' => $request->purchase_id,
                'return_number' => $validated['return_number'],
                'return_date' => $validated['return_date'],
                'sub_total' => $validated['sub_total'],
                'tax_total' => $validated['tax_total'],
                'discount' => $request->discount ?? 0,
                'total' => $validated['total'],
                'status' => 'approved',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $purchaseReturn->items()->create([
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
                'message' => 'Purchase Return created successfully!',
                'return_id' => $purchaseReturn->id,
            ]);
        }

        return redirect()->route('purchase_returns.index')->with('success', 'Purchase Return created successfully!');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        return view('purchase_returns.print', compact('purchaseReturn'));
    }
    
    public function print($id)
    {
        $purchaseReturn = PurchaseReturn::with(['supplier', 'items'])->findOrFail($id);
        return view('purchase_returns.print', compact('purchaseReturn'));
    }

    public function edit(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load('items');
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchases = Purchase::latest()->get(['id', 'purchase_number', 'supplier_id']);
        $returnNumber = $purchaseReturn->return_number;
        return view('purchase_returns.create', compact('suppliers', 'products', 'purchases', 'returnNumber', 'purchaseReturn'));
    }

    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $purchaseReturn) {
            $purchaseReturn->update([
                'supplier_id' => $validated['supplier_id'],
                'purchase_id' => $request->purchase_id,
                'return_date' => $validated['return_date'],
                'sub_total' => $request->sub_total,
                'tax_total' => $request->tax_total,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
                'notes' => $request->notes,
            ]);

            $purchaseReturn->items()->delete();

            foreach ($request->items as $item) {
                $purchaseReturn->items()->create([
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
                'message' => 'Purchase Return updated successfully!',
            ]);
        }

        return redirect()->route('purchase_returns.index')->with('success', 'Purchase Return updated successfully!');
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->delete();
        return redirect()->route('purchase_returns.index')->with('success', 'Purchase Return deleted successfully!');
    }
}
