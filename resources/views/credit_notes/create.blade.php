@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">{{ isset($creditNote) ? 'Edit Credit Note' : 'New Credit Note' }}</h2>
    </div>
    <div>
        <a href="{{ route('credit_notes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ isset($creditNote) ? route('credit_notes.update', $creditNote->id) : route('credit_notes.store') }}" method="POST">
            @csrf
            @if(isset($creditNote))
                @method('PUT')
            @endif

            <div class="row mb-3">
                 <div class="col-md-6">
                    <label class="form-label">Note #</label>
                    <input type="text" name="note_number" class="form-control" value="{{ isset($creditNote) ? $creditNote->note_number : $noteNumber }}">
                </div>
                <div class="col-md-6">
                     <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="note_date" class="form-control" value="{{ isset($creditNote) ? $creditNote->note_date : date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                 <div class="input-group">
                    <select name="supplier_id" class="form-select select2" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ (isset($creditNote) && $creditNote->supplier_id == $supplier->id) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                 <label class="form-label">Amount <span class="text-danger">*</span></label>
                 <input type="number" name="amount" class="form-control" step="0.01" value="{{ isset($creditNote) ? $creditNote->amount : '' }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason / Notes</label>
                <textarea name="reason" class="form-control" rows="3">{{ isset($creditNote) ? $creditNote->reason : '' }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Save Note</button>
        </form>
    </div>
</div>
@endsection
