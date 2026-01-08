@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Master Data</h2>
        <p class="text-muted mb-0">Manage master records</p>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-weight-hanging fa-3x text-primary mb-3"></i>
                <h5>Units</h5>
                <p class="text-muted small">Manage measurement units (kg, pcs, ltr)</p>
                <a href="{{ route('units.index') }}" class="btn btn-sm btn-outline-primary">Manage Units</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-percentage fa-3x text-success mb-3"></i>
                <h5>Tax Rates</h5>
                <p class="text-muted small">Configure GST tax slabs (5%, 12%, 18%)</p>
                <a href="{{ route('tax_rates.index') }}" class="btn btn-sm btn-outline-success">Manage Taxes</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-tags fa-3x text-warning mb-3"></i>
                <h5>Categories</h5>
                <p class="text-muted small">Product categories and groups</p>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-warning">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-box fa-3x text-info mb-3"></i>
                <h5>Products</h5>
                <p class="text-muted small">Manage product catalog and inventory</p>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-info">Manage Products</a>
            </div>
        </div>
    </div>
</div>
@endsection
