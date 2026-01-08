@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Tools & Utilities</h2>
        <p class="text-muted mb-0">System maintenance tasks</p>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <!-- Backup -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="mb-3 text-primary">
                    <i class="fas fa-database fa-3x"></i>
                </div>
                <h5 class="card-title fw-bold">Database Backup</h5>
                <p class="card-text text-muted small">Create a full backup of your billing database.</p>
                <button class="btn btn-outline-primary w-100" onclick="alert('Backup process started...')">
                    <i class="fas fa-download me-2"></i>Download Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Restore -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="mb-3 text-warning">
                    <i class="fas fa-upload fa-3x"></i>
                </div>
                <h5 class="card-title fw-bold">Restore Data</h5>
                <p class="card-text text-muted small">Restore database from a previous SQL backup file.</p>
                <button class="btn btn-outline-warning w-100" onclick="alert('Restore feature coming soon!')">
                    <i class="fas fa-history me-2"></i>Restore Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Clear Cache -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="mb-3 text-danger">
                    <i class="fas fa-broom fa-3x"></i>
                </div>
                <h5 class="card-title fw-bold">Clear System Cache</h5>
                <p class="card-text text-muted small">Clear application cache to resolve glitches.</p>
                <button class="btn btn-outline-danger w-100" onclick="alert('Cache cleared successfully!')">
                    <i class="fas fa-sync me-2"></i>Clear Cache
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
