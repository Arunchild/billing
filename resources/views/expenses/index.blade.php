@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Expenses</h2>
        <p class="text-muted mb-0">Track business expenses</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" id="btnExport">
            <i class="fas fa-file-excel"></i> Download Excel
        </button>
        <a href="{{ route('expenses.create') }}" class="btn btn-danger">
            <i class="fas fa-plus"></i> Add Expense
        </a>
    </div>
</div>

<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('expenses.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-3">
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
            
            <div class="col-md-5">
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
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Expense Name</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $expense->name }}</div>
                            <small class="text-muted">{{ $expense->category_id ?? 'General' }}</small>
                        </td>
                        <td class="text-danger fw-bold">₹ {{ number_format($expense->amount, 2) }}</td>
                        <td>{{ Str::limit($expense->description, 50) }}</td>
                        <td>
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-wallet fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No expenses recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $expenses->links() }}
    </div>
</div>

@push('scripts')
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
                    const day = today.getDay(); 
                    const diff = today.getDate() - day; 
                    start.setDate(diff);
                    break;
            }

            if (period !== 'custom') {
                fromDate.value = formatDate(start);
                toDate.value = formatDate(end);
                filterForm.submit();
            }
        });

        // Excel Export Logic
        document.getElementById('btnExport').addEventListener('click', function() {
            const from = fromDate.value;
            const to = toDate.value;
            
            let url = "{{ route('reports.export') }}?report_type=expenses&filter_type=range";
            if (from) url += "&from_date=" + from;
            if (to) url += "&to_date=" + to;
            
            window.location.href = url;
        });
    });
</script>
@endpush
@endsection
