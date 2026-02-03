<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        // Date Filtering
        if ($request->has('period') && $request->period != 'custom') {
            switch ($request->period) {
                case 'last_7_days':
                    $query->where('invoice_date', '>=', Carbon::now()->subDays(7)->toDateString());
                    break;
                case 'last_month':
                    $query->whereBetween('invoice_date', [
                        Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                        Carbon::now()->subMonth()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('invoice_date', [
                        Carbon::now()->startOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'today':
                    $query->where('invoice_date', Carbon::today()->toDateString());
                    break;
                case 'yesterday':
                    $query->where('invoice_date', Carbon::yesterday()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('invoice_date', [
                        Carbon::now()->startOfWeek()->toDateString(),
                        Carbon::now()->endOfWeek()->toDateString()
                    ]);
                    break;
            }
        } elseif ($request->has('from_date') && $request->has('to_date') && $request->from_date && $request->to_date) {
            $query->whereBetween('invoice_date', [$request->from_date, $request->to_date]);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $nextId = Invoice::max('id') + 1;
        $invoiceNumber = 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        return view('invoices.create', compact('customers', 'products', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'sub_total' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $invoice = null;

        DB::transaction(function () use ($validated, $request, &$invoice) {
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'sub_total' => $validated['sub_total'],
                'tax_total' => $validated['tax_total'],
                'discount' => $request->discount ?? 0,
                'total' => $validated['total'],
                'status' => 'paid',
                'type' => 'gst', // Assuming GST based on screenshot
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create([
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
            $nextId = Invoice::max('id') + 1;
            $nextInvoiceNumber = 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully!',
                'invoice_id' => $invoice->id,
                'next_invoice_number' => $nextInvoiceNumber
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::all();
        $products = Product::all();
        $invoiceNumber = $invoice->invoice_number;
        
        // return view('invoices.edit', compact('invoice', 'customers', 'products', 'invoiceNumber'));
        // For now, reusing create view with edit mode if needed, or better, creating a dedicated edit view is cleaner.
        // Given the request "modify all fields", I'll use the create view but populate it.
        // However, standard resource controller uses 'edit'. I will check if I can reuse create.
        // To be safe and fast, I'll pass the invoice to 'create' view and handle it there, 
        // OR create a separate edit view. Reusing is better for maintainability.
        
        return view('invoices.create', compact('customers', 'products', 'invoiceNumber', 'invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $invoice) {
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $request->due_date,
                'sub_total' => $request->sub_total,
                'tax_total' => $request->tax_total,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
            ]);

            // Sync items (delete all and recreate is simplest for this scope)
            $invoice->items()->delete();

            foreach ($request->items as $item) {
                $invoice->items()->create([
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
                'message' => 'Invoice updated successfully!',
                'invoice_id' => $invoice->id,
                'next_invoice_number' => $invoice->invoice_number
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:invoices,id'
        ]);

        Invoice::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' invoices deleted successfully!'
        ]);
    }

    public function print($id)
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        return view('invoices.print', compact('invoice'));
    }

    public function clone($id)
    {
        $original = Invoice::with('items')->findOrFail($id);
        
        $nextId = Invoice::max('id') + 1;
        $invoiceNumber = 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        DB::transaction(function () use ($original, $invoiceNumber) {
            $clone = $original->replicate();
            $clone->invoice_number = $invoiceNumber;
            $clone->invoice_date = Carbon::now();
            $clone->created_at = Carbon::now();
            $clone->updated_at = Carbon::now();
            $clone->save();

            foreach ($original->items as $item) {
                $itemClone = $item->replicate();
                $itemClone->invoice_id = $clone->id;
                $itemClone->save();
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice cloned successfully as ' . $invoiceNumber);
    }

    public function getItems($id)
    {
        $invoice = Invoice::with(['items', 'items.product', 'customer'])->findOrFail($id);
        return response()->json($invoice);
    }
}
