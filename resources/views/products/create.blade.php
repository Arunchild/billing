@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Add New Product</h2>
        <p class="text-muted mb-0">Create a new product in inventory</p>
    </div>
    <div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<form action="{{ route('products.store') }}" method="POST" id="ajaxForm">
    @csrf
    <div class="row animate__animated animate__fadeInUp">
        <!-- Product Details Column -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">Product Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Group</label>
                        <select name="group" class="form-select">
                            <option value="">Select Group</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Clothing">Clothing</option>
                            <option value="Groceries">Groceries</option>
                            <option value="Hardware">Hardware</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select name="brand" class="form-select" required>
                            <option value="">Select Brand</option>
                            <option value="Generic">Generic</option>
                            <option value="Brand-A">Brand-A</option>
                            <option value="Brand-B">Brand-B</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Code</label>
                        <input type="text" name="item_code" class="form-control" placeholder="Auto-generated or custom">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter product name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Print Name</label>
                        <input type="text" name="print_name" class="form-control" placeholder="Name for invoice printing">
                    </div>
                </div>
            </div>

            <!-- Price Details -->
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">Price Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">₹</span>
                            <input type="number" step="0.01" name="purchase_price" class="form-control" required>
                            <span class="input-group-text bg-light">Excluding Tax</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sale Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">₹</span>
                            <input type="number" step="0.01" name="sale_price" class="form-control" required>
                            <span class="input-group-text bg-light">Including Tax</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Min. Sale Price</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">₹</span>
                            <input type="number" step="0.01" name="min_sale_price" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">M.R.P.</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">₹</span>
                            <input type="number" step="0.01" name="mrp" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock and Unit Details -->
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">Stock and Unit Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select" required>
                            <option value="">Select Unit</option>
                            <option value="Pcs">Pcs (Pieces)</option>
                            <option value="Kg">Kg (Kilogram)</option>
                            <option value="Ltr">Ltr (Liter)</option>
                            <option value="Box">Box</option>
                            <option value="Mtr">Mtr (Meter)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Stock</label>
                        <input type="number" step="0.01" name="opening_stock" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Stock Value</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">₹</span>
                            <input type="number" step="0.01" name="opening_stock_value" class="form-control" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GST and Other Details Column -->
        <div class="col-md-6">
            <!-- GST Details -->
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">GST Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">HSN / SAC Code</label>
                        <input type="text" name="hsn_sac_code" class="form-control" placeholder="Enter HSN/SAC code">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Rates <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small">CGST</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="cgst_rate" class="form-control" value="0">
                                    <span class="input-group-text bg-primary text-white">%</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">SGST</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="sgst_rate" class="form-control" value="0">
                                    <span class="input-group-text bg-primary text-white">%</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">IGST</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="igst_rate" class="form-control" value="0">
                                    <span class="input-group-text bg-primary text-white">%</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Cess</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="cess_rate" class="form-control" value="0">
                                    <span class="input-group-text bg-primary text-white">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Details -->
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">Other Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sale Discount</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="sale_discount" class="form-control" value="0">
                            <span class="input-group-text bg-primary text-white">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Low Level Limit</label>
                        <input type="number" name="low_level_limit" class="form-control" value="0" placeholder="Minimum stock alert">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Type</label>
                        <select name="product_type" class="form-select">
                            <option value="General">General</option>
                            <option value="Service">Service</option>
                            <option value="Raw Material">Raw Material</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location/Rack</label>
                        <select name="location_rack" class="form-select">
                            <option value="">Select Location</option>
                            <option value="Rack-A1">Rack-A1</option>
                            <option value="Rack-A2">Rack-A2</option>
                            <option value="Rack-B1">Rack-B1</option>
                            <option value="Warehouse">Warehouse</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Serial No.</label>
                        <input type="text" name="serial_no" class="form-control" placeholder="Serial/Batch number">
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">Product Description</h6>
                </div>
                <div class="card-body">
                    <textarea name="product_description" class="form-control" rows="4" maxlength="250" placeholder="Enter product description (max 250 characters)"></textarea>
                    <small class="text-muted"><span id="charCount">0</span> / 250</small>
                </div>
            </div>

            <!-- Product Settings -->
            <div class="card mb-3">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold">Product Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="print_description" value="1" class="form-check-input" id="printDesc">
                                <label class="form-check-label" for="printDesc">Print Description</label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="print_serial_no" value="1" class="form-check-input" id="printSerial">
                                <label class="form-check-label" for="printSerial">Print Serial No</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="enable_tracking" value="1" class="form-check-input" id="enableTrack" checked>
                                <label class="form-check-label" for="enableTrack">Enable Tracking</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="one_click_sale" value="1" class="form-check-input" id="oneClick">
                                <label class="form-check-label" for="oneClick">One Click Sale</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="not_for_sale" value="1" class="form-check-input" id="notForSale">
                                <label class="form-check-label" for="notForSale">Not For Sale</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mb-4">
        <button type="submit" class="btn btn-primary px-5">
            <i class="fas fa-save"></i> Save Product
        </button>
    </div>
</form>

@section('scripts')
<script>
document.querySelector('textarea[name="product_description"]').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

$(document).ready(function() {
    $('#ajaxForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let btn = form.find('button[type="submit"]');
        let originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Saving...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    form[0].reset();
                    // Reset Select2
                    $('.select2').val(null).trigger('change');
                    // Reset character count
                    document.getElementById('charCount').textContent = '0';
                    // Focus first visible input
                    form.find('input:visible:first').focus();
                }
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('An error occurred.');
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
