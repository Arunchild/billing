@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Inventory Management</h2>
        <p class="text-muted mb-0">Real-time stock tracking and inventory adjustments</p>
    </div>
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
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
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
            </div>
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

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const table = document.getElementById('inventoryTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    }
});
</script>
@endsection
