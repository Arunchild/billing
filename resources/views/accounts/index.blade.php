@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Accounts</h2>
        <p class="text-muted mb-0">Manage bank accounts, cash in hand, and financial transactions</p>
    </div>
    <div>
        <a href="{{ route('accounts.statements') }}" class="btn btn-outline-success me-2">
            <i class="fas fa-file-invoice-dollar"></i> Financial Statement
        </a>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Account
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4 animate__animated animate__fadeInUp">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Total Balance</p>
                        <h3 class="mb-0 fw-bold">₹ {{ number_format($totalBalance, 2) }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-wallet fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Active Accounts</p>
                        <h3 class="mb-0 fw-bold">{{ $accounts->where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-university fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 small">Total Accounts</p>
                        <h3 class="mb-0 fw-bold">{{ $accounts->count() }}</h3>
                    </div>
                    <div class="opacity-50">
                        <i class="fas fa-credit-card fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accounts List -->
<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Type</th>
                        <th>Account Number</th>
                        <th>Current Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($account->type == 'bank')
                                        <i class="fas fa-university fa-2x text-primary"></i>
                                    @elseif($account->type == 'cash')
                                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                    @else
                                        <i class="fas fa-credit-card fa-2x text-info"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $account->name }}</h6>
                                    @if($account->bank_name)
                                        <small class="text-muted">{{ $account->bank_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-soft-primary text-primary text-uppercase">{{ $account->type }}</span></td>
                        <td>{{ $account->account_number ?? '-' }}</td>
                        <td class="fw-bold text-{{ $account->current_balance >= 0 ? 'success' : 'danger' }}">
                            ₹ {{ number_format($account->current_balance, 2) }}
                        </td>
                        <td>
                            @if($account->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('accounts.show', $account->id) }}" class="btn btn-sm btn-outline-info" title="View Transactions"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This will delete all associated transactions.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-university fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No accounts found. Add your first account to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
