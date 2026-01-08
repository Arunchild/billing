@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Staff</h2>
        <p class="text-muted mb-0">Manage staff and permissions</p>
    </div>
    <div>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Staff
        </a>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-light text-primary fw-bold" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $member->name }}</h6>
                                    <small class="text-muted">{{ $member->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-soft-primary text-primary text-uppercase">{{ $member->role }}</span>
                        </td>
                        <td>{{ $member->phone ?? '-' }}</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this staff member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-user-tie fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">No staff members found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $staff->links() }}
    </div>
</div>
@endsection
