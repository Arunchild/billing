@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">New Quotation</h2>
        <p class="text-muted mb-0">Create a new quotation</p>
    </div>
    <div>
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<form action="{{ route('quotations.store') }}" method="POST" id="quotationForm" class="animate__animated animate__fadeInUp">
    @csrf
    
    <div class="card mb-4">
        <div class="card-header">
            Basic Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select name="customer_id" class="form-select select2" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->phone ?? 'No Phone' }})</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quotation #</label>
                    <input type="text" name="quotation_number" class="form-control" value="{{ $quotationNumber }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quotation Date <span class="text-danger">*</span></label>
                    <input type="date" name="quotation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Items</span>
            <span class="badge bg-info text-dark">Quotation</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Product / Service</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 15%;">Price (₹)</th>
                            <th style="width: 10%;">GST %</th>
                            <th style="width: 15%;">GST Amt</th>
                            <th style="width: 15%;">Total (₹)</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="item-row">
                            <td>
                                <select name="items[0][product_id]" class="form-select product-select select2" onchange="updateProduct(this)" required>
                                    <option value="">Select Product (Name / SKU)</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} {{ $product->item_code ? '('.$product->item_code.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[0][product_name]" class="product-name">
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control qty-input text-center" value="1" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                            </td>
                            <td>
                                <input type="number" name="items[0][price]" class="form-control price-input text-end" step="0.01" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                            </td>
                            <td>
                                <input type="number" name="items[0][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="0" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                            </td>
                            <td>
                                <input type="number" name="items[0][tax_amount]" class="form-control tax-amount-input text-end bg-light" step="0.01" readonly>
                            </td>
                            <td>
                                <input type="number" name="items[0][total]" class="form-control total-input text-end fw-bold bg-light" step="0.01" readonly>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 bg-light border-top">
                <button type="button" class="btn btn-primary btn-sm" onclick="addRow()">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Notes & Terms
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Notes..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-white">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 text-end text-muted">Sub Total:</div>
                        <div class="col-6 text-end fw-bold">₹ <span id="subTotalDisplay">0.00</span></div>
                        <input type="hidden" name="sub_total" id="subTotalInput">
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-end text-muted">GST Total:</div>
                        <div class="col-6 text-end fw-bold">₹ <span id="taxTotalDisplay">0.00</span></div>
                        <input type="hidden" name="tax_total" id="taxTotalInput">
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-6 text-end text-muted">Discount:</div>
                        <div class="col-6">
                            <input type="number" name="discount" id="discountInput" class="form-control form-control-sm text-end ms-auto" style="width: 120px;" value="0" step="0.01" onchange="calculateTotals()" onkeyup="calculateTotals()">
                        </div>
                    </div>
                    <div class="border-top pt-3 row">
                        <div class="col-6 text-end h5">Grand Total:</div>
                        <div class="col-6 text-end h4 text-success fw-bold">₹ <span id="grandTotalDisplay">0.00</span></div>
                        <input type="hidden" name="total" id="grandTotalInput">
                    </div>
                </div>
    <div class="card-footer bg-light text-end">
                    <button type="button" class="btn btn-info px-4 me-2 text-white" id="saveAndPrintBtn"><i class="fas fa-print"></i> Save & Print</button>
                    <button type="submit" class="btn btn-success px-4" id="saveBtn"><i class="fas fa-check"></i> Save Quotation</button>
                </div>
            </div>
        </div>
    </div>
</form>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@endsection

@push('scripts')
<script>
    function updateProduct(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const name = selectedOption.text;
        
        row.querySelector('.price-input').value = price || 0;
        row.querySelector('.product-name').value = name;
        calculateRow(select);
        
        if(price) {
            toastr.info('Product matched: ' + name);
        }
    }

    function calculateRow(element) {
        const row = element.closest('tr');
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
        
        const baseAmount = qty * price;
        const taxAmount = baseAmount * (taxRate / 100);
        const total = baseAmount + taxAmount;
        
        row.querySelector('.tax-amount-input').value = taxAmount.toFixed(2);
        row.querySelector('.total-input').value = total.toFixed(2);
        
        calculateTotals();
    }

    function calculateTotals() {
        let subTotal = 0;
        let taxTotal = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const taxAmt = parseFloat(row.querySelector('.tax-amount-input').value) || 0;
            
            subTotal += (qty * price);
            taxTotal += taxAmt;
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const grandTotal = subTotal + taxTotal - discount;

        document.getElementById('subTotalDisplay').textContent = subTotal.toFixed(2);
        document.getElementById('subTotalInput').value = subTotal.toFixed(2);
        
        document.getElementById('taxTotalDisplay').textContent = taxTotal.toFixed(2);
        document.getElementById('taxTotalInput').value = taxTotal.toFixed(2);
        
        document.getElementById('grandTotalDisplay').textContent = grandTotal.toFixed(2);
        document.getElementById('grandTotalInput').value = grandTotal.toFixed(2);
    }

    let rowCount = 1;

    function addRow() {
        const tbody = document.querySelector('#itemsTable tbody');
        
        const newRowHtml = `
            <tr class="item-row animate__animated animate__fadeIn">
                <td>
                    <select name="items[${rowCount}][product_id]" class="form-select product-select select2-new-${rowCount}" onchange="updateProduct(this)" required>
                        <option value="">Select Product (Name / SKU)</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} {{ $product->item_code ? '('.$product->item_code.')' : '' }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="items[${rowCount}][product_name]" class="product-name">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input text-center" value="1" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][price]" class="form-control price-input text-end" step="0.01" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="0" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][tax_amount]" class="form-control tax-amount-input text-end bg-light" step="0.01" readonly>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][total]" class="form-control total-input text-end fw-bold bg-light" step="0.01" readonly>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;
        
        $(tbody).append(newRowHtml);
        
        $(`.select2-new-${rowCount}`).select2({
            theme: 'bootstrap-5'
        });
        
        rowCount++;
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            $(btn).closest('tr').fadeOut(300, function() {
                $(this).remove();
                calculateTotals();
            });
        } else {
            toastr.warning("You must have at least one item.");
        }
    }

    // Function to initialize handlers, safe to call multiple times or on Turbo load
    function initQuotationForm() {
        console.log('Initializing quotation form script');
        let isSaveAndPrint = false;

        // Unbind previous handlers to avoid duplicates
        $('#saveAndPrintBtn').off('click').on('click', function() {
            isSaveAndPrint = true;
            $('#quotationForm').submit();
        });

        $('#saveBtn').off('click').on('click', function() {
            isSaveAndPrint = false;
        });

        $('#quotationForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let btn = isSaveAndPrint ? $('#saveAndPrintBtn') : $('#saveBtn');
            let originalText = btn.html();
            
            // Disable buttons to prevent double submit
            $('#saveBtn, #saveAndPrintBtn').prop('disabled', true);
            btn.html('<i class="fas fa-circle-notch fa-spin"></i> Saving...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        form[0].reset();
                        $('.select2').val(null).trigger('change');
                        $('#itemsTable tbody').empty();
                        addRow(); 
                        $('input[name="quotation_number"]').val(response.next_quotation_number);
                        $('input[name="quotation_date"]').val(new Date().toISOString().split('T')[0]);
                        calculateTotals();
                        
                        if (isSaveAndPrint) {
                            window.open("{{ url('quotations') }}/" + response.quotation_id + "/print", '_blank');
                        }
                        
                        setTimeout(() => {
                            $('select[name="customer_id"]').select2('open');
                        }, 100);
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        console.error(xhr);
                        toastr.error('An error occurred. Please check the console.');
                    }
                },
                complete: function() {
                    $('#saveBtn, #saveAndPrintBtn').prop('disabled', false);
                    btn.html(originalText);
                }
            });
        });
    }

    $(document).ready(function() {
        initQuotationForm();
    });

    // Support Turbo Drive if enabled
    document.addEventListener("turbo:load", function() {
        initQuotationForm();
    });
</script>
@endpush
