@extends('layouts.app')

@php
    $user = auth()->user();
    $can = [
        'invoice' => $user->hasPermission('invoice'),
        'quotation' => $user->hasPermission('quotation'),
        'receipt' => $user->hasPermission('receipt'),
        'sale_return' => $user->hasPermission('sale_return'),
    ];
@endphp

@section('content')
<!-- Customer Header -->
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="cust-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $customer->name }}</h5>
                        <div class="d-flex flex-wrap gap-2 small text-muted">
                            @if($customer->reg_no)<span class="badge bg-primary">{{ $customer->reg_no }}</span>@endif
                            @if($customer->barcode)<span class="badge bg-success">{{ $customer->barcode }}</span>@endif
                            @if($customer->phone)<span><i class="fas fa-phone-alt me-1"></i>{{ $customer->phone }}</span>@endif
                            @if($customer->email)<span><i class="fas fa-envelope me-1"></i>{{ $customer->email }}</span>@endif
                            @if($customer->age || $customer->gender)<span><i class="fas fa-user me-1"></i>{{ $customer->age }}{{ $customer->age ? 'Y' : '' }} {{ $customer->gender }}</span>@endif
                        </div>
                        @if($customer->address || $customer->city || $customer->gst_number)
                        <div class="small text-muted mt-1">
                            @if($customer->address)<i class="fas fa-map-marker-alt me-1"></i>{{ $customer->address }}@endif
                            {{ $customer->city }} {{ $customer->pincode }}
                            @if($customer->gst_number)<span class="ms-2"><i class="fas fa-file-invoice me-1"></i>GSTIN: {{ $customer->gst_number }}</span>@endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                    @if($can['receipt'])
                        <a href="{{ route('receipts.create', ['customer_id' => $customer->id]) }}" class="btn btn-success btn-sm"><i class="fas fa-receipt"></i> New Receipt</a>
                    @endif
                    @if($can['invoice'])
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Invoice</a>
                    @endif
                    @if($can['quotation'])
                        <a href="{{ route('quotations.create') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-contract"></i> New Quotation</a>
                    @endif
                    @if($customer->barcode)
                        <a href="{{ route('barcode.label', $customer->id) }}" target="_blank" class="btn btn-outline-success btn-sm" title="Print Barcode"><i class="fas fa-barcode"></i></a>
                    @endif
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Summary -->
<div class="row g-2 mb-3 animate__animated animate__fadeIn">
    <div class="col-6 col-lg-3">
        <div class="card h-100 stat-card border-start-primary">
            <div class="card-body py-2 px-3">
                <div class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Total Billed</div>
                <div class="fw-bold" style="font-size: 1.05rem;">₹ {{ number_format($stats['invoiced'], 2) }}</div>
                <div class="text-muted" style="font-size: 0.7rem;">{{ $invoices->count() }} invoice(s)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100 stat-card border-start-success">
            <div class="card-body py-2 px-3">
                <div class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Total Received</div>
                <div class="fw-bold text-success" style="font-size: 1.05rem;">₹ {{ number_format($stats['received'], 2) }}</div>
                <div class="text-muted" style="font-size: 0.7rem;">{{ $receipts->count() }} receipt(s)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100 stat-card {{ $stats['balance'] > 0 ? 'border-start-danger' : 'border-start-success' }}">
            <div class="card-body py-2 px-3">
                <div class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Balance Due</div>
                <div class="fw-bold {{ $stats['balance'] > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 1.05rem;">₹ {{ number_format($stats['balance'], 2) }}</div>
                <div class="text-muted" style="font-size: 0.7rem;">{{ $stats['balance'] > 0 ? 'Outstanding' : 'Settled' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100 stat-card border-start-warning">
            <div class="card-body py-2 px-3">
                <div class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Quoted / Returned</div>
                <div class="fw-bold" style="font-size: 1.05rem;">₹ {{ number_format($stats['quoted'], 2) }} <span class="text-muted fw-normal" style="font-size: 0.8rem;">/ ₹ {{ number_format($stats['returned'], 2) }}</span></div>
                <div class="text-muted" style="font-size: 0.7rem;">{{ $quotations->count() }} quotation(s), {{ $saleReturns->count() }} return(s)</div>
            </div>
        </div>
    </div>
</div>

<!-- Remarks -->
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fw-bold"><i class="fas fa-comment-medical me-2 text-primary"></i>Remarks <span class="badge bg-secondary ms-1">{{ $remarks->count() }}</span></h6>
        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i> Add / Edit Remarks</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 doc-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Date</th>
                        <th style="width: 40%;">Purpose</th>
                        <th>Solution</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remarks as $remark)
                    <tr>
                        <td class="text-nowrap">{{ $remark->remark_date ? $remark->remark_date->format('d-M-Y') : '-' }}</td>
                        <td style="white-space: pre-line;">{{ $remark->purpose ?: '-' }}</td>
                        <td style="white-space: pre-line;">{{ $remark->solution ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="fas fa-comment-medical fa-2x d-block mb-2 opacity-25"></i>
                            No remarks recorded for this customer.
                            <a href="{{ route('customers.edit', $customer->id) }}" class="d-block mt-2">Add the first one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Documents -->
<div class="card animate__animated animate__fadeInUp">
    <div class="card-header bg-white pb-0">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            @if($can['invoice'])
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-invoices" type="button">
                    <i class="fas fa-file-alt me-1 text-primary"></i> Invoices <span class="badge bg-secondary ms-1">{{ $invoices->count() }}</span>
                </button>
            </li>
            @endif
            @if($can['quotation'])
            <li class="nav-item">
                <button class="nav-link {{ $can['invoice'] ? '' : 'active' }}" data-bs-toggle="tab" data-bs-target="#tab-quotations" type="button">
                    <i class="fas fa-file-contract me-1 text-info"></i> Quotations <span class="badge bg-secondary ms-1">{{ $quotations->count() }}</span>
                </button>
            </li>
            @endif
            @if($can['receipt'])
            <li class="nav-item">
                <button class="nav-link {{ (!$can['invoice'] && !$can['quotation']) ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-receipts" type="button">
                    <i class="fas fa-receipt me-1 text-success"></i> Receipts <span class="badge bg-secondary ms-1">{{ $receipts->count() }}</span>
                </button>
            </li>
            @endif
            @if($can['sale_return'])
            <li class="nav-item">
                <button class="nav-link {{ (!$can['invoice'] && !$can['quotation'] && !$can['receipt']) ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-returns" type="button">
                    <i class="fas fa-undo me-1 text-danger"></i> Sale Returns <span class="badge bg-secondary ms-1">{{ $saleReturns->count() }}</span>
                </button>
            </li>
            @endif
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">

            @if($can['invoice'])
            <!-- Invoices -->
            <div class="tab-pane fade show active" id="tab-invoices">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 doc-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                                <td>{{ strtoupper($invoice->type ?? 'GST') }}</td>
                                <td>
                                    <span class="badge {{ $invoice->status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ strtoupper($invoice->status) }}</span>
                                </td>
                                <td class="text-end fw-bold">₹ {{ number_format($invoice->total, 2) }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-outline-primary" title="View / Modify"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print (Bill 1)"><i class="fas fa-print"></i></a>
                                    <a href="{{ route('invoices.print2', $invoice->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print (Bill 2)"><i class="fas fa-file-invoice"></i></a>
                                    <a href="{{ route('invoices.clone', $invoice->id) }}" class="btn btn-sm btn-outline-info" title="Create Clone"><i class="fas fa-copy"></i></a>
                                    @if($can['receipt'])
                                        <a href="{{ route('receipts.create', ['customer_id' => $customer->id, 'invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-outline-success" title="Add Receipt"><i class="fas fa-receipt"></i></a>
                                    @endif
                                    <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this invoice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-file-alt fa-2x d-block mb-2 opacity-25"></i>No invoices for this customer.</td></tr>
                            @endforelse
                        </tbody>
                        @if($invoices->count())
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">₹ {{ number_format($stats['invoiced'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            @if($can['quotation'])
            <!-- Quotations -->
            <div class="tab-pane fade {{ $can['invoice'] ? '' : 'show active' }}" id="tab-quotations">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 doc-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Quotation No.</th>
                                <th>Date</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $index => $quotation)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $quotation->quotation_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d-M-Y') }}</td>
                                <td>{{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d-M-Y') : '-' }}</td>
                                <td>
                                    <span class="badge {{ $quotation->status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">{{ strtoupper($quotation->status) }}</span>
                                </td>
                                <td class="text-end fw-bold">₹ {{ number_format($quotation->total, 2) }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-outline-primary" title="View / Modify"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('quotations.print', $quotation->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print"><i class="fas fa-print"></i></a>
                                    <a href="{{ route('quotations.clone', $quotation->id) }}" class="btn btn-sm btn-outline-info" title="Create Copy"><i class="fas fa-copy"></i></a>
                                    <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this quotation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-file-contract fa-2x d-block mb-2 opacity-25"></i>No quotations for this customer.</td></tr>
                            @endforelse
                        </tbody>
                        @if($quotations->count())
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">₹ {{ number_format($stats['quoted'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            @if($can['receipt'])
            <!-- Receipts -->
            <div class="tab-pane fade {{ (!$can['invoice'] && !$can['quotation']) ? 'show active' : '' }}" id="tab-receipts">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 doc-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Receipt No.</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Reference</th>
                                <th>Against Invoice</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $index => $receipt)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $receipt->receipt_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d-M-Y') }}</td>
                                <td><span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', $receipt->payment_mode)) }}</span></td>
                                <td>{{ $receipt->reference_no ?? '-' }}</td>
                                <td>
                                    @if($receipt->invoice && $can['invoice'])
                                        <a href="{{ route('invoices.edit', $receipt->invoice_id) }}" class="text-decoration-none">{{ $receipt->invoice->invoice_number }}</a>
                                    @elseif($receipt->invoice)
                                        {{ $receipt->invoice->invoice_number }}
                                    @else
                                        <span class="text-muted">On Account</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">₹ {{ number_format($receipt->amount, 2) }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn btn-sm btn-outline-primary" title="View / Modify"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('receipts.print', $receipt->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print"><i class="fas fa-print"></i></a>
                                    <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this receipt?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-receipt fa-2x d-block mb-2 opacity-25"></i>
                                    No receipts yet.
                                    <a href="{{ route('receipts.create', ['customer_id' => $customer->id]) }}" class="d-block mt-2">Record a payment</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($receipts->count())
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total Received</td>
                                <td class="text-end fw-bold text-success">₹ {{ number_format($stats['received'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            @if($can['sale_return'])
            <!-- Sale Returns -->
            <div class="tab-pane fade {{ (!$can['invoice'] && !$can['quotation'] && !$can['receipt']) ? 'show active' : '' }}" id="tab-returns">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 doc-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Return No.</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($saleReturns as $index => $saleReturn)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $saleReturn->return_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($saleReturn->return_date)->format('d-M-Y') }}</td>
                                <td><span class="badge {{ $saleReturn->status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">{{ strtoupper($saleReturn->status) }}</span></td>
                                <td class="text-end fw-bold">₹ {{ number_format($saleReturn->total, 2) }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('sale_returns.edit', $saleReturn->id) }}" class="btn btn-sm btn-outline-primary" title="View / Modify"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('sale_returns.print', $saleReturn->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print"><i class="fas fa-print"></i></a>
                                    <form action="{{ route('sale_returns.destroy', $saleReturn->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this sale return?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-undo fa-2x d-block mb-2 opacity-25"></i>No sale returns for this customer.</td></tr>
                            @endforelse
                        </tbody>
                        @if($saleReturns->count())
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">₹ {{ number_format($stats['returned'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
    .cust-avatar {
        width: 46px; height: 46px; flex: 0 0 46px;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem; font-weight: 600;
    }
    .stat-card { border-left: 4px solid #cbd5e1 !important; }
    .border-start-primary { border-left-color: #4f46e5 !important; }
    .border-start-success { border-left-color: #10b981 !important; }
    .border-start-danger { border-left-color: #ef4444 !important; }
    .border-start-warning { border-left-color: #f59e0b !important; }

    .nav-tabs .nav-link {
        font-size: 0.8rem; font-weight: 600; color: #64748b;
        border: none; border-bottom: 2px solid transparent; padding: 0.5rem 0.85rem;
    }
    .nav-tabs .nav-link.active {
        color: var(--primary-color); background: transparent;
        border-bottom-color: var(--primary-color);
    }
    .doc-table { font-size: 0.8rem; }
    .doc-table th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.4px; color: #64748b; white-space: nowrap; }
    .doc-table .btn-sm { padding: 0.15rem 0.4rem; font-size: 0.7rem; }
</style>
@endsection
