@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Add New Account</h2>
        <p class="text-muted mb-0">Create a new bank account or cash account</p>
    </div>
    <div>
        <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. SBI Main Account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required id="accountType">
                                <option value="bank">Bank Account</option>
                                <option value="cash">Cash in Hand</option>
                                <option value="card">Credit/Debit Card</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="accountNumberField">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" placeholder="1234567890">
                        </div>
                        <div class="col-md-6" id="bankNameField">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. State Bank of India">
                        </div>
                        <div class="col-md-6" id="ifscField">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" placeholder="e.g. SBIN0001234">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Opening Balance <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" required value="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any additional information..."></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Create Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('accountType').addEventListener('change', function() {
    const isCash = this.value === 'cash';
    document.getElementById('accountNumberField').style.display = isCash ? 'none' : 'block';
    document.getElementById('bankNameField').style.display = isCash ? 'none' : 'block';
    document.getElementById('ifscField').style.display = isCash ? 'none' : 'block';
});
</script>
@endsection
