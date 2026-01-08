@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Scanner Column -->
        <div class="col-md-5">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="text-center text-white mb-4">
                        <i class="fas fa-barcode fa-4x mb-3 opacity-75"></i>
                        <h4 class="fw-bold">Quick Customer Lookup</h4>
                        <p class="opacity-75">Scan barcode or search manually</p>
                    </div>

                    <div class="bg-white rounded p-4 mb-3">
                        <form id="scannerForm" action="{{ route('barcode.lookup') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Scan or Enter Barcode</label>
                                <input type="text" name="barcode" id="barcodeInput" class="form-control form-control-lg text-center" 
                                       placeholder="8270071710" autofocus style="font-size: 1.5rem; letter-spacing: 2px;">
                                <small class="text-muted">Or use mobile number / reg no</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-search"></i> Lookup Customer
                            </button>
                        </form>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('customers.create') }}" class="btn btn-light w-100">
                                <i class="fas fa-user-plus"></i> New Customer
                            </a>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#manualSearchModal">
                                <i class="fas fa-list"></i> View All
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-3 mt-3">
                <div class="col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary fw-bold mb-0">{{ \App\Models\Customer::count() }}</h3>
                            <small class="text-muted">Total Customers</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-success fw-bold mb-0">{{ \App\Models\Customer::whereDate('created_at', today())->count() }}</h3>
                            <small class="text-muted">New Today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info / Results Column -->
        <div class="col-md-7">
            <div id="customerResult" class="d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" width="150">Name:</td>
                                        <td class="fw-bold" id="custName">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Reg. No:</td>
                                        <td id="custRegNo">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Age / Gender:</td>
                                        <td id="custAge">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Mobile:</td>
                                        <td id="custMobile">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">City:</td>
                                        <td id="custCity">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Address:</td>
                                        <td id="custAddress">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3 bg-light">
                                    <div id="barcodeDisplay"></div>
                                    <small class="text-muted d-block mt-2" id="barcodeNumber">-</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <a href="#" id="createInvoiceBtn" class="btn btn-primary">
                                <i class="fas fa-file-invoice"></i> Create Invoice
                            </a>
                            <a href="#" id="viewHistoryBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-history"></i> View History
                            </a>
                            <a href="#" id="editCustomerBtn" class="btn btn-outline-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="placeholder" class="card border-0 shadow-sm">
                <div class="card-body py-5 text-center">
                    <i class="fas fa-qrcode fa-5x text-muted opacity-25 mb-4"></i>
                    <h5 class="text-muted">Scan a customer barcode to begin</h5>
                    <p class="text-muted small">or search by mobile number / registration number</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Search Modal -->
<div class="modal fade" id="manualSearchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Customers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="customerSearch" class="form-control mb-3" placeholder="Search by name, mobile, or reg no...">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="customerList">
                            @foreach(\App\Models\Customer::latest()->limit(50)->get() as $customer)
                            <tr>
                                <td>{{ $customer->reg_no }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary select-customer" data-id="{{ $customer->id }}">
                                        Select
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('scannerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const barcode = document.getElementById('barcodeInput').value;
    
    fetch('{{ route("barcode.lookup") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ barcode: barcode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayCustomer(data.customer);
        } else {
            alert('Customer not found!');
            document.getElementById('barcodeInput').value = '';
        }
    });
});

function displayCustomer(customer) {
    document.getElementById('placeholder').classList.add('d-none');
    document.getElementById('customerResult').classList.remove('d-none');
    
    document.getElementById('custName').textContent = customer.name;
    document.getElementById('custRegNo').textContent = customer.reg_no || '-';
    document.getElementById('custAge').textContent = (customer.age ? customer.age + ' Y' : '') + ' ' + (customer.gender || '');
    document.getElementById('custMobile').textContent = customer.phone || '-';
    document.getElementById('custCity').textContent = customer.city || '-';
    document.getElementById('custAddress').textContent = customer.address || '-';
    document.getElementById('barcodeNumber').textContent = customer.barcode || customer.phone;
    
    // Update action buttons
    document.getElementById('createInvoiceBtn').href = '{{ url("/invoices/create") }}?customer_id=' + customer.id;
    document.getElementById('editCustomerBtn').href = '{{ url("/customers") }}/' + customer.id + '/edit';
    
    document.getElementById('barcodeInput').value = '';
    document.getElementById('barcodeInput').focus();
}

// Customer search in modal
document.getElementById('customerSearch').addEventListener('keyup', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('#customerList tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});
</script>
@endsection
