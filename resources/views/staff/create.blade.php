@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="mb-1 fw-bold text-primary">Add Staff</h2>
        <p class="text-muted mb-0">Onboard a new staff member</p>
    </div>
    <div>
        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row animate__animated animate__fadeInUp">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('staff.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="staff">Staff/Cashier</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        
                        <div class="col-12 mt-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Login Access</h6>
                            <hr class="mt-1">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Username (Email)</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter username or email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter secure password">
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Menu Access Permissions</h6>
                            <hr class="mt-1">
                        </div>

                        <div class="col-12">
                            <div class="row g-3">
                                <!-- Sales Modules -->
                                <div class="col-md-6">
                                    <div class="card border-light shadow-sm h-100">
                                        <div class="card-header bg-light py-2">
                                            <span class="fw-bold text-primary"><i class="fas fa-file-invoice me-2"></i>Sales Module</span>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="invoice" id="perm_invoice" checked>
                                                <label class="form-check-label" for="perm_invoice">Invoice (Sales)</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="sale_return" id="perm_sale_return" checked>
                                                <label class="form-check-label" for="perm_sale_return">Sale Return</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="quotation" id="perm_quotation" checked>
                                                <label class="form-check-label" for="perm_quotation">Quotation</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchase Modules -->
                                <div class="col-md-6">
                                    <div class="card border-light shadow-sm h-100">
                                        <div class="card-header bg-light py-2">
                                            <span class="fw-bold text-success"><i class="fas fa-shopping-bag me-2"></i>Purchase Module</span>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="purchase_bill" id="perm_purchase_bill" checked>
                                                <label class="form-check-label" for="perm_purchase_bill">Purchase Bill</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="purchase_return" id="perm_purchase_return" checked>
                                                <label class="form-check-label" for="perm_purchase_return">Purchase Return</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="purchase_order" id="perm_purchase_order" checked>
                                                <label class="form-check-label" for="perm_purchase_order">Purchase Order</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="debit_note" id="perm_debit_note" checked>
                                                <label class="form-check-label" for="perm_debit_note">Debit Note</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="credit_note" id="perm_credit_note" checked>
                                                <label class="form-check-label" for="perm_credit_note">Credit Note</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="supplier" id="perm_supplier" checked>
                                                <label class="form-check-label" for="perm_supplier">Supplier</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Core Modules -->
                                <div class="col-md-6">
                                    <div class="card border-light shadow-sm h-100">
                                        <div class="card-header bg-light py-2">
                                            <span class="fw-bold text-warning"><i class="fas fa-boxes me-2"></i>Core Modules</span>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="dashboard" id="perm_dashboard" checked>
                                                <label class="form-check-label" for="perm_dashboard">Dashboard</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="inventory" id="perm_inventory" checked>
                                                <label class="form-check-label" for="perm_inventory">Inventory</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="accounts" id="perm_accounts" checked>
                                                <label class="form-check-label" for="perm_accounts">Accounts</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="expense" id="perm_expense" checked>
                                                <label class="form-check-label" for="perm_expense">Expense</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="customer" id="perm_customer" checked>
                                                <label class="form-check-label" for="perm_customer">Customer</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="reports" id="perm_reports" checked>
                                                <label class="form-check-label" for="perm_reports">Reports</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- System Management -->
                                <div class="col-md-6">
                                    <div class="card border-light shadow-sm h-100">
                                        <div class="card-header bg-light py-2">
                                            <span class="fw-bold text-danger"><i class="fas fa-cog me-2"></i>System & Management</span>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="staff" id="perm_staff" checked>
                                                <label class="form-check-label" for="perm_staff">Staff</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="tools" id="perm_tools" checked>
                                                <label class="form-check-label" for="perm_tools">Tools</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="master" id="perm_master" checked>
                                                <label class="form-check-label" for="perm_master">Master</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="settings" id="perm_settings" checked>
                                                <label class="form-check-label" for="perm_settings">Settings</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Save Staff</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
