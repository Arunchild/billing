<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;

class CustomerController extends Controller
{
    /**
     * Columns the top-bar search scans. Anything text-ish on the customer is
     * fair game so a partial mobile number, a reg no or a city all work.
     */
    private const SEARCHABLE = [
        'name', 'phone', 'email', 'reg_no', 'barcode',
        'city', 'address', 'pincode', 'gst_number',
    ];

    public function index(Request $request)
    {
        $query = \App\Models\Customer::with('remarks');

        $term = trim((string) $request->input('search'));

        if ($term !== '') {
            // Every whitespace-separated word must match at least one column, so
            // "kirthesh marthandam" narrows the result instead of widening it.
            foreach (preg_split('/\s+/', $term) as $word) {
                // Escape LIKE wildcards - a stray % would otherwise match everything.
                $like = '%' . addcslashes($word, '%_\\') . '%';

                $query->where(function ($q) use ($like) {
                    foreach (self::SEARCHABLE as $column) {
                        $q->orWhere($column, 'like', $like);
                    }
                    $q->orWhereRaw('CAST(age AS CHAR) LIKE ?', [$like]);
                });
            }
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $remarks = Arr::pull($validated, 'remarks', []);

        // Auto-generate reg_no and barcode. Both columns are unique, so retry
        // if a concurrent insert claimed the same number first.
        $customer = null;

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $validated['reg_no'] = \App\Models\Customer::generateRegNo();
            $validated['barcode'] = \App\Models\Customer::generateBarcode();

            try {
                $customer = \App\Models\Customer::create($validated);
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === 5) {
                    throw $e;
                }
            }
        }

        $this->syncRemarks($customer, $remarks);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully!',
                'redirect' => route('customers.index'),
                'customer' => $customer->load('remarks')
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer created successfully with barcode.');
    }

    public function show(string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);

        $invoices = $customer->invoices()->get();
        $quotations = $customer->quotations()->get();
        $receipts = $customer->receipts()->with('invoice')->get();
        $saleReturns = $customer->saleReturns()->get();
        $remarks = $customer->remarks()->get();

        $stats = [
            'invoiced' => (float) $invoices->sum('total'),
            'received' => (float) $receipts->sum('amount'),
            'returned' => (float) $saleReturns->sum('total'),
            'quoted' => (float) $quotations->sum('total'),
        ];
        $stats['balance'] = round($stats['invoiced'] - $stats['received'], 2);

        return view('customers.show', compact('customer', 'invoices', 'quotations', 'receipts', 'saleReturns', 'remarks', 'stats'));
    }

    public function edit(string $id)
    {
        $customer = \App\Models\Customer::with('remarks')->findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);

        $validated = $request->validate($this->rules());
        $remarks = Arr::pull($validated, 'remarks', []);

        $customer->update($validated);

        // Only touch remarks when the submitting form actually manages them,
        // so other customer forms can't silently wipe the history.
        if ($request->boolean('remarks_submitted')) {
            $this->syncRemarks($customer, $remarks);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully!',
                'redirect' => route('customers.index'),
                'customer' => $customer->load('remarks')
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:M,F,Other',
            'city' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'pincode' => 'nullable|string',
            'remarks' => 'nullable|array',
            'remarks.*.id' => 'nullable|integer',
            'remarks.*.remark_date' => 'nullable|date',
            'remarks.*.purpose' => 'nullable|string|max:2000',
            'remarks.*.solution' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Create / update / remove the customer's remark rows to match what was submitted.
     */
    private function syncRemarks(\App\Models\Customer $customer, $rows): void
    {
        $keptIds = [];

        foreach ((array) $rows as $row) {
            $date = $row['remark_date'] ?? null;
            $purpose = trim((string) ($row['purpose'] ?? ''));
            $solution = trim((string) ($row['solution'] ?? ''));

            if (!$date && $purpose === '' && $solution === '') {
                continue; // ignore blank rows
            }

            $data = [
                'remark_date' => $date ?: null,
                'purpose' => $purpose ?: null,
                'solution' => $solution ?: null,
            ];

            $existing = !empty($row['id']) ? $customer->remarks()->whereKey($row['id'])->first() : null;

            if ($existing) {
                $existing->update($data);
                $keptIds[] = $existing->id;
            } else {
                $keptIds[] = $customer->remarks()->create($data)->id;
            }
        }

        $customer->remarks()->whereNotIn('customer_remarks.id', $keptIds)->delete();
    }
}
