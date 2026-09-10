@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 animate__animated animate__fadeIn">
    <div>
        <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-receipt me-2"></i>Receipt {{ $receipt->receipt_number }}</h4>
        <p class="text-muted mb-0 small">View or modify this receipt</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('receipts.print', $receipt->id) }}" target="_blank" class="btn btn-outline-dark btn-sm">
            <i class="fas fa-print"></i> Print
        </a>
        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <form action="{{ route('receipts.update', $receipt->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('receipts._form')

            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                <button type="submit" form="deleteReceiptForm" class="btn btn-outline-danger"><i class="fas fa-trash-alt me-1"></i> Delete</button>
                <div>
                    <a href="{{ route('receipts.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Receipt</button>
                </div>
            </div>
        </form>

        <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST" id="deleteReceiptForm" onsubmit="return confirm('Delete this receipt?');">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
