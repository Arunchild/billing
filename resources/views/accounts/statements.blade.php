@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Financial Statements</h2>
        <p class="text-muted mb-0">View overall financial performance and transactions</p>
    </div>
    <div>
        <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Accounts
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4 animate__animated animate__fadeInUp">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body text-white">
                <p class="mb-1 opacity-75 small">Total Income</p>
                <h3 class="mb-0 fw-bold">₹ {{ number_format($totalIncome, 2) }}</h3>
                <small class="opacity-75"><i class="fas fa-arrow-up"></i> Money In</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
            <div class="card-body text-white">
                <p class="mb-1 opacity-75 small">Total Expenses</p>
                <h3 class="mb-0 fw-bold">₹ {{ number_format($totalExpenses, 2) }}</h3>
                <small class="opacity-75"><i class="fas fa-arrow-down"></i> Money Out</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <p class="mb-1 opacity-75 small">Net Balance</p>
                <h3 class="mb-0 fw-bold">₹ {{ number_format($netBalance, 2) }}</h3>
                <small class="opacity-75"><i class="fas fa-equals"></i> Profit/Loss</small>
            </div>
        </div>
    </div>
</div>

<!-- Account Balances -->
<div class="card mb-4 animate__animated animate__fadeInUp">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Account Balances</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($accounts as $account)
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                    <div>
                        <h6 class="mb-0">{{ $account->name }}</h6>
                        <small class="text-muted text-uppercase">{{ $account->type }}</small>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0 fw-bold text-{{ $account->current_balance >= 0 ? 'success' : 'danger' }}">
                            ₹ {{ number_format($account->current_balance, 2) }}
                        </h5>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card animate__animated animate__fadeInUp">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Recent Transactions (Last 20)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('d M, Y') }}</td>
                        <td>{{ $transaction->account->name }}</td>
                        <td>
                            @if($transaction->type == 'credit')
                                <span class="badge bg-success"><i class="fas fa-arrow-down"></i> Credit</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Debit</span>
                            @endif
                        </td>
                        <td>{{ $transaction->category ?? 'General' }}</td>
                        <td>{{ Str::limit($transaction->description, 40) }}</td>
                        <td class="fw-bold text-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                            {{ $transaction->type == 'credit' ? '+' : '-' }} ₹ {{ number_format($transaction->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No transactions recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
