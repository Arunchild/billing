@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Excel Reports Generator</h2>
        <p class="text-muted mb-0">Generate and download Excel/CSV reports for billing modules</p>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-export text-primary me-2"></i> Report Configuration</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('reports.export') }}" method="POST" id="reportForm">
                    @csrf
                    
                    <!-- Report Type Selector -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-uppercase text-muted small d-block mb-3">1. Select Report Category</label>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <input type="radio" class="btn-check" name="report_type" id="report_invoices" value="invoices" checked>
                                <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2" for="report_invoices">
                                    <i class="fas fa-file-invoice fa-2x"></i>
                                    <span class="fw-semibold">Invoices</span>
                                </label>
                            </div>
                            <div class="col-md-3 col-6">
                                <input type="radio" class="btn-check" name="report_type" id="report_quotations" value="quotations">
                                <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2" for="report_quotations">
                                    <i class="fas fa-file-contract fa-2x"></i>
                                    <span class="fw-semibold">Quotations</span>
                                </label>
                            </div>
                            <div class="col-md-3 col-6">
                                <input type="radio" class="btn-check" name="report_type" id="report_inventory" value="inventory">
                                <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2" for="report_inventory">
                                    <i class="fas fa-boxes fa-2x"></i>
                                    <span class="fw-semibold">Inventory</span>
                                </label>
                            </div>
                            <div class="col-md-3 col-6">
                                <input type="radio" class="btn-check" name="report_type" id="report_expenses" value="expenses">
                                <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2" for="report_expenses">
                                    <i class="fas fa-wallet fa-2x"></i>
                                    <span class="fw-semibold">Expenses</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Specific Settings -->
                    <div id="inventory_settings_block" class="mb-4 d-none p-3 bg-light rounded-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="apply_date_filter" id="apply_date_filter">
                            <label class="form-check-label fw-semibold" for="apply_date_filter">Filter inventory by registration/added date?</label>
                        </div>
                        <small class="text-muted d-block mt-1">If disabled, the generated report will export the full current inventory status.</small>
                    </div>

                    <!-- Filter Type Selector -->
                    <div class="mb-4" id="filter_type_block">
                        <label class="form-label fw-semibold text-uppercase text-muted small d-block mb-3">2. Select Filter Criteria</label>
                        <div class="row g-2">
                            <div class="col-md">
                                <input type="radio" class="btn-check" name="filter_type" id="filter_range" value="range" checked>
                                <label class="btn btn-outline-secondary w-100 py-2" for="filter_range">Date Range</label>
                            </div>
                            <div class="col-md">
                                <input type="radio" class="btn-check" name="filter_type" id="filter_date" value="date">
                                <label class="btn btn-outline-secondary w-100 py-2" for="filter_date">Particular Date</label>
                            </div>
                            <div class="col-md">
                                <input type="radio" class="btn-check" name="filter_type" id="filter_month" value="month">
                                <label class="btn btn-outline-secondary w-100 py-2" for="filter_month">Month-wise</label>
                            </div>
                            <div class="col-md">
                                <input type="radio" class="btn-check" name="filter_type" id="filter_year" value="year">
                                <label class="btn btn-outline-secondary w-100 py-2" for="filter_year">Year-wise</label>
                            </div>
                            <div class="col-md">
                                <input type="radio" class="btn-check" name="filter_type" id="filter_recurring" value="recurring">
                                <label class="btn btn-outline-secondary w-100 py-2" for="filter_recurring">Recurring List</label>
                            </div>
                        </div>
                    </div>

                    <!-- Inputs depending on selection -->
                    <div class="mb-5 p-3 border rounded bg-white shadow-sm" id="inputs_block">
                        <label class="form-label fw-semibold text-uppercase text-muted small d-block mb-3">3. Specify Dates / Parameters</label>
                        
                        <!-- Date Range Input -->
                        <div id="input_group_range" class="filter-input-group">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Particular Date Input -->
                        <div id="input_group_date" class="filter-input-group d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Select Date</label>
                                    <input type="date" name="report_date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Month-wise Input -->
                        <div id="input_group_month" class="filter-input-group d-none">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Select Month</label>
                                    <select name="report_month" class="form-select">
                                        @for ($m=1; $m<=12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}" {{ \Carbon\Carbon::now()->month == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Select Year</label>
                                    <select name="report_year" class="form-select">
                                        @foreach($yearsList as $yr)
                                            <option value="{{ $yr }}" {{ \Carbon\Carbon::now()->year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Year-wise Input -->
                        <div id="input_group_year" class="filter-input-group d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Select Year</label>
                                    <select name="report_year" class="form-select">
                                        @foreach($yearsList as $yr)
                                            <option value="{{ $yr }}" {{ \Carbon\Carbon::now()->year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Recurring List Input -->
                        <div id="input_group_recurring" class="filter-input-group d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Select Month & Year Range</label>
                                    <select name="recurring_month" class="form-select">
                                        <option value="">Select Month Option</option>
                                        @foreach($monthsList as $monthOption)
                                            <option value="{{ $monthOption['value'] }}">{{ $monthOption['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">Select from the past 12-month interval to download monthly records.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="fas fa-download me-2"></i> Export Report to Excel/CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle input groups based on filter type selection
        $('input[name="filter_type"]').on('change', function() {
            let filterType = $(this).val();
            
            // Hide all filter groups
            $('.filter-input-group').addClass('d-none');
            
            // Show selected group
            $('#input_group_' + filterType).removeClass('d-none');
        });

        // Handle inventory report date toggle
        $('input[name="report_type"]').on('change', function() {
            let reportType = $(this).val();
            
            if (reportType === 'inventory') {
                $('#inventory_settings_block').removeClass('d-none');
                
                // If apply date filter is off, hide filter criteria block
                if (!$('#apply_date_filter').is(':checked')) {
                    $('#filter_type_block').addClass('d-none');
                    $('#inputs_block').addClass('d-none');
                }
            } else {
                $('#inventory_settings_block').addClass('d-none');
                $('#filter_type_block').removeClass('d-none');
                $('#inputs_block').removeClass('d-none');
            }
        });

        $('#apply_date_filter').on('change', function() {
            if ($(this).is(':checked')) {
                $('#filter_type_block').removeClass('d-none');
                $('#inputs_block').removeClass('d-none');
            } else {
                $('#filter_type_block').addClass('d-none');
                $('#inputs_block').addClass('d-none');
            }
        });
    });
</script>
@endpush
@endsection
