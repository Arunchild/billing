@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('invoices.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
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
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-plus"></i> Add New Invoice
                </a>
            </div>
        </form>
    </div>
</div>

<div class="px-2 py-2 mb-2 d-flex align-items-center gap-3 animate__animated animate__fadeIn">
    <span class="fw-bold text-muted small text-uppercase">Invoice(s)</span>
    <div class="d-flex align-items-center gap-3">
        <a href="javascript:void(0)" class="text-decoration-none small text-danger" onclick="bulkDelete()"><i class="fas fa-trash-alt"></i> Delete</a>
        <a href="javascript:void(0)" class="text-decoration-none small text-success" onclick="alert('Bulk mark as paid not implemented yet')"><i class="fas fa-check-circle"></i> Mark As Paid</a>
        <a href="javascript:void(0)" class="text-decoration-none small text-primary" onclick="bulkPrint()"><i class="fas fa-print"></i> Print</a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-2"><input type="checkbox" id="selectAll"></th>
                        <th class="py-2">S. No.</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Type</th>
                        <th class="py-2">Invoice No.</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Customer Name</th>
                        <th class="py-2">Contact No.</th>
                        <th class="py-2">Address</th>
                        <th class="py-2">Total</th>
                        <th class="py-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $index => $invoice)
                    <tr>
                        <td><input type="checkbox" name="selected_invoices[]" value="{{ $invoice->id }}"></td>
                        <td>{{ $invoices->firstItem() + $index }}</td>
                        <td>
                            <span class="badge {{ $invoice->status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ strtoupper($invoice->status) }}
                            </span>
                        </td>
                        <td>{{ strtoupper($invoice->type ?? 'GST') }}</td>
                        <td class="fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                        <td class="fw-bold">{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
                        <td>{{ $invoice->customer ? $invoice->customer->phone : '-' }}</td>
                        <td>{{ Str::limit($invoice->customer ? $invoice->customer->address : '-', 20) }}</td>
                        <td class="fw-bold">{{ number_format($invoice->total, 2) }}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}"><i class="fas fa-edit text-primary me-2"></i> View / Modify</a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.print', $invoice->id) }}" target="_blank"><i class="fas fa-print text-secondary me-2"></i> Print / Export</a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.clone', $invoice->id) }}"><i class="fas fa-copy text-info me-2"></i> Create Invoice Clone</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                <p>No invoices found matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($invoices->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="9" class="text-end fw-bold">Total Page Amount:</td>
                        <td class="fw-bold">₹ {{ number_format($invoices->sum('total'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $invoices->withQueryString()->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Date Filter Logic
        const periodSelect = document.getElementById('periodSelect');
        const fromDate = document.getElementById('fromDate');
        const toDate = document.getElementById('toDate');
        const filterForm = document.getElementById('filterForm');

        // Helper to format date as YYYY-MM-DD
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };

        periodSelect.addEventListener('change', function() {
            const period = this.value;
            const today = new Date();
            let start = new Date();
            let end = new Date();

            if (period === 'custom') {
                // Do not auto-submit on custom, let user pick dates
                return; 
            }

            switch(period) {
                case 'last_7_days':
                    start.setDate(today.getDate() - 7);
                    break;
                case 'last_month':
                    start.setMonth(today.getMonth() - 1);
                    start.setDate(1);
                    end.setMonth(today.getMonth());
                    end.setDate(0); // Last day of prev month
                    break;
                case 'this_month':
                    start.setDate(1);
                    break;
                case 'yesterday':
                    start.setDate(today.getDate() - 1);
                    end.setDate(today.getDate() - 1);
                    break;
                case 'today':
                    // start and end are already today
                    break;
                case 'this_week':
                    // Assuming week starts on Monday (1) or Sunday (0). Let's say Sunday.
                    const day = today.getDay(); 
                    const diff = today.getDate() - day; 
                    start.setDate(diff); // First day of week
                    break;
            }

            if (period !== 'custom') {
                fromDate.value = formatDate(start);
                toDate.value = formatDate(end);
                // Auto submit form to apply filter immediately
                filterForm.submit();
            }
        });

        // "Select All" checkbox logic
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_invoices[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    function bulkPrint() {
        const selected = Array.from(document.querySelectorAll('input[name="selected_invoices[]"]:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one invoice to print.');
            return;
        }
        
        // For simplicity, just opening the first one or a loop. 
        // Ideally a bulk print view or PDF merger is needed.
        // Opening multiple tabs might be blocked.
        // Let's just open the first one for now or loop if < 3
        
        if(selected.length > 5) {
            if(!confirm('You are about to open ' + selected.length + ' print windows. Continue?')) return;
        }

        selected.forEach(id => {
             window.open('/invoices/' + id + '/print', '_blank');
        });
    }
    function bulkDelete() {
        const selected = Array.from(document.querySelectorAll('input[name="selected_invoices[]"]:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one invoice to delete.');
            return;
        }

        if (!confirm('Are you sure you want to delete ' + selected.length + ' invoice(s)? This action cannot be undone.')) {
            return;
        }

        fetch('{{ route("invoices.bulk_destroy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids: selected })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // optional: show success toast
                // toastr.success(data.message);
                // Reload page to reflect changes
                window.location.reload(); 
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
</script>

<style>
    /* Table Header custom style to match screenshot */
    thead.bg-primary th {
        background-color: #0d6efd !important; /* Bootstrap Primary or custom blue */
        color: white;
        font-weight: 500;
        border-bottom: none;
    }
    
    /* Hover menu adjustments */
    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: var(--bs-primary);
    }
    
    /* Remove outline from btn-icon */
    .btn-icon:focus {
        box-shadow: none;
    }

    /* Fix for dropdown clipping in table-responsive */
    .table-responsive {
        /* Allow min-height so dropdowns have space to render if near bottom */
        min-height: 400px;
        /* Or use overflow visible if horizontal scrolling isn't needed for this width, 
           but usually it is. A common fix is enough padding bottom. */
        padding-bottom: 150px; 
    }
    /* Counteract the padding so it doesn't look empty */
    .card-body.p-0 {
        overflow: hidden; /* Hide the extra padding space if not used */
    }
    /* Re-enable overflow for table-responsive but constrained by card-body? 
       No, that hides it again. 
       Better fix: Make the dropdown static or append to body via JS if simple CSS fails.
       But padding-bottom is a reliable CSS-only hack for small lists.
       Let's try standard JS popper config via data attr first? 
       Actually, `position: static` on .dropdown sometimes helps in tables.
    */
</style>
@endsection
