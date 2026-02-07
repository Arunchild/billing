@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">{{ isset($invoice) ? 'Edit Invoice' : 'New Invoice' }}</h2>
        <p class="text-muted mb-0">{{ isset($invoice) ? 'Modify invoice details' : 'Create a new invoice for your customer' }}</p>
    </div>
    <div>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<form action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}" method="POST" id="invoiceForm" class="animate__animated animate__fadeInUp">
    @csrf
    @if(isset($invoice))
        @method('PUT')
    @endif
    
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
                                <option value="{{ $customer->id }}" {{ (isset($invoice) && $invoice->customer_id == $customer->id) || request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->phone ?? 'No Phone' }})</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Invoice #</label>
                    <input type="text" name="invoice_number" class="form-control" value="{{ isset($invoice) ? $invoice->invoice_number : $invoiceNumber }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                    <input type="date" name="invoice_date" class="form-control" value="{{ isset($invoice) ? $invoice->invoice_date : date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ isset($invoice) ? $invoice->due_date : '' }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Items</span>
            <span class="badge bg-primary">GST Invoice</span>
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
                        @if(isset($invoice) && $invoice->items->count() > 0)
                            @foreach($invoice->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{$index}}][product_id]" class="form-select product-select select2" onchange="updateProduct(this)" required>
                                        <option value="">Select Product (Name / SKU)</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} {{ $product->item_code ? '('.$product->item_code.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{$index}}][product_name]" class="product-name" value="{{ $item->product_name }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][quantity]" class="form-control qty-input text-center" value="{{ $item->quantity }}" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][price]" class="form-control price-input text-end" step="0.01" value="{{ $item->price }}" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="{{ $item->tax_rate }}" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][tax_amount]" class="form-control tax-amount-input text-end bg-light" step="0.01" value="{{ $item->tax_amount }}" readonly>
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][total]" class="form-control total-input text-end fw-bold bg-light" step="0.01" value="{{ $item->total }}" readonly>
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @else
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
                        @endif
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
                        <textarea class="form-control" rows="2" placeholder="Thank you for your business!">{{ isset($invoice) ? $invoice->remarks : '' }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Terms & Conditions</label>
                        <textarea class="form-control" rows="2" placeholder="Payment due within 15 days.">{{ isset($invoice) ? $invoice->delivery_terms : '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-white">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 text-end text-muted">Sub Total:</div>
                        <div class="col-6 text-end fw-bold">₹ <span id="subTotalDisplay">{{ isset($invoice) ? $invoice->sub_total : '0.00' }}</span></div>
                        <input type="hidden" name="sub_total" id="subTotalInput" value="{{ isset($invoice) ? $invoice->sub_total : '0.00' }}">
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-end text-muted">GST Total:</div>
                        <div class="col-6 text-end fw-bold">₹ <span id="taxTotalDisplay">{{ isset($invoice) ? $invoice->tax_total : '0.00' }}</span></div>
                        <input type="hidden" name="tax_total" id="taxTotalInput" value="{{ isset($invoice) ? $invoice->tax_total : '0.00' }}">
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-6 text-end text-muted">Discount:</div>
                        <div class="col-6">
                            <input type="number" name="discount" id="discountInput" class="form-control form-control-sm text-end ms-auto" style="width: 120px;" value="{{ isset($invoice) ? $invoice->discount : '0' }}" step="0.01" onchange="calculateTotals()" onkeyup="calculateTotals()">
                        </div>
                    </div>
                    <div class="border-top pt-3 row">
                        <div class="col-6 text-end h5">Grand Total:</div>
                        <div class="col-6 text-end h4 text-success fw-bold">₹ <span id="grandTotalDisplay">{{ isset($invoice) ? $invoice->total : '0.00' }}</span></div>
                        <input type="hidden" name="total" id="grandTotalInput" value="{{ isset($invoice) ? $invoice->total : '0.00' }}">
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <button type="button" class="btn btn-outline-secondary me-2">Save as Draft</button>
                    <button type="button" class="btn btn-info px-4 text-white me-2" onclick="submitAndPrint()"><i class="fas fa-print"></i> Save & Print</button>
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-check"></i> {{ isset($invoice) ? 'Update Invoice' : 'Save Invoice' }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Invoice Logic 
    function updateProduct(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const name = selectedOption.text;
        
        row.querySelector('.price-input').value = price || 0;
        row.querySelector('.product-name').value = name;
        calculateRow(select);
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
    
    let rowCount = {{ isset($invoice) ? $invoice->items->count() : 1 }};

    function addRow() {
        const tbody = document.querySelector('#itemsTable tbody');
        const newRowHtml = `
            <tr class="item-row animate__animated animate__fadeIn">
                <td>
                    <div class="input-group">
                        <select name="items[${rowCount}][product_id]" class="form-select product-select select2-new-${rowCount}" onchange="updateProduct(this)" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price ?? $product->price ?? 0 }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus"></i></button>
                    </div>
                    <input type="hidden" name="items[${rowCount}][product_name]" class="product-name">
                </td>
                <td><input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input text-center" value="1" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][price]" class="form-control price-input text-end" step="0.01" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="0" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][tax_amount]" class="form-control tax-amount-input text-end bg-light" step="0.01" readonly></td>
                <td><input type="number" name="items[${rowCount}][total]" class="form-control total-input text-end fw-bold bg-light" step="0.01" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>
            </tr>
        `;
        $(tbody).append(newRowHtml);
        $(`.select2-new-${rowCount}`).select2({ theme: 'bootstrap-5' });
        rowCount++;
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            $(btn).closest('tr').fadeOut(300, function() {
                $(this).remove();
                calculateTotals();
            });
        } else {
            toastr.warning("At least one item required");
        }
    }

    $(document).ready(function() {
        calculateTotals();
        
        // Handle new product (Sale Side) - using specific sale route logic or parameter if needed, but products.store is unified.
        // We just need to make sure we set correct price.
        $('#saveProductBtn').click(function() {
            const btn = $(this);
            const name = $('#new_product_name').val();
            const price = $('#new_product_price').val();
            
            if(!name) {
                toastr.error('Product Name is required');
                return;
            }

            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ route("products.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    brand: 'Generic',
                    unit: 'Piece',
                    purchase_price: 0,
                    sale_price: price, 
                    opening_stock: 0
                },
                success: function(response) {
                    if(response.success) {
                        $('.product-select').each(function() {
                            const newOption = new Option(response.product.name, response.product.id, false, false);
                            $(newOption).attr('data-price', response.product.sale_price); // Sale Price for Invoice
                            $(this).append(newOption);
                        });
                        
                        $('#addProductModal').modal('hide');
                        $('#addProductForm')[0].reset();
                        toastr.success('Product added successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to add product');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Product');
                }
            });
        });

         // Handle new Customer
        $('#saveCustomerBtn').click(function() {
            const btn = $(this);
            const name = $('#new_cust_name').val();
            const phone = $('#new_cust_phone').val();
            
            if(!name) {
                toastr.error('Customer Name is required');
                return;
            }

            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ route("customers.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    phone: phone,
                    address: $('#new_cust_address').val()
                },
                success: function(response) {
                    if(response.success) {
                        const newOption = new Option(response.customer.name, response.customer.id, true, true);
                        $('select[name="customer_id"]').append(newOption).trigger('change');
                        $('#addCustomerModal').modal('hide');
                        $('#addCustomerForm')[0].reset();
                        toastr.success('Customer added successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to add customer');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Customer');
                }
            });
        });
    });
</script>
@endpush

<!-- Add/Edit Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_cust_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="new_cust_phone">
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="new_cust_address"></textarea>
                    </div>
                </form>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_product_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sale Price</label>
                        <input type="number" class="form-control" id="new_product_price" step="0.01">
                    </div>
                     <div class="alert alert-info small">
                        * Creates a basic product. For more details go to Inventory > Products.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveProductBtn">Save Product</button>
            </div>
        </div>
    </div>
</div>


<script>
    let shouldPrintAfterSave = false;
    
    function submitAndPrint() {
        shouldPrintAfterSave = true;
        $('#invoiceForm').submit();
    }

    $(document).ready(function() {
        // AJAX Form Submission
        $('#invoiceForm').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            let originalText = btn.html();
            
            // Visual feedback - fast and subtle
            btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Saving...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.find('input[name="_method"]').length > 0 ? form.find('input[name="_method"]').val() : 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        
                        // Handle Print
                        if(shouldPrintAfterSave && response.invoice_id) {
                            window.open('/invoices/' + response.invoice_id + '/print', '_blank');
                            shouldPrintAfterSave = false; // Reset flag
                        }

                        if(response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            // If create mode, reset. If edit, maybe redirect to index?
                            // Default behavior for now: if edit, staying on page is fine but usually we go back.
                            // But for AJAX 'create', we reset.
                            
                            // Check if we are in edit mode based on URL or method
                            // Simplest: check if we have method PUT
                            if(form.find('input[name="_method"]').val() === 'PUT') {
                                // In edit mode, usually best to redirect or reload
                                window.location.href = "{{ route('invoices.index') }}";
                            } else {
                                // Reset the form completely
                                form[0].reset();
                                $('.select2').val(null).trigger('change');
                                $('#itemsTable tbody').empty();
                                rowCount = 1;
                                addRow(); 
                                $('input[name="invoice_number"]').val(response.next_invoice_number);
                                $('input[name="invoice_date"]').val(new Date().toISOString().split('T')[0]);
                                calculateTotals();
                                setTimeout(() => {
                                    $('select[name="customer_id"]').select2('open');
                                }, 100);
                            }
                        }
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
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endsection
