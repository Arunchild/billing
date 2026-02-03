<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase - {{ $purchase->purchase_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: sans-serif; }
        .print-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media print {
            body { background: white; margin: 0; }
            .print-container { box-shadow: none; padding: 0; margin: 0; width: 100%; max-width: 100%; }
            .no-print { display: none !important; }
        }
        .invoice-title { color: #0d6efd; font-weight: 700; font-size: 2rem; text-transform: uppercase; }
        .table-total tr td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="mb-4 no-print text-center">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i> Print Purchase</button>
        </div>
        <div class="print-container">
            <div class="row mb-5 align-items-center">
                <div class="col-8">
                    <h4 class="mb-0 fw-bold">My Company Name</h4>
                    <p class="text-muted mb-0">123 Business Street</p>
                </div>
                <div class="col-4 text-end">
                    <div class="invoice-title">Purchase Bill</div>
                    <h5 class="mb-1">#{{ $purchase->purchase_number }}</h5>
                    <p class="text-muted mb-1">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}</p>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-uppercase text-muted fw-bold small mb-2">Supplier:</h6>
                    <h5 class="fw-bold mb-1">{{ $purchase->supplier->name }}</h5>
                    <p class="mb-0">{{ $purchase->supplier->address }}</p>
                    <p class="mb-0">{{ $purchase->supplier->phone }}</p>
                </div>
            </div>

            <table class="table table-bordered mb-4">
                <thead class="table-light">
                    <tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Tax</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="row justify-content-end">
                <div class="col-5">
                    <table class="table table-sm table-borderless">
                        <tr><td class="text-end">Sub Total:</td><td class="text-end fw-bold">{{ number_format($purchase->sub_total, 2) }}</td></tr>
                        <tr><td class="text-end">Tax Total:</td><td class="text-end fw-bold">{{ number_format($purchase->tax_total, 2) }}</td></tr>
                        <tr><td class="text-end text-danger">Discount:</td><td class="text-end text-danger">- {{ number_format($purchase->discount, 2) }}</td></tr>
                        <tr class="border-top"><td class="text-end h5">Grand Total:</td><td class="text-end h5 fw-bold text-primary">₹ {{ number_format($purchase->total, 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
