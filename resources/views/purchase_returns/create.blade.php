@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">{{ isset($purchaseReturn) ? 'Edit Purchase Return' : 'New Purchase Return' }}</h2>
        <p class="text-muted mb-0">{{ isset($purchaseReturn) ? 'Modify return details' : 'Create a new purchase return' }}</p>
    </div>
    <div>
        <a href="{{ route('purchase_returns.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<form action="{{ isset($purchaseReturn) ? route('purchase_returns.update', $purchaseReturn->id) : route('purchase_returns.store') }}" method="POST" id="purchaseReturnForm" class="animate__animated animate__fadeInUp">
    @csrf
    @if(isset($purchaseReturn))
        @method('PUT')
    @endif
    
    <div class="card mb-4">
        <div class="card-header">
            Basic Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select name="supplier_id" class="form-select select2" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ (isset($purchaseReturn) && $purchaseReturn->supplier_id == $supplier->id) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addSupplierModal"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <!-- Optional Purchase Link -->
                <div class="col-md-3">
                    <label class="form-label">Link Original Purchase</label>
                    <select name="purchase_id" id="purchaseSelect" class="form-select select2">
                        <option value="">Select Purchase (Optional)</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}" data-supplier="{{ $purchase->supplier_id }}">{{ $purchase->purchase_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Return #</label>
                    <input type="text" name="return_number" class="form-control" value="{{ isset($purchaseReturn) ? $purchaseReturn->return_number : $returnNumber }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Return Date <span class="text-danger">*</span></label>
                    <input type="date" name="return_date" class="form-control" value="{{ isset($purchaseReturn) ? $purchaseReturn->return_date : date('Y-m-d') }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Items</span>
            <span class="badge bg-danger">Return Items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Product</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 15%;">Price</th>
                            <th style="width: 10%;">Tax %</th>
                            <th style="width: 15%;">Tax Amt</th>
                            <th style="width: 15%;">Total</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($purchaseReturn) && $purchaseReturn->items->count() > 0)
                            @foreach($purchaseReturn->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $index }}][product_id]" class="form-select product-select select2" onchange="updateProduct(this)" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? 0 }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][product_name]" class="product-name" value="{{ $item->product_name }}">
                                </td>
                                <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control qty-input text-center" value="{{ $item->quantity }}" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                                <td><input type="number" name="items[{{ $index }}][price]" class="form-control price-input text-end" step="0.01" value="{{ $item->price }}" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                                <td><input type="number" name="items[{{ $index }}][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="{{ $item->tax_rate }}" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                                <td><input type="number" name="items[{{ $index }}][tax_amount]" class="form-control tax-amount-input text-end bg-light" value="{{ $item->tax_amount }}" readonly></td>
                                <td><input type="number" name="items[{{ $index }}][total]" class="form-control total-input text-end fw-bold bg-light" value="{{ $item->total }}" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-select product-select select2" onchange="updateProduct(this)" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? 0 }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[0][product_name]" class="product-name">
                                </td>
                                <td><input type="number" name="items[0][quantity]" class="form-control qty-input text-center" value="1" min="1" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                                <td><input type="number" name="items[0][price]" class="form-control price-input text-end" step="0.01" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                                <td><input type="number" name="items[0][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="0" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                                <td><input type="number" name="items[0][tax_amount]" class="form-control tax-amount-input text-end bg-light" step="0.01" readonly></td>
                                <td><input type="number" name="items[0][total]" class="form-control total-input text-end fw-bold bg-light" step="0.01" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>
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
                    Notes
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Notes / Reasons</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Reasons...">{{ isset($purchaseReturn) ? $purchaseReturn->notes : '' }}</textarea>
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
                        <div class="col-6 text-end text-muted">Tax Total:</div>
                        <div class="col-6 text-end fw-bold">₹ <span id="taxTotalDisplay">0.00</span></div>
                        <input type="hidden" name="tax_total" id="taxTotalInput">
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-6 text-end text-muted">Discount:</div>
                        <div class="col-6">
                            <input type="number" name="discount" id="discountInput" class="form-control form-control-sm text-end ms-auto" style="width: 120px;" value="{{ isset($purchaseReturn) ? $purchaseReturn->discount : 0 }}" step="0.01" onchange="calculateTotals()" onkeyup="calculateTotals()">
                        </div>
                    </div>
                    <div class="border-top pt-3 row">
                        <div class="col-6 text-end h5">Grand Total:</div>
                        <div class="col-6 text-end h4 text-success fw-bold">₹ <span id="grandTotalDisplay">0.00</span></div>
                        <input type="hidden" name="total" id="grandTotalInput">
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-check"></i> Save Return</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Logic similar to purchase/sales
    // ... Copy of script functions from previous file ...
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

    let rowCount = {{ isset($purchaseReturn) ? $purchaseReturn->items->count() : 1 }};

    function addRow() {
        const tbody = document.querySelector('#itemsTable tbody');
        const newRowHtml = `
            <tr class="item-row animate__animated animate__fadeIn">
                <td>
                    <div class="input-group">
                        <select name="items[${rowCount}][product_id]" class="form-select product-select select2-new-${rowCount}" onchange="updateProduct(this)" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? 0 }}">{{ $product->name }}</option>
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

    $('#purchaseSelect').on('change', function() {
        const supplierId = $(this).find(':selected').data('supplier');
        if(supplierId) {
            $('select[name="supplier_id"]').val(supplierId).trigger('change');
        }
        
        const purchaseId = $(this).val();
        if(purchaseId) {
             $.ajax({
                url: '/purchases/' + purchaseId + '/items',
                method: 'GET',
                success: function(response) {
                    if(response && response.items) {
                        populateItems(response.items);
                    }
                }
            });
        }
    });

    function populateItems(items) {
        const tbody = document.querySelector('#itemsTable tbody');
        $(tbody).empty();
        rowCount = 0; 
        
        items.forEach(item => {
            const newRowHtml = `
            <tr class="item-row animate__animated animate__fadeIn">
                <td>
                    <div class="input-group">
                         <select name="items[${rowCount}][product_id]" class="form-select product-select select2-new-${rowCount}" onchange="updateProduct(this)" required>
                            <option value="${item.product_id}" data-price="${item.price}" selected>${item.product_name}</option>
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus"></i></button>
                    </div>
                    <input type="hidden" name="items[${rowCount}][product_name]" class="product-name" value="${item.product_name}">
                </td>
                <td><input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input text-center" value="${item.quantity}" min="1" max="${item.quantity}" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][price]" class="form-control price-input text-end" step="0.01" value="${item.price}" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][tax_rate]" class="form-control tax-rate-input text-center" step="0.01" value="${item.tax_rate}" onchange="calculateRow(this)" onkeyup="calculateRow(this)"></td>
                <td><input type="number" name="items[${rowCount}][tax_amount]" class="form-control tax-amount-input text-end bg-light" value="${item.tax_amount}" readonly></td>
                <td><input type="number" name="items[${rowCount}][total]" class="form-control total-input text-end fw-bold bg-light" value="${item.total}" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>
            </tr>
            `;
            $(tbody).append(newRowHtml);
            $(`.select2-new-${rowCount}`).select2({ theme: 'bootstrap-5' });
            rowCount++;
        });
        calculateTotals();
        toastr.success("Items loaded from purchase");
    }

    $(document).ready(function() {
        calculateTotals();
        
        // Handle new supplier via Ajax
        $('#saveSupplierBtn').click(function() {
            const btn = $(this);
            const name = $('#new_supplier_name').val();
            const phone = $('#new_supplier_phone').val();
            
            if(!name) {
                toastr.error('Supplier Name is required');
                return;
            }

            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ route("suppliers.storeAjax") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    phone: phone,
                    address: $('#new_supplier_address').val()
                },
                success: function(response) {
                    if(response.success) {
                        const newOption = new Option(response.supplier.name, response.supplier.id, true, true);
                        $('select[name="supplier_id"]').append(newOption).trigger('change');
                        $('#addSupplierModal').modal('hide');
                        $('#addSupplierForm')[0].reset();
                        toastr.success('Supplier added successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to add supplier');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Supplier');
                }
            });
        });

        // Handle new product via Ajax
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
                    purchase_price: price,
                    sale_price: price, // Defaulting logic
                    opening_stock: 0
                },
                success: function(response) {
                    if(response.success) {
                        // Add new option to all product selects
                        $('.product-select').each(function() {
                            const newOption = new Option(response.product.name, response.product.id, false, false);
                            $(newOption).attr('data-price', response.product.purchase_price);
                            $(this).append(newOption);
                        });
                        
                        $('#addProductModal').modal('hide');
                        $('#addProductForm')[0].reset();
                        toastr.success('Product added successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to add product. Check required fields.');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Product');
                }
            });
        });
    });
</script>
@endpush

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm">
                    <div class="mb-3">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_supplier_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="new_supplier_phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="new_supplier_address"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSupplierBtn">Save Supplier</button>
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
                        <label class="form-label">Purchase Price</label>
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
@endsection
