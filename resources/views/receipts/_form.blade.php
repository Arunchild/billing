@php
    $isEdit = isset($receipt);
    $currentCustomerId = old('customer_id', $isEdit ? $receipt->customer_id : ($selectedCustomer->id ?? null));
    $currentInvoiceId = old('invoice_id', $isEdit ? $receipt->invoice_id : ($selectedInvoice->id ?? null));
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Receipt No.</label>
        <input type="text" class="form-control bg-light" value="{{ $isEdit ? $receipt->receipt_number : $receiptNumber }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Receipt Date <span class="text-danger">*</span></label>
        <input type="date" name="receipt_date" class="form-control" required
               value="{{ old('receipt_date', $isEdit ? \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d') : date('Y-m-d')) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Customer <span class="text-danger">*</span></label>
        <select name="customer_id" id="customerSelect" class="form-select select2" required>
            <option value="">Select Customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ (string) $currentCustomerId === (string) $customer->id ? 'selected' : '' }}>
                    {{ $customer->name }}@if($customer->phone) — {{ $customer->phone }}@endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <div class="row g-2" id="ledgerSummary" style="display: none;">
            <div class="col-md-4">
                <div class="border rounded p-2 bg-light d-flex justify-content-between">
                    <span class="text-muted small text-uppercase">Total Billed</span>
                    <span class="fw-bold" id="sumInvoiced">₹ 0.00</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-2 bg-light d-flex justify-content-between">
                    <span class="text-muted small text-uppercase">Total Received</span>
                    <span class="fw-bold text-success" id="sumReceived">₹ 0.00</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-2 bg-light d-flex justify-content-between">
                    <span class="text-muted small text-uppercase">Balance Due</span>
                    <span class="fw-bold text-danger" id="sumBalance">₹ 0.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Against Invoice (optional)</label>
        <select name="invoice_id" id="invoiceSelect" class="form-select">
            <option value="">On Account (no specific invoice)</option>
        </select>
        <div class="form-text">Pick an invoice to auto-fill its pending balance as the amount.</div>
    </div>
    <div class="col-md-3">
        <label class="form-label">Amount Received <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₹</span>
            <input type="number" step="0.01" min="0.01" name="amount" id="amountInput" class="form-control fw-bold" required
                   value="{{ old('amount', $isEdit ? $receipt->amount : '') }}">
        </div>
    </div>
    <div class="col-md-3">
        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
        <select name="payment_mode" id="paymentMode" class="form-select" required>
            @foreach(\App\Models\Receipt::paymentModes() as $value => $label)
                <option value="{{ $value }}" {{ old('payment_mode', $isEdit ? $receipt->payment_mode : 'cash') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Reference / Txn No.</label>
        <input type="text" name="reference_no" class="form-control" placeholder="UPI ref, cheque no, txn id"
               value="{{ old('reference_no', $isEdit ? $receipt->reference_no : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Bank Name</label>
        <input type="text" name="bank_name" class="form-control"
               value="{{ old('bank_name', $isEdit ? $receipt->bank_name : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Received By</label>
        <input type="text" name="received_by" class="form-control"
               value="{{ old('received_by', $isEdit ? $receipt->received_by : auth()->user()->name) }}">
    </div>

    <div class="col-12">
        <label class="form-label">Notes / Towards</label>
        <textarea name="notes" class="form-control" rows="2" maxlength="500" placeholder="e.g. Part payment towards outstanding dues">{{ old('notes', $isEdit ? $receipt->notes : '') }}</textarea>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const invoicesUrlTemplate = "{{ route('receipts.customer_invoices', ['customer' => '__ID__']) }}";
    const preselectedInvoiceId = "{{ $currentInvoiceId }}";
    let firstLoad = true;

    function money(value) {
        return '₹ ' + Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function loadCustomerData() {
        const customerId = $('#customerSelect').val();
        const $invoiceSelect = $('#invoiceSelect');

        $invoiceSelect.html('<option value="">On Account (no specific invoice)</option>');

        if (!customerId) {
            $('#ledgerSummary').hide();
            firstLoad = false;
            return;
        }

        $.getJSON(invoicesUrlTemplate.replace('__ID__', customerId), function(data) {
            $('#sumInvoiced').text(money(data.summary.invoiced));
            $('#sumReceived').text(money(data.summary.received));
            $('#sumBalance').text(money(data.summary.balance));
            $('#ledgerSummary').show();

            data.invoices.forEach(function(invoice) {
                const label = invoice.invoice_number + ' | ' + invoice.invoice_date +
                    ' | Total ' + money(invoice.total) + ' | Pending ' + money(invoice.balance);
                $invoiceSelect.append(
                    $('<option>').val(invoice.id).text(label).attr('data-balance', invoice.balance)
                );
            });

            if (firstLoad && preselectedInvoiceId) {
                $invoiceSelect.val(preselectedInvoiceId);
            }
            firstLoad = false;
        });
    }

    $(document).on('change', '#customerSelect', loadCustomerData);

    $(document).on('change', '#invoiceSelect', function() {
        const balance = $(this).find('option:selected').data('balance');
        if (balance !== undefined && balance !== null && Number(balance) > 0) {
            $('#amountInput').val(Number(balance).toFixed(2));
        }
    });

    $(function() {
        if ($('#customerSelect').val()) {
            loadCustomerData();
        }
    });
})();
</script>
@endpush
