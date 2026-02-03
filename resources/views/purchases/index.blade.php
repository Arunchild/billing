@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('purchases.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search PO Number or Supplier..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="col-md-2 text-end ms-auto">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-plus"></i> New Purchase
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 400px; padding-bottom: 150px;">
            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-2">PO #</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Status</th>
                        <th class="py-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td class="fw-bold text-primary">{{ $purchase->purchase_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-M-Y') }}</td>
                        <td class="fw-bold">{{ $purchase->supplier->name }}</td>
                        <td class="fw-bold">{{ number_format($purchase->total, 2) }}</td>
                        <td>
                             <span class="badge {{ $purchase->status == 'received' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ strtoupper($purchase->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="{{ route('purchases.edit', $purchase->id) }}"><i class="fas fa-edit text-primary me-2"></i> View / Modify</a></li>
                                    <li><a class="dropdown-item" href="{{ route('purchases.show', $purchase->id) }}" target="_blank"><i class="fas fa-print text-secondary me-2"></i> Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-shopping-bag fa-3x mb-3 opacity-50"></i>
                                <p>No purchases found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $purchases->withQueryString()->links() }}
    </div>
</div>

<style>
    thead.bg-primary th {
        background-color: #0d6efd !important;
        color: white;
        font-weight: 500;
        border-bottom: none;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: var(--bs-primary);
    }
    .btn-icon:focus {
        box-shadow: none;
    }
    .table-responsive {
        min-height: 400px;
        padding-bottom: 150px; 
    }
    .card-body.p-0 {
        overflow: hidden;
    }
</style>
@endsection
