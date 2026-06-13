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
                            <th style="width: 30%;">Product / Service</th>
                            <th style="width: 8%;">Qty</th>
                            <th style="width: 12%;">Price (₹)</th>
                            <th style="width: 10%;">GST %</th>
                            <th style="width: 12%;">Tax Type</th>
                            <th style="width: 11%;">GST Amt</th>
                            <th style="width: 12%;">Total (₹)</th>
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
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->product_description ?? $product->description ?? '' }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} {{ $product->item_code ? '('.$product->item_code.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{$index}}][product_name]" class="product-name" value="{{ $item->product_name }}">
                                    <textarea name="items[{{$index}}][item_description]" class="form-control mt-1 item-description form-control-sm" rows="2" placeholder="Custom Description (optional)">{{ $item->item_description }}</textarea>
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][quantity]" class="form-control qty-input text-center" value="{{ $item->quantity }}" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][price]" class="form-control price-input text-end" step="0.01" value="{{ $item->price }}" data-entered-price="{{ $item->price }}" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <select name="items[{{$index}}][tax_rate]" class="form-select tax-rate-input text-center" onchange="calculateRow(this)">
                                        <option value="0" {{ (int)$item->tax_rate == 0 ? 'selected' : '' }}>0%</option>
                                        <option value="5" {{ (int)$item->tax_rate == 5 ? 'selected' : '' }}>5%</option>
                                        <option value="12" {{ (int)$item->tax_rate == 12 ? 'selected' : '' }}>12%</option>
                                        <option value="18" {{ (int)$item->tax_rate == 18 ? 'selected' : '' }}>18%</option>
                                        <option value="28" {{ (int)$item->tax_rate == 28 ? 'selected' : '' }}>28%</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[{{$index}}][tax_type]" class="form-select tax-type-input text-center" onchange="calculateRow(this)">
                                        <option value="exclusive" selected>Exclusive</option>
                                        <option value="inclusive">Inclusive</option>
                                    </select>
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
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->product_description ?? $product->description ?? '' }}">{{ $product->name }} {{ $product->item_code ? '('.$product->item_code.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[0][product_name]" class="product-name">
                                    <textarea name="items[0][item_description]" class="form-control mt-1 item-description form-control-sm" rows="2" placeholder="Custom Description (optional)"></textarea>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control qty-input text-center" value="1" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="items[0][price]" class="form-control price-input text-end" step="0.01" data-entered-price="" onchange="calculateRow(this)" onkeyup="calculateRow(this)">
                                </td>
                                <td>
                                    <select name="items[0][tax_rate]" class="form-select tax-rate-input text-center" onchange="calculateRow(this)">
                                        <option value="0" selected>0%</option>
                                        <option value="5">5%</option>
                                        <option value="12">12%</option>
                                        <option value="18">18%</option>
                                        <option value="28">28%</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[0][tax_type]" class="form-select tax-type-input text-center" onchange="calculateRow(this)">
                                        <option value="exclusive" selected>Exclusive</option>
                                        <option value="inclusive">Inclusive</option>
                                    </select>
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
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable_inline {
        min-height: 80px !important;
    }
</style>
<script>
    // CKEditor Instances Map
    let editors = {};

    function initEditor(textarea) {
        if (!textarea) return;
        const id = textarea.getAttribute('id') || 'editor-' + Math.random().toString(36).substring(2, 9);
        textarea.setAttribute('id', id);
        
        // Destroy if already exists to prevent duplication
        if (editors[id]) {
            editors[id].destroy().then(() => {
                delete editors[id];
                createEditor(textarea, id);
            });
        } else {
            createEditor(textarea, id);
        }
    }

    function createEditor(textarea, id) {
        ClassicEditor
            .create(textarea, {
                toolbar: [ 'bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
            })
            .then(editor => {
                editors[id] = editor;
                editor.model.document.on('change:data', () => {
                    editor.updateSourceElement();
                });
            })
            .catch(error => {
                console.error(error);
            });
    }

    function removeEditor(textarea) {
        if (!textarea) return;
        const id = textarea.getAttribute('id');
        if (id && editors[id]) {
            editors[id].destroy().then(() => {
                delete editors[id];
            });
        }
    }

    function initAllEditors() {
        document.querySelectorAll('.item-description').forEach(textarea => {
            initEditor(textarea);
        });
    }

    function updateProduct(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const name = selectedOption.text;
        const description = selectedOption.getAttribute('data-description') || '';
        
        const priceInput = row.querySelector('.price-input');
        priceInput.value = price;
        priceInput.setAttribute('data-entered-price', price);
        row.querySelector('.product-name').value = name;
        
        const descTextarea = row.querySelector('.item-description');
        if (descTextarea) {
            if (descTextarea.id && editors[descTextarea.id]) {
                editors[descTextarea.id].setData(description);
            } else {
                descTextarea.value = description;
            }
        }
        
        calculateRow(select);
    }

    function calculateRow(element) {
        const row = element.closest('tr');
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const priceInput = row.querySelector('.price-input');
        
        // Update data-entered-price when user is actively editing the field
        if (document.activeElement === priceInput) {
            priceInput.setAttribute('data-entered-price', priceInput.value);
        }
        
        const enteredPrice = parseFloat(priceInput.getAttribute('data-entered-price')) || parseFloat(priceInput.value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
        const taxType = row.querySelector('.tax-type-input').value || 'exclusive';
        
        let taxablePrice = enteredPrice;
        let taxAmount = 0;
        let total = 0;
        
        if (taxType === 'inclusive') {
            taxablePrice = enteredPrice / (1 + taxRate / 100);
            const baseAmount = qty * taxablePrice;
            const totalAmount = qty * enteredPrice;
            taxAmount = totalAmount - baseAmount;
            total = totalAmount;
        } else {
            taxablePrice = enteredPrice;
            const baseAmount = qty * taxablePrice;
            taxAmount = baseAmount * (taxRate / 100);
            total = baseAmount + taxAmount;
        }
        
        row.querySelector('.tax-amount-input').value = taxAmount.toFixed(2);
        row.querySelector('.total-input').value = total.toFixed(2);
        
        // Update input display only if not active editing
        if (document.activeElement !== priceInput) {
            priceInput.value = taxablePrice.toFixed(2);
        }
        
        calculateTotals();
    }

    function calculateTotals() {
        let subTotal = 0;
        let taxTotal = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const priceInput = row.querySelector('.price-input');
            const enteredPrice = parseFloat(priceInput.getAttribute('data-entered-price')) || parseFloat(priceInput.value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
            const taxType = row.querySelector('.tax-type-input').value || 'exclusive';
            
            let taxablePrice = enteredPrice;
            if (taxType === 'inclusive') {
                taxablePrice = enteredPrice / (1 + taxRate / 100);
            }
            
            subTotal += (qty * taxablePrice);
            const taxAmt = parseFloat(row.querySelector('.tax-amount-input').value) || 0;
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
    
    // Setup Price Input Focus/Blur Handlers using event delegation
    $(document).on('focus', '.price-input', function() {
        const row = this.closest('tr');
        const priceInput = row.querySelector('.price-input');
        const enteredPrice = parseFloat(priceInput.getAttribute('data-entered-price')) || parseFloat(priceInput.value) || 0;
        const taxType = row.querySelector('.tax-type-input').value || 'exclusive';
        
        if (taxType === 'inclusive' && enteredPrice > 0) {
            priceInput.value = enteredPrice;
        }
    });

    $(document).on('blur', '.price-input', function() {
        const row = this.closest('tr');
        const priceInput = row.querySelector('.price-input');
        priceInput.setAttribute('data-entered-price', priceInput.value);
        calculateRow(priceInput);
    });
    
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
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price ?? $product->price ?? 0 }}" data-description="{{ $product->product_description ?? $product->description ?? '' }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus"></i></button>
                    </div>
                    <input type="hidden" name="items[${rowCount}][product_name]" class="product-name">
                    <textarea name="items[${rowCount}][item_description]" class="form-control mt-1 item-description form-control-sm" rows="2" placeholder="Custom Description (optional)"></textarea>
                </td>
                <td><input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input text-center" value="1" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][price]" class="form-control price-input text-end" step="0.01" data-entered-price="" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td>
                    <select name="items[${rowCount}][tax_rate]" class="form-select tax-rate-input text-center" onchange="calculateRow(this)">
                        <option value="0" selected>0%</option>
                        <option value="5">5%</option>
                        <option value="12">12%</option>
                        <option value="18">18%</option>
                        <option value="28">28%</option>
                    </select>
                </td>
                <td>
                    <select name="items[${rowCount}][tax_type]" class="form-select tax-type-input text-center" onchange="calculateRow(this)">
                        <option value="exclusive" selected>Exclusive</option>
                        <option value="inclusive">Inclusive</option>
                    </select>
                </td>
                <td><input type="number" name="items[${rowCount}][tax_amount]" class="form-control tax-amount-input text-end bg-light" step="0.01" readonly></td>
                <td><input type="number" name="items[${rowCount}][total]" class="form-control total-input text-end fw-bold bg-light" step="0.01" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>
            </tr>
        `;
        $(tbody).append(newRowHtml);
        $(`.select2-new-${rowCount}`).select2({ theme: 'bootstrap-5' });
        
        const newTextarea = tbody.querySelector(`textarea[name="items[${rowCount}][item_description]"]`);
        if (newTextarea) {
            initEditor(newTextarea);
        }

        rowCount++;
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            const row = $(btn).closest('tr');
            const textarea = row.find('.item-description')[0];
            removeEditor(textarea);
            row.fadeOut(300, function() {
                $(this).remove();
                calculateTotals();
            });
        } else {
            toastr.warning("At least one item required");
        }
    }

    let shouldPrintAfterSave = false;
    
    function submitAndPrint() {
        shouldPrintAfterSave = true;
        $('#invoiceForm').submit();
    }

    function initInvoiceForm() {
        calculateTotals();
        initAllEditors();

        // Handle new product (Sale Side)
        $('#saveProductBtn').off('click').on('click', function() {
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
                            $(newOption).attr('data-price', response.product.sale_price);
                            $(newOption).attr('data-description', response.product.product_description || response.product.description || '');
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
        $('#saveCustomerBtn').off('click').on('click', function() {
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

        // AJAX Form Submission
        $('#invoiceForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            let originalText = btn.html();
            
            // Ensure all price inputs are set to their calculated taxable (exclusive) prices before submit
            document.querySelectorAll('.item-row').forEach(row => {
                const priceInput = row.querySelector('.price-input');
                const enteredPrice = parseFloat(priceInput.getAttribute('data-entered-price')) || parseFloat(priceInput.value) || 0;
                const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
                const taxType = row.querySelector('.tax-type-input').value || 'exclusive';
                
                let taxablePrice = enteredPrice;
                if (taxType === 'inclusive') {
                    taxablePrice = enteredPrice / (1 + taxRate / 100);
                }
                priceInput.value = taxablePrice.toFixed(2);
            });

            // Visual feedback - fast and subtle
            btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Saving...');
            
            // Sync all CKEditor instances to their underlying textareas
            Object.values(editors).forEach(editor => {
                editor.updateSourceElement();
            });

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
                            if(form.find('input[name="_method"]').val() === 'PUT') {
                                window.location.href = "{{ route('invoices.index') }}";
                            } else {
                                // Destroy all existing editors before clearing table to prevent leaks
                                Object.values(editors).forEach(editor => {
                                    editor.destroy();
                                });
                                editors = {};

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
    }

    $(document).ready(function() {
        initInvoiceForm();
    });

    document.addEventListener('turbo:load', function() {
        initInvoiceForm();
    });
</script>
@endpush
@endsection
