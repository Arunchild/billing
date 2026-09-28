@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Edit Customer</h2>
        <p class="text-muted mb-0">Update customer information</p>
    </div>
    <div>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Registration No</label>
                            <input type="text" class="form-control" value="{{ $customer->reg_no }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barcode</label>
                            <input type="text" class="form-control" value="{{ $customer->barcode }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Title</label>
                            @include('customers._title_select', ['selected' => old('title', $customer->title)])
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required maxlength="255">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" value="{{ old('age', $customer->age) }}" min="0" max="120">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-</option>
                                <option value="M" {{ old('gender', $customer->gender) == 'M' ? 'selected' : '' }}>Male</option>
                                <option value="F" {{ old('gender', $customer->gender) == 'F' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $customer->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" title="10-digit Indian mobile number starting with 6-9">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $customer->pincode) }}" inputmode="numeric" maxlength="6" pattern="[1-9][0-9]{5}" title="6-digit pincode">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" maxlength="1000">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $customer->gst_number) }}" maxlength="20">
                        </div>
                        <div class="col-12">
                            @include('customers._registration_fields', ['customer' => $customer])
                        </div>
                        <div class="col-12">
                            @include('customers._remarks', ['remarks' => $customer->remarks])
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Update Customer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
