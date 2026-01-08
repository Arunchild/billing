@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Tax Rates</h2>
        <p class="text-muted mb-0">Manage GST and tax rates</p>
    </div>
    <div>
        <a href="{{ route('master.index') }}" class="btn btn-outline-secondary me-2">Back</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaxModal">
            <i class="fas fa-plus"></i> New Tax Rate
        </button>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Rate (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taxRates as $tax)
                <tr>
                    <td>{{ $tax->name }}</td>
                    <td>{{ $tax->rate }}%</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No tax rates found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $taxRates->links() }}
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createTaxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tax_rates.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Tax Rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tax Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="GST 18%">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rate (%)</label>
                        <input type="number" step="0.01" name="rate" class="form-control" required placeholder="18.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Tax Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
