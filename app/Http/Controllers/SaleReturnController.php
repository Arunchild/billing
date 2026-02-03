<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaleReturn;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SaleReturn::with('customer');
        
        // Date Filtering
        if ($request->has('period') && $request->period != 'custom') {
            switch ($request->period) {
                case 'last_7_days':
                    $query->where('return_date', '>=', \Carbon\Carbon::now()->subDays(7)->toDateString());
                    break;
                case 'last_month':
                    $query->whereBetween('return_date', [
                        \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                        \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('return_date', [
                        \Carbon\Carbon::now()->startOfMonth()->toDateString(),
                        \Carbon\Carbon::now()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'today':
                    $query->where('return_date', \Carbon\Carbon::today()->toDateString());
                    break;
                case 'yesterday':
                    $query->where('return_date', \Carbon\Carbon::yesterday()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('return_date', [
                        \Carbon\Carbon::now()->startOfWeek()->toDateString(),
                        \Carbon\Carbon::now()->endOfWeek()->toDateString()
                    ]);
                    break;
            }
        } elseif ($request->has('from_date') && $request->has('to_date') && $request->from_date && $request->to_date) {
            $query->whereBetween('return_date', [$request->from_date, $request->to_date]);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $saleReturns = $query->latest()->paginate(20);
        return view('sale_returns.index', compact('saleReturns'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $invoices = \App\Models\Invoice::latest()->get(['id', 'invoice_number', 'customer_id']); 
        $nextId = SaleReturn::max('id') + 1;
        $returnNumber = 'SR-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        return view('sale_returns.create', compact('customers', 'products', 'invoices', 'returnNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'return_number' => 'required|unique:sale_returns,return_number',
            'return_date' => 'required|date',
            'sub_total' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $saleReturn = null;

        DB::transaction(function () use ($validated, $request, &$saleReturn) {
            $saleReturn = SaleReturn::create([
                'customer_id' => $validated['customer_id'],
                'return_number' => $validated['return_number'],
                'return_date' => $validated['return_date'],
                'sub_total' => $validated['sub_total'],
                'tax_total' => $validated['tax_total'],
                'discount' => $request->discount ?? 0,
                'total' => $validated['total'],
                'status' => 'approved', // Auto approve returns for now
                'invoice_id' => $request->invoice_id,
            ]);

            foreach ($request->items as $item) {
                $saleReturn->items()->create([
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
                'message' => 'Sale Return created successfully!',
                'sale_return_id' => $saleReturn->id,
                'next_return_number' => 'SR-' . str_pad(SaleReturn::max('id') + 1, 5, '0', STR_PAD_LEFT)
            ]);
        }

        return redirect()->route('sale_returns.index')->with('success', 'Sale Return created successfully!');
    }

    public function edit(SaleReturn $saleReturn)
    {
        $saleReturn->load('items');
        $customers = Customer::all();
        $products = Product::all();
        $invoices = \App\Models\Invoice::latest()->get(['id', 'invoice_number', 'customer_id']);
        $returnNumber = $saleReturn->return_number;
        
        return view('sale_returns.create', compact('customers', 'products', 'invoices', 'returnNumber', 'saleReturn'));
    }

    public function update(Request $request, SaleReturn $saleReturn)
    {
         $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $saleReturn) {
            $saleReturn->update([
                'customer_id' => $validated['customer_id'],
                'return_date' => $validated['return_date'],
                'sub_total' => $request->sub_total,
                'tax_total' => $request->tax_total,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
                'invoice_id' => $request->invoice_id,
            ]);

            $saleReturn->items()->delete();

            foreach ($request->items as $item) {
                $saleReturn->items()->create([
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
                'message' => 'Sale Return updated successfully!',
                'sale_return_id' => $saleReturn->id,
            ]);
        }
        
        return redirect()->route('sale_returns.index')->with('success', 'Sale Return updated successfully!');
    }

    public function destroy(SaleReturn $saleReturn)
    {
        $saleReturn->delete();
        return redirect()->route('sale_returns.index')->with('success', 'Sale Return deleted successfully!');
    }

    public function print($id)
    {
        $saleReturn = SaleReturn::with(['customer', 'items'])->findOrFail($id);
         // Reuse invoice print view or specific one. For now use specific.
        return view('sale_returns.print', compact('saleReturn'));
    }
}
