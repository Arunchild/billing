@extends('layouts.app')

@section('content')
@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('sale_returns.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
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
                    <input type="text" name="search" class="form-control" placeholder="Search keyword..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="col-md-2 text-end">
                <a href="{{ route('sale_returns.create') }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-plus"></i> New Return
                </a>
            </div>
        </form>
    </div>
</div>

<div class="px-2 py-2 mb-2 d-flex align-items-center gap-3 animate__animated animate__fadeIn">
    <span class="fw-bold text-muted small text-uppercase">Return(s)</span>
    <div class="d-flex align-items-center gap-3">
        <a href="javascript:void(0)" class="text-decoration-none small text-danger" onclick="alert('Bulk delete not implemented yet')"><i class="fas fa-trash-alt"></i> Delete</a>
        <a href="javascript:void(0)" class="text-decoration-none small text-primary" onclick="bulkPrint()"><i class="fas fa-print"></i> Print</a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 400px; padding-bottom: 150px;">
            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-2"><input type="checkbox" id="selectAll"></th>
                        <th class="py-2">S. No.</th>
                        <th class="py-2">Return No.</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Customer Name</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Status</th>
                        <th class="py-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saleReturns as $index => $return)
                    <tr>
                        <td><input type="checkbox" name="selected_returns[]" value="{{ $return->id }}"></td>
                        <td>{{ $saleReturns->firstItem() + $index }}</td>
                        <td class="fw-bold text-primary">{{ $return->return_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d-M-Y') }}</td>
                        <td class="fw-bold">{{ $return->customer ? $return->customer->name : 'N/A' }}</td>
                        <td class="fw-bold">{{ number_format($return->total, 2) }}</td>
                        <td>
                             <span class="badge {{ $return->status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ strtoupper($return->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="{{ route('sale_returns.edit', $return->id) }}"><i class="fas fa-edit text-primary me-2"></i> View / Modify</a></li>
                                    <li><a class="dropdown-item" href="{{ route('sale_returns.print', $return->id) }}" target="_blank"><i class="fas fa-print text-secondary me-2"></i> Print / Export</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('sale_returns.destroy', $return->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-undo fa-3x mb-3 opacity-50"></i>
                                <p>No sale returns found matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $saleReturns->withQueryString()->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodSelect = document.getElementById('periodSelect');
        const fromDate = document.getElementById('fromDate');
        const toDate = document.getElementById('toDate');
        const filterForm = document.getElementById('filterForm');

        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };

        periodSelect.addEventListener('change', function() {
            const period = this.value;
            const today = new Date();
            let start = new Date();
            let end = new Date();

            if (period === 'custom') return;

            switch(period) {
                case 'last_7_days': start.setDate(today.getDate() - 7); break;
                case 'last_month': start.setMonth(today.getMonth() - 1); start.setDate(1); end.setMonth(today.getMonth()); end.setDate(0); break;
                case 'this_month': start.setDate(1); break;
                case 'yesterday': start.setDate(today.getDate() - 1); end.setDate(today.getDate() - 1); break;
                case 'today': break;
                case 'this_week': let day = today.getDay(); let diff = today.getDate() - day; start.setDate(diff); break;
            }

            if (period !== 'custom') {
                fromDate.value = formatDate(start);
                toDate.value = formatDate(end);
                filterForm.submit();
            }
        });

        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_returns[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    function bulkPrint() {
        const selected = Array.from(document.querySelectorAll('input[name="selected_returns[]"]:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one return to print.');
            return;
        }
        if(selected.length > 5 && !confirm('Open ' + selected.length + ' windows?')) return;

        selected.forEach(id => {
             window.open('/sale_returns/' + id + '/print', '_blank');
        });
    }
</script>

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
