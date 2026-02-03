<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $purchaseOrders = $query->latest()->paginate(20);
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $nextId = PurchaseOrder::max('id') + 1;
        $poNumber = 'PO-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        return view('purchase_orders.create', compact('suppliers', 'products', 'poNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'po_number' => 'required|unique:purchase_orders,po_number',
            'sub_total' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $purchaseOrder = null;

        DB::transaction(function () use ($validated, $request, &$purchaseOrder) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'po_number' => $validated['po_number'],
                'po_date' => $validated['po_date'],
                'delivery_date' => $request->delivery_date,
                'sub_total' => $validated['sub_total'],
                'tax_total' => $validated['tax_total'],
                'total' => $validated['total'],
                'status' => 'draft',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $purchaseOrder->items()->create([
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
                'message' => 'Purchase Order created successfully!',
                'po_id' => $purchaseOrder->id,
            ]);
        }

        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order created successfully!');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        return view('purchase_orders.print', compact('purchaseOrder'));
    }

    public function print($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items'])->findOrFail($id);
        return view('purchase_orders.print', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        $suppliers = Supplier::all();
        $products = Product::all();
        $poNumber = $purchaseOrder->po_number;
        return view('purchase_orders.create', compact('suppliers', 'products', 'poNumber', 'purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'po_date' => $validated['po_date'],
                'delivery_date' => $request->delivery_date,
                'sub_total' => $request->sub_total,
                'tax_total' => $request->tax_total,
                'total' => $request->total,
                'status' => $request->status ?? 'draft',
                'notes' => $request->notes,
            ]);

            $purchaseOrder->items()->delete();

            foreach ($request->items as $item) {
                $purchaseOrder->items()->create([
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
                'message' => 'Purchase Order updated successfully!',
            ]);
        }

        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order updated successfully!');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order deleted successfully!');
    }
}
