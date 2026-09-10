<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Models\Receipt;
use App\Models\Customer;
use App\Models\Invoice;

class ReceiptController extends Controller implements HasMiddleware
{
    /**
     * Receipt routes are gated on the "receipt" staff permission here, mirroring
     * what CheckMenuPermission does for the other menus.
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, $next) {
                if (!$request->user() || !$request->user()->hasPermission('receipt')) {
                    abort(403, 'Unauthorized access. You do not have permission to access this menu.');
                }

                return $next($request);
            },
        ];
    }

    public function index(Request $request)
    {
        $query = Receipt::with(['customer', 'invoice']);

        // Date Filtering
        if ($request->filled('period') && $request->period != 'custom') {
            switch ($request->period) {
                case 'last_7_days':
                    $query->where('receipt_date', '>=', \Carbon\Carbon::now()->subDays(7)->toDateString());
                    break;
                case 'last_month':
                    $query->whereBetween('receipt_date', [
                        \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                        \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('receipt_date', [
                        \Carbon\Carbon::now()->startOfMonth()->toDateString(),
                        \Carbon\Carbon::now()->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'today':
                    $query->where('receipt_date', \Carbon\Carbon::today()->toDateString());
                    break;
                case 'yesterday':
                    $query->where('receipt_date', \Carbon\Carbon::yesterday()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('receipt_date', [
                        \Carbon\Carbon::now()->startOfWeek()->toDateString(),
                        \Carbon\Carbon::now()->endOfWeek()->toDateString()
                    ]);
                    break;
            }
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('receipt_date', [$request->from_date, $request->to_date]);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $totalReceived = (clone $query)->sum('amount');
        $receipts = $query->latest('receipt_date')->latest('id')->paginate(20)->withQueryString();

        return view('receipts.index', compact('receipts', 'totalReceived'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $receiptNumber = Receipt::generateReceiptNumber();
        $selectedCustomer = $request->filled('customer_id')
            ? Customer::find($request->customer_id)
            : null;
        $selectedInvoice = $request->filled('invoice_id')
            ? Invoice::find($request->invoice_id)
            : null;

        if ($selectedInvoice && !$selectedCustomer) {
            $selectedCustomer = $selectedInvoice->customer;
        }

        return view('receipts.create', compact('customers', 'receiptNumber', 'selectedCustomer', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateReceipt($request);
        $validated['receipt_number'] = Receipt::generateReceiptNumber();

        $receipt = Receipt::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Receipt created successfully!',
                'redirect' => route('receipts.print', $receipt->id),
                'receipt' => $receipt,
            ]);
        }

        if ($request->filled('save_and_print')) {
            return redirect()->route('receipts.print', $receipt->id);
        }

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt ' . $receipt->receipt_number . ' created successfully.');
    }

    public function show(string $id)
    {
        return redirect()->route('receipts.edit', $id);
    }

    public function edit(string $id)
    {
        $receipt = Receipt::findOrFail($id);
        $customers = Customer::orderBy('name')->get();

        return view('receipts.edit', compact('receipt', 'customers'));
    }

    public function update(Request $request, string $id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->update($this->validateReceipt($request));

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt ' . $receipt->receipt_number . ' updated successfully.');
    }

    public function destroy(string $id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->delete();

        return redirect()->back()->with('success', 'Receipt deleted successfully.');
    }

    public function print(string $id)
    {
        $receipt = Receipt::with(['customer', 'invoice'])->findOrFail($id);
        $customer = $receipt->customer;

        // Ledger position as of this receipt
        $totalInvoiced = $customer ? (float) $customer->invoices()->sum('total') : 0;
        $totalReceived = $customer ? (float) $customer->receipts()->sum('amount') : 0;
        $balanceDue = round($totalInvoiced - $totalReceived, 2);

        return view('receipts.print', compact('receipt', 'totalInvoiced', 'totalReceived', 'balanceDue'));
    }

    /**
     * Outstanding invoices for a customer, used by the receipt form.
     */
    public function customerInvoices(string $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $invoices = Invoice::where('customer_id', $customerId)
            ->latest('invoice_date')
            ->get(['id', 'invoice_number', 'invoice_date', 'total']);

        $receiptsByInvoice = Receipt::where('customer_id', $customerId)
            ->whereNotNull('invoice_id')
            ->selectRaw('invoice_id, SUM(amount) as paid')
            ->groupBy('invoice_id')
            ->pluck('paid', 'invoice_id');

        return response()->json([
            'summary' => [
                'invoiced' => $customer->total_invoiced,
                'received' => $customer->total_received,
                'balance' => $customer->balance_due,
            ],
            'invoices' => $invoices->map(function ($invoice) use ($receiptsByInvoice) {
                $paid = (float) ($receiptsByInvoice[$invoice->id] ?? 0);
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y'),
                    'total' => (float) $invoice->total,
                    'paid' => $paid,
                    'balance' => round((float) $invoice->total - $paid, 2),
                ];
            })->values(),
        ]);
    }

    private function validateReceipt(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:50',
            'reference_no' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);
    }
}
