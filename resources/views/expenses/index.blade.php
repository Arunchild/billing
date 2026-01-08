@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Expenses</h2>
        <p class="text-muted mb-0">Track business expenses</p>
    </div>
    <div>
        <a href="{{ route('expenses.create') }}" class="btn btn-danger">
            <i class="fas fa-plus"></i> Add Expense
        </a>
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
@endsection
