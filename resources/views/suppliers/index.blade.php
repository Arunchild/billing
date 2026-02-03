@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <div class="row align-items-center">
            <div class="col-md-6">
                 <h4 class="mb-0 text-primary"><i class="fas fa-users me-2"></i> Suppliers</h4>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Add New Supplier
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td class="fw-bold">{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone ?? '-' }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td>{{ Str::limit($supplier->address, 30) ?? '-' }}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditModal({{ $supplier }})"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No suppliers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $suppliers->links() }}
    </div>
</div>

<!-- Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalTitle">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="supplierForm" method="POST">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="mb-3">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="supplierName" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="supplierPhone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="supplierEmail" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="supplierAddress" class="form-control" rows="3"></textarea>
                    </div>

                   <div class="mb-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" name="gstin" id="supplierGstin" class="form-control">
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        $('#supplierModalTitle').text('Add New Supplier');
        $('#supplierForm').attr('action', '{{ route('suppliers.store') }}');
        $('#methodField').html('');
        
        $('#supplierName').val('');
        $('#supplierPhone').val('');
        $('#supplierEmail').val('');
        $('#supplierAddress').val('');
        $('#supplierGstin').val('');
        
        $('#supplierModal').modal('show');
    }

    function openEditModal(supplier) {
        $('#supplierModalTitle').text('Edit Supplier');
        $('#supplierForm').attr('action', '/suppliers/' + supplier.id);
        $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
        
        $('#supplierName').val(supplier.name);
        $('#supplierPhone').val(supplier.phone);
        $('#supplierEmail').val(supplier.email);
        $('#supplierAddress').val(supplier.address);
        $('#supplierGstin').val(supplier.gstin);
        
        $('#supplierModal').modal('show');
    }
</script>
@endpush
@endsection
