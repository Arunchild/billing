@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Inventory Management</h2>
        <p class="text-muted mb-0">Real-time stock tracking and inventory adjustments</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" id="btnExport">
            <i class="fas fa-file-excel"></i> Download Excel
        </button>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('inventory.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
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
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4 animate__animated animate__fadeInUp">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Total Products</p>
                        <h3 class="mb-0 fw-bold">{{ $totalProducts }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-boxes fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Low Stock Items</p>
                        <h3 class="mb-0 fw-bold">{{ $lowStockProducts }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Out of Stock</p>
                        <h3 class="mb-0 fw-bold">{{ $outOfStockProducts }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-box-open fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Total Stock Value</p>
                        <h3 class="mb-0 fw-bold">₹ {{ number_format($totalStockValue, 2) }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-coins fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Table -->
<div class="card animate__animated animate__fadeInUp">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Stock Overview</h5>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="inventoryTable">
                <thead class="bg-light">
                    <tr>
                        <th>Product</th>
                        <th>Item Code</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Min. Level</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->current_stock <= 0 ? 'table-danger' : ($product->current_stock <= $product->low_level_limit ? 'table-warning' : '') }}">
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                @if($product->brand)
                                    <small class="text-muted">{{ $product->brand }}</small>
                                @endif
                            </div>
                        </td>
                        <td>{{ $product->item_code ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $product->unit ?? 'N/A' }}</span></td>
                        <td>
                            <strong class="text-{{ $product->current_stock <= 0 ? 'danger' : ($product->current_stock <= $product->low_level_limit ? 'warning' : 'success') }}">
                                {{ number_format($product->current_stock, 2) }}
                            </strong>
                        </td>
                        <td>{{ $product->low_level_limit }}</td>
                        <td>₹ {{ number_format($product->current_stock * $product->purchase_price, 2) }}</td>
                        <td>
                            @if($product->current_stock <= 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($product->current_stock <= $product->low_level_limit && $product->low_level_limit > 0)
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $product->id }}">
                                <i class="fas fa-edit"></i> Adjust
                            </button>
                        </td>
                    </tr>

                    <!-- Adjustment Modal -->
                    <div class="modal fade" id="adjustModal{{ $product->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Adjust Stock: {{ $product->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('inventory.adjust', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Current Stock</label>
                                            <input type="text" class="form-control" value="{{ $product->current_stock }} {{ $product->unit }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Adjustment Type</label>
                                            <select name="adjustment_type" class="form-select" required>
                                                <option value="add">Add Stock</option>
                                                <option value="subtract">Subtract Stock</option>
                                                <option value="set">Set Stock</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" step="0.01" name="quantity" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Reason (Optional)</label>
                                            <textarea name="reason" class="form-control" rows="2" placeholder="Reason for adjustment"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Stock</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No products with tracking enabled.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $products->links() }}
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
            const search = document.querySelector('input[name="search"]').value;
            
            let url = "{{ route('reports.export') }}?report_type=inventory&filter_type=range";
            if (from) url += "&from_date=" + from;
            if (to) url += "&to_date=" + to;
            if (search) url += "&search=" + encodeURIComponent(search);
            
            window.location.href = url;
        });
    });
</script>
@endpush
@endsection
