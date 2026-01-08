@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Products</h2>
        <p class="text-muted mb-0">Manage your product catalog</p>
    </div>
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Item Code</th>
                        <th>Brand</th>
                        <th>Sale Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->print_name }}</small>
                            </div>
                        </td>
                        <td>{{ $product->item_code ?? '-' }}</td>
                        <td><span class="badge bg-soft-secondary text-secondary">{{ $product->brand }}</span></td>
                        <td class="fw-bold">₹ {{ number_format($product->sale_price, 2) }}</td>
                        <td>
                            <span class="text-{{ $product->current_stock > $product->low_level_limit ? 'success' : 'warning' }}">
                                {{ $product->current_stock }} {{ $product->unit }}
                            </span>
                        </td>
                        <td>
                            @if($product->not_for_sale)
                                <span class="badge bg-secondary">Not For Sale</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-box fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No products found. Add your first product.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
