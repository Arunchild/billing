@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="alert alert-info py-2 mb-3 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" role="alert">
        <i class="fas fa-info-circle text-white"></i> <span class="text-white">Welcome to your billing dashboard! All systems operational.</span>
    </div>

    <div class="row">
        <!-- Main Stats Column -->
        <div class="col-md-5">
            <div class="card mb-3 animate__animated animate__fadeInLeft border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <span class="fw-bold">Today's Performance</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">{{ date('d M, Y') }}</span>
                        <a href="#"><i class="fas fa-sync-alt text-primary"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="small text-white opacity-75 text-uppercase mb-1">Gross Sale</div>
                                <h4 class="text-white fw-bold mb-0">₹ {{ number_format($grossSale, 0) }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="small text-white opacity-75 text-uppercase mb-1">No. of Invoices</div>
                                <h4 class="text-white fw-bold mb-0">{{ $invoiceCount }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <div class="small text-white opacity-75 text-uppercase mb-1">Amount Received</div>
                                <h4 class="text-white fw-bold mb-0">₹ {{ number_format($amountReceived, 0) }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <div class="small text-white opacity-75 text-uppercase mb-1">Amount Paid</div>
                                <h4 class="text-white fw-bold mb-0">₹ 0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row g-3 mb-3 animate__animated animate__fadeInUp">
                <div class="col-6">
                    <a href="{{ route('invoices.create') }}" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">New Invoice</h6>
                                <small class="opacity-75">Create GST Invoice</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="#" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">New Quotation</h6>
                                <small class="opacity-75">Price Estimate</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('purchases.create') }}" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">Add Purchase</h6>
                                <small class="opacity-75">Record Stock In</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('expenses.create') }}" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">Add Expense</h6>
                                <small class="opacity-75">Track Spending</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('customers.create') }}" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">Add Customer</h6>
                                <small class="opacity-75">Register New</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('reminders.create') }}" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">Add Reminder</h6>
                                <small class="opacity-75">Set Alert</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('barcode.scanner') }}" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">Scan Barcode</h6>
                                <small class="opacity-75">Quick Lookup</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="#" class="text-decoration-none">
                        <div class="quick-action-card" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
                            <div class="icon-circle">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="action-text">
                                <h6 class="mb-0">Payment</h6>
                                <small class="opacity-75">In / Out</small>
                            </div>
                            <i class="fas fa-arrow-right action-arrow"></i>
                        </div>
                    </a>
                </div>
            </div>
            
            <style>
            .quick-action-card {
                padding: 1rem;
                border-radius: 12px;
                color: white;
                display: flex;
                align-items: center;
                gap: 12px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                position: relative;
                overflow: hidden;
            }
            
            .quick-action-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            }
            
            .quick-action-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.1);
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .quick-action-card:hover::before {
                opacity: 1;
            }
            
            .icon-circle {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                background: rgba(255,255,255,0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }
            
            .action-text {
                flex: 1;
            }
            
            .action-text h6 {
                font-weight: 600;
                font-size: 0.95rem;
            }
            
            .action-text small {
                font-size: 0.75rem;
            }
            
            .action-arrow {
                font-size: 1.2rem;
                opacity: 0.7;
                transition: transform 0.3s;
            }
            
            .quick-action-card:hover .action-arrow {
                transform: translateX(5px);
            }
            </style>
            
            
            <div class="card mb-3">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>BUSINESS INSIGHTS</span>
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-frown fa-3x text-warning mb-3 opacity-50"></i>
                    <p class="text-muted">No Sufficient Data</p>
                </div>
            </div>
        </div>

        <!-- Vital Stats & Charts Column -->
        <div class="col-md-7">
            <div class="card mb-3 animate__animated animate__fadeInRight">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-3">
                    <h6 class="mb-0 text-uppercase fw-bold text-muted">Vital Stats</h6>
                    <i class="fas fa-sync-alt text-primary"></i>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="vital-stat-item">
                                <div class="vital-icon bg-warning"></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">₹ {{ number_format($amountPending, 0) }}</h5>
                                    <small class="text-muted">Amount Outstanding</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="vital-stat-item">
                                <div class="vital-icon bg-info"></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $unpaidInvoices }}</h5>
                                    <small class="text-muted">Unpaid Invoices</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="vital-stat-item">
                                <div class="vital-icon bg-success"></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">0</h5>
                                    <small class="text-muted">Unpaid Purchases</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="vital-stat-item">
                                <div class="vital-icon bg-danger"></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $overdueInvoices }}</h5>
                                    <small class="text-muted">Overdue Invoices</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="vital-stat-item">
                                <div class="vital-icon bg-secondary"></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">0</h5>
                                    <small class="text-muted">Open Quotation</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="vital-stat-item">
                                <div class="vital-icon bg-primary"></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">0</h5>
                                    <small class="text-muted">Staff Present Today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card animate__animated animate__fadeInRight animate__delay-1s">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div class="d-flex gap-3">
                        <span class="fw-bold">Recent Activity</span>
                        <a href="#" class="text-decoration-none text-muted">Customer Due</a>
                        <a href="#" class="text-decoration-none text-muted">Supplier Due</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                         <select class="form-select form-select-sm" style="width: auto;">
                             <option>All</option>
                         </select>
                         <select class="form-select form-select-sm" style="width: auto;">
                             <option>Last 30 days</option>
                         </select>
                        <i class="fas fa-sync-alt text-primary"></i>
                    </div>
                </div>
                <div class="card-body" style="height: 300px; position:relative;">
                     <!-- Placeholder for chart -->
                     <div style="position: absolute; bottom: 10px; left: 0; right: 0; height: 1px; background: #eee;"></div>
                     <div style="position: absolute; bottom: 60px; left: 0; right: 0; height: 1px; background: #eee;"></div>
                     <div style="position: absolute; bottom: 110px; left: 0; right: 0; height: 1px; background: #eee;"></div>
                     <div style="position: absolute; bottom: 160px; left: 0; right: 0; height: 1px; background: #eee;"></div>
                     <div style="position: absolute; bottom: 210px; left: 0; right: 0; height: 1px; background: #eee;"></div>
                     
                     <div class="d-flex h-100 align-items-end justify-content-center text-muted">
                         <p class="small">Chart Visualization Area</p>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
