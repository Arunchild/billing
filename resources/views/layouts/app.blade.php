<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AVP Soft') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --sidebar-width: 230px; /* Compact width */
            --header-height: 50px;
            
            /* Vibrant Palette */
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            --secondary-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            --orange-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --purple-gradient: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            
            --primary-color: #4f46e5;
            --sidebar-bg: #0f172a;
            --sidebar-active: rgba(255, 255, 255, 0.1);
            --bg-color: #f1f5f9;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: #334155;
            font-size: 0.875rem; /* Compact font size */
            overflow-x: hidden;
        }

        /* Compact Layout Overrides */
        h1, h2, h3, h4, h5, h6 { font-weight: 600; letter-spacing: -0.025em; }
        .btn-sm, .form-control-sm, .input-group-sm > .form-control, .input-group-sm > .input-group-text, .input-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .table td, .table th {
            padding: 0.5rem 0.75rem; /* Compact table cells */
            vertical-align: middle;
        }
        .card-header {
            padding: 0.75rem 1rem;
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
        }
        .card-body { padding: 1rem; }
        .mb-4 { margin-bottom: 1.5rem !important; }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0,0,0,0.2);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.25rem 1rem;
            background: linear-gradient(to right, #0f172a, #1e293b);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar-header h2 {
            font-size: 1.25rem;
            margin: 0;
            background: linear-gradient(90deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .sidebar-menu li a:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.05);
        }

        .sidebar-menu li a.active {
            background: var(--primary-gradient);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        .sidebar-menu li a i {
            width: 24px;
            margin-right: 10px;
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .sidebar-menu li a.active i { opacity: 1; }

        /* Icon Colors in Sidebar (when not active) */
        .sidebar-menu li a:not(.active) i.fa-th-large { color: #60a5fa; }
        .sidebar-menu li a:not(.active) i.fa-file-invoice { color: #34d399; }
        .sidebar-menu li a:not(.active) i.fa-shopping-cart { color: #fbbf24; }
        .sidebar-menu li a:not(.active) i.fa-shopping-bag { color: #f87171; }
        .sidebar-menu li a:not(.active) i.fa-boxes { color: #a78bfa; }
        .sidebar-menu li a:not(.active) i.fa-calculator { color: #22d3ee; }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 15px; /* Compact padding */
            min-height: 100vh;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 0.5rem 1rem; /* Compact top bar */
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #f1f5f9;
        }
        
        /* Vibrant Buttons */
        .btn-primary { background: var(--primary-gradient); border: none; }
        .btn-success { background: var(--secondary-gradient); border: none; }
        .btn-danger { background: var(--danger-gradient); border: none; }
        .btn-warning { background: var(--orange-gradient); border: none; color: white; }
        
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Cards & Widgets */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            margin-bottom: 15px; /* Compact spacing */
        }
        
        .quick-action-btn {
            font-size: 0.8rem;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        
        .form-control, .form-select {
            font-size: 0.875rem;
            border-color: #cbd5e1;
            padding: 0.4rem 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }
        
        .input-group-text { background-color: #f8fafc; border-color: #cbd5e1; }

        /* Select2 Customization */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #cbd5e1;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-header">
        <h2>AVP <span style="font-weight:300;">Soft</span></h2>
        <div class="version">1.0</div>
    </div>
    
    <style>
        /* Additional Sidebar Submenu Styles */
        .sidebar-menu .submenu {
            background-color: rgba(0, 0, 0, 0.2);
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu .submenu li a {
            padding-left: 45px;
            font-size: 0.8rem;
        }
        .sidebar-menu .submenu .submenu li a {
            padding-left: 65px;
        }
        .sidebar-menu a[data-bs-toggle="collapse"] {
            position: relative;
        }
        .sidebar-menu a[data-bs-toggle="collapse"]::after {
            content: "\f107";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s;
        }
        .sidebar-menu a[data-bs-toggle="collapse"][aria-expanded="true"]::after {
            transform: translateY(-50%) rotate(180deg);
        }
    </style>
    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard</a></li>
        
        <!-- Sale Menu -->
        <li>
            <a href="#saleSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('invoices.*') || request()->routeIs('sale_returns.*') || request()->routeIs('quotations.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Sale
            </a>
            <ul class="collapse submenu {{ request()->routeIs('invoices.*') || request()->routeIs('sale_returns.*') || request()->routeIs('quotations.*') ? 'show' : '' }}" id="saleSubmenu">
                
                <!-- Invoice Submenu -->
                <li>
                    <a href="#invoiceSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('invoices.*') ? 'text-white' : '' }}">
                        <i class="fas fa-file-alt"></i> Invoice
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('invoices.*') ? 'show' : '' }}" id="invoiceSubmenu">
                        <li><a href="{{ route('invoices.create') }}" class="{{ request()->routeIs('invoices.create') ? 'active' : '' }}">New Invoice</a></li>
                        <li><a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Sale Return Submenu -->
                <li>
                    <a href="#saleReturnSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('sale_returns.*') ? 'text-white' : '' }}">
                        <i class="fas fa-undo"></i> Sale Return
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('sale_returns.*') ? 'show' : '' }}" id="saleReturnSubmenu">
                        <li><a href="{{ route('sale_returns.create') }}" class="{{ request()->routeIs('sale_returns.create') ? 'active' : '' }}">New Sale Return</a></li>
                        <li><a href="{{ route('sale_returns.index') }}" class="{{ request()->routeIs('sale_returns.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Quotation Submenu -->
                <li>
                    <a href="#quotationSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('quotations.*') ? 'text-white' : '' }}">
                        <i class="fas fa-file-contract"></i> Quotation
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('quotations.*') ? 'show' : '' }}" id="quotationSubmenu">
                        <li><a href="{{ route('quotations.create') }}" class="{{ request()->routeIs('quotations.create') ? 'active' : '' }}">New Quotation</a></li>
                        <li><a href="{{ route('quotations.index') }}" class="{{ request()->routeIs('quotations.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>
            </ul>
        </li>

        <!-- Purchase Menu -->
        <li>
            <a href="#purchaseSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('purchases.*') || request()->routeIs('purchase_returns.*') || request()->routeIs('purchase_orders.*') || request()->routeIs('debit_notes.*') || request()->routeIs('credit_notes.*') || request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i> Purchase
            </a>
            <ul class="collapse submenu {{ request()->routeIs('purchases.*') || request()->routeIs('purchase_returns.*') || request()->routeIs('purchase_orders.*') || request()->routeIs('debit_notes.*') || request()->routeIs('credit_notes.*') || request()->routeIs('suppliers.*') ? 'show' : '' }}" id="purchaseSubmenu">
                
                <!-- Purchase Bill Submenu -->
                <li>
                    <a href="#purchaseBillSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('purchases.*') ? 'text-white' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> Purchase Bill
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('purchases.*') ? 'show' : '' }}" id="purchaseBillSubmenu">
                        <li><a href="{{ route('purchases.create') }}" class="{{ request()->routeIs('purchases.create') ? 'active' : '' }}">Add Purchase Bill</a></li>
                        <li><a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Purchase Return Submenu -->
                <li>
                    <a href="#purchaseReturnSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('purchase_returns.*') ? 'text-white' : '' }}">
                        <i class="fas fa-undo"></i> Purchase Return
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('purchase_returns.*') ? 'show' : '' }}" id="purchaseReturnSubmenu">
                        <li><a href="{{ route('purchase_returns.create') }}" class="{{ request()->routeIs('purchase_returns.create') ? 'active' : '' }}">Add Purchase Return</a></li>
                        <li><a href="{{ route('purchase_returns.index') }}" class="{{ request()->routeIs('purchase_returns.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Purchase Order Submenu -->
                <li>
                    <a href="#purchaseOrderSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('purchase_orders.*') ? 'text-white' : '' }}">
                        <i class="fas fa-clipboard-list"></i> Purchase Order
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('purchase_orders.*') ? 'show' : '' }}" id="purchaseOrderSubmenu">
                        <li><a href="{{ route('purchase_orders.create') }}" class="{{ request()->routeIs('purchase_orders.create') ? 'active' : '' }}">New Order</a></li>
                        <li><a href="{{ route('purchase_orders.index') }}" class="{{ request()->routeIs('purchase_orders.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Debit Note Submenu -->
                <li>
                    <a href="#debitNoteSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('debit_notes.*') ? 'text-white' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> Debit Note
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('debit_notes.*') ? 'show' : '' }}" id="debitNoteSubmenu">
                        <li><a href="{{ route('debit_notes.create') }}" class="{{ request()->routeIs('debit_notes.create') ? 'active' : '' }}">New Debit Note</a></li>
                        <li><a href="{{ route('debit_notes.index') }}" class="{{ request()->routeIs('debit_notes.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Credit Note Submenu -->
                <li>
                    <a href="#creditNoteSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('credit_notes.*') ? 'text-white' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> Credit Note
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('credit_notes.*') ? 'show' : '' }}" id="creditNoteSubmenu">
                         <li><a href="{{ route('credit_notes.create') }}" class="{{ request()->routeIs('credit_notes.create') ? 'active' : '' }}">New Credit Note</a></li>
                        <li><a href="{{ route('credit_notes.index') }}" class="{{ request()->routeIs('credit_notes.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>

                <!-- Supplier Submenu -->
                <li>
                    <a href="#supplierSubmenu" data-bs-toggle="collapse" class="{{ request()->routeIs('suppliers.*') ? 'text-white' : '' }}">
                        <i class="fas fa-users"></i> Supplier
                    </a>
                    <ul class="collapse submenu {{ request()->routeIs('suppliers.*') ? 'show' : '' }}" id="supplierSubmenu">
                         <li><a href="{{ route('suppliers.create') }}" class="{{ request()->routeIs('suppliers.create') ? 'active' : '' }}">Add Supplier</a></li>
                        <li><a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.index') ? 'active' : '' }}">Search & Manage</a></li>
                    </ul>
                </li>
            </ul>
        </li>

        <li><a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}"><i class="fas fa-calculator"></i> Accounts</a></li>
        <li><a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}"><i class="fas fa-wallet"></i> Expense</a></li>
        <li><a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Customer</a></li>
        <li><a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.index') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}"><i class="fas fa-user-tie"></i> Staff</a></li>
        <li><a href="{{ route('tools.index') }}" class="{{ request()->routeIs('tools.*') ? 'active' : '' }}"><i class="fas fa-tools"></i> Tools</a></li>
        <li><a href="{{ route('master.index') }}" class="{{ request()->routeIs('master.*') ? 'active' : '' }}"><i class="fas fa-database"></i> Master</a></li>
        <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
</nav>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Bar -->
    <div class="top-bar animate__animated animate__fadeInDown">
        <div class="d-flex align-items-center" style="width: 50%;">
            <form action="{{ route('invoices.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Invoice No or Customer Name or Mobile No" value="{{ request('search') }}">
                <button class="btn btn-success" type="submit"><i class="fas fa-search"></i> Search in Invoice</button>
            </form>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <div class="small text-muted">Helpline : +91-6262 9898 04</div>
            </div>
            <a href="#" class="text-secondary"><i class="fas fa-cloud-upload-alt fa-lg"></i></a>
            <a href="#" class="text-secondary"><i class="fas fa-user-circle fa-lg"></i></a>
            <a href="#" class="text-secondary"><i class="fas fa-calculator fa-lg"></i></a>
            <a href="#" class="text-secondary"><i class="fas fa-bell fa-lg"></i></a>
            <a href="#" class="text-secondary"><i class="fas fa-money-bill-wave fa-lg"></i></a>
            <a href="#" class="text-secondary"><i class="fas fa-cog fa-lg"></i></a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // Global Configuration
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
    };

    // Re-initialize plugins on Turbo Load (Navigation) AND initial load
    document.addEventListener('turbo:load', function() {
        // Initialize all Select2 elements if they aren't already
        // We destroy and recreate to ensure clean state after navigation cache restoration
        $('.select2').each(function() {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            $(this).select2({
                theme: 'bootstrap-5'
            });
        });

        // Sidebar Accordion Behavior (Re-bind)
        // Unbind first to prevent duplicate listeners
        $('.sidebar-menu .collapse').off('show.bs.collapse').on('show.bs.collapse', function () {
            $(this).parent().siblings().find('.collapse.show').collapse('hide');
        });

        // Fade in main content if desired
        $('.main-content').addClass('animate__animated animate__fadeIn');
    });
    
    // Legacy support for scripts doing $(document).ready()
    // Since Turbo fires 'turbo:load' instead of standard events on nav, 
    // we bridge it for simple cases, but specific views should ideally listen to turbo:load.
</script>
<script type="module">
    import hotwiredTurbo from 'https://cdn.skypack.dev/@hotwired/turbo';
</script>
</script>

@stack('scripts')
</body>
</html>
