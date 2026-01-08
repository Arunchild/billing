@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Settings</h2>
        <p class="text-muted mb-0">Configure application preferences</p>
    </div>
    <div>
        <!-- Save button mimics form submission -->
        <button type="submit" form="settingsForm" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white py-3">
                <ul class="nav nav-pills card-header-pills" id="settingsTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#company" type="button">Company</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#app" type="button">Application</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form id="settingsForm" action="{{ route('settings.store') }}" method="POST">
                    @csrf
                    <div class="tab-content" id="settingsTabContent">
                        <!-- Company Settings -->
                        <div class="tab-pane fade show active" id="company" role="tabpanel">
                            <h6 class="text-primary mb-3">Company Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? 'My Company' }}" placeholder="e.g. Acme Corp">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">GSTIN / Tax ID</label>
                                    <input type="text" name="tax_id" class="form-control" value="{{ $settings['tax_id'] ?? '' }}" placeholder="GSTIN12345678">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="company_address" class="form-control" rows="2">{{ $settings['company_address'] ?? '' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="company_email" class="form-control" value="{{ $settings['company_email'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Settings -->
                        <div class="tab-pane fade" id="app" role="tabpanel">
                            <h6 class="text-primary mb-3">Invoice Settings</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Invoice Prefix</label>
                                    <input type="text" name="invoice_prefix" class="form-control" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" placeholder="INV-">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Default Tax Rate</label>
                                    <input type="number" name="default_tax" class="form-control" value="{{ $settings['default_tax'] ?? '0' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Terms & Conditions</label>
                                    <textarea name="invoice_terms" class="form-control" rows="3">{{ $settings['invoice_terms'] ?? 'Thank you for your business!' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
