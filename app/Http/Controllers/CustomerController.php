<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Models\Customer;

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
        $this->normalizePhones($request);
        $validated = $request->validate($this->rules(), $this->messages());
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

        $this->normalizePhones($request);
        $validated = $request->validate($this->rules(), $this->messages());
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

    private const INDIAN_MOBILE = 'regex:/^[6-9][0-9]{9}$/';

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['nullable', self::INDIAN_MOBILE],
            'alternate_phone' => ['nullable', self::INDIAN_MOBILE, 'different:phone'],
            'address' => 'nullable|string|max:1000',
            'gst_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0|max:120',
            'gender' => 'nullable|in:M,F,Other',
            'weight' => 'nullable|numeric|min:1|max:300',
            'height' => 'nullable|numeric|min:30|max:250',
            'city' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'pincode' => ['nullable', 'regex:/^[1-9][0-9]{5}$/'],
            'is_diabetic' => 'nullable|boolean',
            'on_insulin' => 'nullable|boolean',
            'latex_allergy' => 'nullable|boolean',
            'medical_notes' => 'nullable|string|max:500',
            'employment_status' => ['nullable', Rule::in(array_keys(Customer::EMPLOYMENT_STATUSES))],
            'employment_details' => 'nullable|string|max:255',
            'referral_source' => ['nullable', Rule::in(array_keys(Customer::REFERRAL_SOURCES))],
            'referral_details' => 'nullable|string|max:500',
            'remarks' => 'nullable|array',
            'remarks.*.id' => 'nullable|integer',
            'remarks.*.remark_date' => 'nullable|date',
            'remarks.*.purpose' => 'nullable|string|max:2000',
            'remarks.*.solution' => 'nullable|string|max:2000',
        ];
    }

    private function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starting with 6-9).',
            'alternate_phone.regex' => 'Enter a valid 10-digit Indian mobile number (starting with 6-9).',
            'alternate_phone.different' => 'Alternate number must differ from the primary phone.',
            'pincode.regex' => 'Enter a valid 6-digit pincode.',
            'date_of_birth.before_or_equal' => 'Date of birth cannot be in the future.',
        ];
    }

    /**
     * Accept numbers typed as "+91 98765 43210", "098765-43210" etc. by
     * reducing them to the bare 10 digits before validation.
     */
    private function normalizePhones(Request $request): void
    {
        foreach (['phone', 'alternate_phone'] as $field) {
            $value = $request->input($field);
            if (!is_string($value) || trim($value) === '') {
                continue;
            }

            $digits = preg_replace('/\D/', '', $value);
            if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
                $digits = substr($digits, 2);
            } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
                $digits = substr($digits, 1);
            }

            $request->merge([$field => $digits]);
        }
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
