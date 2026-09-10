@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('receipts.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light fw-bold">Select</span>
                    <select name="period" class="form-select" id="periodSelect">
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom</option>
                        <option value="last_7_days" {{ request('period') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last month</option>
                        <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This month</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This week</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">From</span>
                    <input type="date" name="from_date" id="fromDate" class="form-control" value="{{ request('from_date') }}">
                    <span class="input-group-text bg-light">To</span>
                    <input type="date" name="to_date" id="toDate" class="form-control" value="{{ request('to_date') }}">
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Receipt no, reference, customer name or mobile..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="col-md-2 text-end">
                <a href="{{ route('receipts.create') }}" class="btn btn-primary btn-sm w-100 text-nowrap">
                    <i class="fas fa-plus"></i> New Receipt
                </a>
            </div>
        </form>
    </div>
</div>

<div class="px-2 py-2 mb-2 d-flex align-items-center gap-3 animate__animated animate__fadeIn">
    <span class="fw-bold text-muted small text-uppercase"><i class="fas fa-receipt me-1"></i> Receipt(s)</span>
    <span class="badge bg-success">Total Received: ₹ {{ number_format($totalReceived, 2) }}</span>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 300px; padding-bottom: 120px;">
            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-2">S. No.</th>
                        <th class="py-2">Receipt No.</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Customer Name</th>
                        <th class="py-2">Contact No.</th>
                        <th class="py-2">Mode</th>
                        <th class="py-2">Reference</th>
                        <th class="py-2">Against Invoice</th>
                        <th class="py-2 text-end">Amount</th>
                        <th class="py-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $index => $receipt)
                    <tr>
                        <td>{{ $receipts->firstItem() + $index }}</td>
                        <td class="fw-bold text-primary">{{ $receipt->receipt_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d-M-Y') }}</td>
                        <td class="fw-bold">
                            @if($receipt->customer)
                                <a href="{{ route('customers.show', $receipt->customer_id) }}" class="text-decoration-none">{{ $receipt->customer->name }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $receipt->customer->phone ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', $receipt->payment_mode)) }}</span></td>
                        <td>{{ $receipt->reference_no ?? '-' }}</td>
                        <td>
                            @if($receipt->invoice)
                                <a href="{{ route('invoices.edit', $receipt->invoice_id) }}" class="text-decoration-none">{{ $receipt->invoice->invoice_number }}</a>
                            @else
                                <span class="text-muted">On Account</span>
                            @endif
                        </td>
                        <td class="fw-bold text-end text-success">₹ {{ number_format($receipt->amount, 2) }}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="{{ route('receipts.edit', $receipt->id) }}"><i class="fas fa-edit text-primary me-2"></i> View / Modify</a></li>
                                    <li><a class="dropdown-item" href="{{ route('receipts.print', $receipt->id) }}" target="_blank"><i class="fas fa-print text-secondary me-2"></i> Print / Export</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                                <p>No receipts found matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($receipts->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="8" class="text-end fw-bold">Total Page Amount:</td>
                        <td class="fw-bold text-end">₹ {{ number_format($receipts->sum('amount'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $receipts->withQueryString()->links() }}
    </div>
</div>

<style>
    thead.bg-primary th {
        background-color: #0d6efd !important;
        color: white;
        font-weight: 500;
        border-bottom: none;
    }
    .btn-icon:focus { box-shadow: none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodSelect = document.getElementById('periodSelect');
        const fromDate = document.getElementById('fromDate');
        const toDate = document.getElementById('toDate');
        const filterForm = document.getElementById('filterForm');

        const formatDate = (date) => date.toISOString().split('T')[0];

        periodSelect.addEventListener('change', function() {
            const period = this.value;
            if (period === 'custom') return;

            const today = new Date();
            let start = new Date();
            let end = new Date();

            switch(period) {
                case 'last_7_days':
                    start.setDate(today.getDate() - 7);
                    break;
                case 'last_month':
                    start.setMonth(today.getMonth() - 1);
                    start.setDate(1);
                    end.setMonth(today.getMonth());
                    end.setDate(0);
                    break;
                case 'this_month':
                    start.setDate(1);
                    break;
                case 'yesterday':
                    start.setDate(today.getDate() - 1);
                    end.setDate(today.getDate() - 1);
                    break;
                case 'today':
                    break;
                case 'this_week':
                    start.setDate(today.getDate() - today.getDay());
                    break;
            }

            fromDate.value = formatDate(start);
            toDate.value = formatDate(end);
            filterForm.submit();
        });
    });
</script>
@endsection
