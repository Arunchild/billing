<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('customer');
        
        // Date Filtering
        if ($request->has('period') && $request->period != 'custom') {
            switch ($request->period) {
                case 'last_7_days':
                    $query->where('quotation_date', '>=', \Carbon\Carbon::now()->subDays(7)->toDateString());
                    break;
                case 'last_month':
                    $query->whereBetween('quotation_date', [
                        \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                        \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('quotation_date', [
                        \Carbon\Carbon::now()->startOfMonth()->toDateString(),
                        \Carbon\Carbon::now()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'today':
                    $query->where('quotation_date', \Carbon\Carbon::today()->toDateString());
                    break;
                case 'yesterday':
                    $query->where('quotation_date', \Carbon\Carbon::yesterday()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('quotation_date', [
                        \Carbon\Carbon::now()->startOfWeek()->toDateString(),
                        \Carbon\Carbon::now()->endOfWeek()->toDateString()
                    ]);
                    break;
            }
        } elseif ($request->has('from_date') && $request->has('to_date') && $request->from_date && $request->to_date) {
            $query->whereBetween('quotation_date', [$request->from_date, $request->to_date]);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $quotations = $query->latest()->paginate(20);
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $nextId = Quotation::max('id') + 1;
        $quotationNumber = 'QT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        return view('quotations.create', compact('customers', 'products', 'quotationNumber'));
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Quotation Store Request:', $request->all());

        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'quotation_number' => 'required|unique:quotations,quotation_number',
                'quotation_date' => 'required|date',
                'valid_until' => 'nullable|date',
                'sub_total' => 'required|numeric',
                'tax_total' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'total' => 'required|numeric',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ]);

            $quotation = null;

            DB::transaction(function () use ($validated, $request, &$quotation) {
                $quotation = Quotation::create([
                    'customer_id' => $validated['customer_id'],
                    'quotation_number' => $validated['quotation_number'],
                    'quotation_date' => $validated['quotation_date'],
                    'valid_until' => $validated['valid_until'],
                    'sub_total' => $validated['sub_total'],
                    'tax_total' => $validated['tax_total'],
                    'discount' => $request->discount ?? 0,
                    'total' => $validated['total'],
                    'status' => 'pending',
                ]);

                foreach ($request->items as $item) {
                    $productName = $item['product_name'] ?? null;
                    if (!$productName) {
                        $product = Product::find($item['product_id']);
                        $productName = $product ? $product->name : 'Unknown Product';
                    }

                    // Calculate total if missing (fail-safe)
                    $itemTotal = $item['total'] ?? (($item['quantity'] * $item['price']) + ($item['tax_amount'] ?? 0));

                    $quotation->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => $productName,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'tax_rate' => $item['tax_rate'] ?? 0,
                        'tax_amount' => $item['tax_amount'] ?? 0,
                        'total' => $itemTotal,
                    ]);
                }
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quotation created successfully!',
                    'quotation_id' => $quotation->id,
                    'next_quotation_number' => 'QT-' . str_pad(Quotation::max('id') + 1, 5, '0', STR_PAD_LEFT)
                ]);
            }

            return redirect()->route('quotations.index')->with('success', 'Quotation created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Quotation Validation Error: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Quotation Store Error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving quotation: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error saving quotation: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $customers = Customer::all();
        $products = Product::all();
        $quotationNumber = $quotation->quotation_number;
        
        return view('quotations.create', compact('customers', 'products', 'quotationNumber', 'quotation'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $quotation) {
            $quotation->update([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $request->valid_until,
                'sub_total' => $request->sub_total,
                'tax_total' => $request->tax_total,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
            ]);

            $quotation->items()->delete();

            foreach ($request->items as $item) {
                $productName = $item['product_name'] ?? null;
                if (!$productName) {
                    $product = Product::find($item['product_id']);
                    $productName = $product ? $product->name : 'Unknown Product';
                }

                $quotation->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $productName,
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
                'message' => 'Quotation updated successfully!',
                'quotation_id' => $quotation->id,
            ]);
        }

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully!');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully!');
    }

    public function print($id)
    {
        $quotation = Quotation::with(['customer', 'items'])->findOrFail($id);
        return view('quotations.print', compact('quotation'));
    }

    public function clone($id)
    {
        $original = Quotation::with('items')->findOrFail($id);
        
        $nextId = Quotation::max('id') + 1;
        $quotationNumber = 'QT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        DB::transaction(function () use ($original, $quotationNumber) {
            $clone = $original->replicate();
            $clone->quotation_number = $quotationNumber;
            $clone->quotation_date = \Carbon\Carbon::now();
            $clone->valid_until = \Carbon\Carbon::now()->addDays(30);
            $clone->save();

            foreach ($original->items as $item) {
                $itemClone = $item->replicate();
                $itemClone->quotation_id = $clone->id;
                $itemClone->save();
            }
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation cloned successfully as ' . $quotationNumber);
    }
}
