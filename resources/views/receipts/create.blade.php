@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 animate__animated animate__fadeIn">
    <div>
        <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-receipt me-2"></i>New Receipt</h4>
        <p class="text-muted mb-0 small">Record an amount received from a customer</p>
    </div>
    <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <form action="{{ route('receipts.store') }}" method="POST">
            @csrf
            @include('receipts._form')

            <div class="text-end mt-3 pt-3 border-top">
                <a href="{{ route('receipts.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-outline-success px-3"><i class="fas fa-save me-1"></i> Save</button>
                <button type="submit" name="save_and_print" value="1" class="btn btn-success px-4"><i class="fas fa-print me-1"></i> Save &amp; Print</button>
            </div>
        </form>
    </div>
</div>
@endsection
