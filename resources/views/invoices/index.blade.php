@extends('layouts.app')

@section('content')
<div class="header">
    <h1 class="page-title">Invoices</h1>
    <div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Invoice
        </a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                    <td>{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
                    <td>{{ number_format($invoice->total, 2) }}</td>
                    <td>
                        <span class="badge {{ $invoice->status }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <a href="#" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 2rem;">
                        <div style="color: var(--text-muted);">
                            <i class="fas fa-file-invoice" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p>No invoices found. Create your first invoice!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>

<style>
    .badge {
        padding: 0.3rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge.paid { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .badge.unpaid { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .badge.draft { background: rgba(148, 163, 184, 0.2); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }
    
    /* Pagination styling support */
    .pagination { display: flex; list-style: none; gap: 0.5rem; justify-content: center; }
    .page-link { 
        padding: 0.5rem 1rem; 
        background: var(--card-bg); 
        color: var(--text-muted); 
        text-decoration: none; 
        border-radius: 0.5rem; 
        border: 1px solid var(--glass-border);
    }
    .page-item.active .page-link { background: var(--primary); color: white; border-color: var(--primary); }
</style>
@endsection
