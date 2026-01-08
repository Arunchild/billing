@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Customers</h2>
        <p class="text-muted mb-0">Manage your customer database</p>
    </div>
    <div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Customer
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Barcode</th>
                        <th>City</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td><span class="badge bg-primary">{{ $customer->reg_no }}</span></td>
                        <td>
                            <div class="fw-medium">{{ $customer->name }}</div>
                            @if($customer->age || $customer->gender)
                                <small class="text-muted">{{ $customer->age }}Y {{ $customer->gender }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>
                            @if($customer->barcode)
                                <span class="badge bg-success">{{ $customer->barcode }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $customer->city ?? '-' }}</td>
                        <td class="text-end">
                            @if($customer->barcode)
                                <a href="{{ route('barcode.label', $customer->id) }}" class="btn btn-sm btn-outline-success" title="Print Barcode" target="_blank">
                                    <i class="fas fa-barcode"></i>
                                </a>
                            @endif
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No customers found. Add your first customer!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $customers->links() }}
    </div>
</div>
@endsection
