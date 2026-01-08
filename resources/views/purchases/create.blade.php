@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">New Purchase Order</h2>
        <p class="text-muted mb-0">Create a new purchase from supplier</p>
    </div>
    <div>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('purchases.store') }}" method="POST">
                    @csrf
                    
                    <!-- Supplier Details -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" name="supplier_name" class="form-control" required placeholder="Enter supplier name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier Phone</label>
                            <input type="text" name="supplier_phone" class="form-control" placeholder="Phone number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Invoice Number</label>
                            <input type="text" name="invoice_number" class="form-control" placeholder="Supplier invoice #">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="credit">Credit</option>
                                <option value="upi">UPI</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- Purchase Items -->
                    <h6 class="text-primary mb-3">Purchase Items</h6>
                    <div id="itemsContainer">
                        <div class="row g-2 mb-2 item-row">
                            <div class="col-md-4">
                                <input type="text" name="items[0][name]" class="form-control" placeholder="Item name" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity]" class="form-control" placeholder="Qty" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" name="items[0][unit_price]" class="form-control" placeholder="Unit Price" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" name="items[0][total]" class="form-control item-total" placeholder="Total" readonly>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="addItem()"><i class="fas fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Totals -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong id="subtotal">₹ 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <input type="number" step="0.01" name="tax_amount" id="taxAmount" class="form-control form-control-sm" value="0" style="width: 100px;">
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total Amount:</strong>
                                    <strong class="text-primary" id="grandTotal">₹ 0.00</strong>
                                </div>
                                <input type="hidden" name="total_amount" id="totalAmountField">
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Save Purchase Order</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('itemsContainer');
    const newRow = `
        <div class="row g-2 mb-2 item-row">
            <div class="col-md-4">
                <input type="text" name="items[${itemCount}][name]" class="form-control" placeholder="Item name" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][quantity]" class="form-control" placeholder="Qty" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="items[${itemCount}][unit_price]" class="form-control" placeholder="Unit Price" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="items[${itemCount}][total]" class="form-control item-total" placeholder="Total" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeItem(this)"><i class="fas fa-trash"></i> Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newRow);
    itemCount++;
    attachCalculators();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    calculateTotals();
}

function attachCalculators() {
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = row.querySelector('input[name*="[quantity]"]');
        const price = row.querySelector('input[name*="[unit_price]"]');
        const total = row.querySelector('.item-total');
        
        [qty, price].forEach(input => {
            input.addEventListener('input', function() {
                const itemTotal = (parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0);
                total.value = itemTotal.toFixed(2);
                calculateTotals();
            });
        });
    });
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-total').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    const tax = parseFloat(document.getElementById('taxAmount').value) || 0;
    const grandTotal = subtotal + tax;
    
    document.getElementById('subtotal').textContent = '₹ ' + subtotal.toFixed(2);
    document.getElementById('grandTotal').textContent = '₹ ' + grandTotal.toFixed(2);
    document.getElementById('totalAmountField').value = grandTotal.toFixed(2);
}

document.getElementById('taxAmount').addEventListener('input', calculateTotals);
attachCalculators();
</script>
@endsection
