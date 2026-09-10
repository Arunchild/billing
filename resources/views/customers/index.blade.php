@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <div class="row align-items-center">
            <div class="col-md-6">
                 <h4 class="mb-0 text-primary"><i class="fas fa-users me-2"></i> Customers</h4>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Add New Customer
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Barcode</th>
                        <th>City</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td><span class="badge bg-primary">{{ $customer->reg_no }}</span></td>
                        <td>
                            <a href="{{ route('customers.show', $customer->id) }}" class="fw-medium text-decoration-none" title="View invoices, quotations & receipts">{{ $customer->name }}</a>
                            @if($customer->age || $customer->gender)
                                <small class="text-muted">{{ $customer->age }}Y {{ $customer->gender }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>
                            @if($customer->barcode)
                                <span class="badge bg-success">{{ $customer->barcode }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $customer->city ?? '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-outline-dark" title="View History">
                                <i class="fas fa-folder-open"></i>
                            </a>
                            @if($customer->barcode)
                                <a href="{{ route('barcode.label', $customer->id) }}" class="btn btn-sm btn-outline-success" title="Print Barcode" target="_blank">
                                    <i class="fas fa-barcode"></i>
                                </a>
                            @endif
                            <button class="btn btn-sm btn-outline-primary" onclick="openEditModal({{ $customer }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No customers found. Add your first customer!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $customers->links() }}
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalTitle">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm" method="POST">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="custName" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="custPhone" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="custEmail" class="form-control">
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_number" id="custGst" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="custDob" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" id="custAge" class="form-control">
                        </div>
                         <div class="col-md-3 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="custGender" class="form-select">
                                <option value="">Select</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="custAddress" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" id="custCity" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" id="custPincode" class="form-control">
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0 fw-bold text-primary"><i class="fas fa-comment-medical me-1"></i> Remarks</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRemarkRow()">
                            <i class="fas fa-plus"></i> Add Remark
                        </button>
                    </div>
                    <input type="hidden" name="remarks_submitted" value="1">
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0 remarks-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 150px;">Date</th>
                                    <th>Purpose</th>
                                    <th>Solution</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="remarksBody"></tbody>
                        </table>
                        <div class="text-muted small py-2" id="remarksEmpty">No remarks yet. Click "Add Remark" to record a visit.</div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveCustBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let remarkIndex = 0;

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function toggleRemarksEmpty() {
        $('#remarksEmpty').toggle($('#remarksBody tr').length === 0);
    }

    function addRemarkRow(remark) {
        remark = remark || {};
        const i = remarkIndex++;
        const today = new Date().toISOString().slice(0, 10);
        const date = remark.remark_date ? String(remark.remark_date).slice(0, 10) : today;

        $('#remarksBody').append(
            '<tr>' +
                '<td>' +
                    '<input type="hidden" name="remarks[' + i + '][id]" value="' + escapeHtml(remark.id || '') + '">' +
                    '<input type="date" name="remarks[' + i + '][remark_date]" class="form-control form-control-sm" value="' + escapeHtml(date) + '">' +
                '</td>' +
                '<td><textarea name="remarks[' + i + '][purpose]" class="form-control form-control-sm" rows="2" placeholder="Why they came in">' + escapeHtml(remark.purpose) + '</textarea></td>' +
                '<td><textarea name="remarks[' + i + '][solution]" class="form-control form-control-sm" rows="2" placeholder="What was done">' + escapeHtml(remark.solution) + '</textarea></td>' +
                '<td class="text-end align-top">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRemarkRow(this)" title="Remove"><i class="fas fa-times"></i></button>' +
                '</td>' +
            '</tr>'
        );
        toggleRemarksEmpty();
    }

    function removeRemarkRow(btn) {
        $(btn).closest('tr').remove();
        toggleRemarksEmpty();
    }

    function loadRemarks(remarks) {
        $('#remarksBody').empty();
        remarkIndex = 0;
        (remarks || []).forEach(function(remark) { addRemarkRow(remark); });
        toggleRemarksEmpty();
    }

    function openCreateModal() {
        $('#customerModalTitle').text('Add New Customer');
        $('#customerForm').attr('action', '{{ route('customers.store') }}');
        $('#methodField').html('');
        
        $('#custName').val('');
        $('#custPhone').val('');
        $('#custEmail').val('');
        $('#custGst').val('');
        $('#custDob').val('');
        $('#custAge').val('');
        $('#custGender').val('');
        $('#custAddress').val('');
        $('#custCity').val('');
        $('#custPincode').val('');
        loadRemarks([]);
        
        $('#customerModal').modal('show');
    }

    function openEditModal(customer) {
        $('#customerModalTitle').text('Edit Customer');
        $('#customerForm').attr('action', '/customers/' + customer.id);
        $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
        
        $('#custName').val(customer.name);
        $('#custPhone').val(customer.phone);
        $('#custEmail').val(customer.email);
        $('#custGst').val(customer.gst_number);
        $('#custDob').val(customer.date_of_birth);
        $('#custAge').val(customer.age);
        $('#custGender').val(customer.gender);
        $('#custAddress').val(customer.address);
        $('#custCity').val(customer.city);
        $('#custPincode').val(customer.pincode);
        loadRemarks(customer.remarks);
        
        $('#customerModal').modal('show');
    }

    $('#customerForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#saveCustBtn');
        const form = $(this);
        
        btn.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.success || response.redirect) {
                    toastr.success('Success');
                    setTimeout(() => location.reload(), 500); 
                } else {
                     location.reload();
                }
            },
            error: function(xhr) {
                toastr.error('Error saving customer. Check inputs.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Save Changes');
            }
        });
    });
</script>
@endpush
@endsection
