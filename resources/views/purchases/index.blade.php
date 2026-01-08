@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Purchases</h2>
        <p class="text-muted mb-0">Manage your supplier purchases</p>
    </div>
    <div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Purchase
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body text-center py-5">
        <i class="fas fa-shopping-bag fa-4x text-muted opacity-25 mb-3"></i>
        <h5 class="text-muted">Purchase Module</h5>
        <p class="mb-0 text-muted">Track your inventory purchases and supplier bills here.</p>
    </div>
</div>
@endsection
