@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">{{ isset($supplier) ? 'Edit Supplier' : 'Add New Supplier' }}</h2>
    </div>
    <div>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" method="POST">
            @csrf
            @if(isset($supplier))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ isset($supplier) ? $supplier->name : old('name') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ isset($supplier) ? $supplier->phone : old('phone') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ isset($supplier) ? $supplier->email : old('email') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3">{{ isset($supplier) ? $supplier->address : old('address') }}</textarea>
            </div>

           <div class="mb-3">
                <label class="form-label">GSTIN</label>
                <input type="text" name="gstin" class="form-control" value="{{ isset($supplier) ? $supplier->gstin : old('gstin') }}">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Save Supplier</button>
        </form>
    </div>
</div>
@endsection
