<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Return Print - {{ $saleReturn->return_number }}</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f1f5f9; font-family: sans-serif; }
        .print-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        @media print {
            body { background-color: white; margin: 0; }
            .print-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: 100%;
                border-radius: 0;
            }
            .no-print { display: none !important; }
        }
        .company-logo { max-width: 150px; }
        .invoice-title {
            color: #0d6efd;
            font-weight: 700;
            font-size: 2rem;
            text-transform: uppercase;
        }
        .table-total tr td { font-weight: bold; }
        .border-top-2 { border-top: 2px solid #dee2e6 !important; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="mb-4 no-print text-center">
            <button onclick="window.print()" class="btn btn-primary btn-lg"><i class="fas fa-print me-2"></i> Print Return</button>
            <button onclick="window.close()" class="btn btn-secondary btn-lg ms-2"><i class="fas fa-times me-2"></i> Close</button>
        </div>

        <div class="print-container">
            <!-- Header -->
            <div class="row mb-5 align-items-center">
                <div class="col-8">
                    <img src="https://placehold.co/150x50?text=Company+Logo" alt="Logo" class="company-logo mb-3">
                    <h4 class="mb-0 fw-bold">My Company Name</h4>
                    <p class="text-muted mb-0">123 Business Street, Chennai, TN, 600001</p>
                    <p class="text-muted mb-0">Phone: +91 98765 43210 | Email: support@mycompany.com</p>
                </div>
                <div class="col-4 text-end">
                    <div class="invoice-title">Sale Return</div>
                    <h5 class="mb-1">#{{ $saleReturn->return_number }}</h5>
                    <p class="text-muted mb-1">Date: {{ \Carbon\Carbon::parse($saleReturn->return_date)->format('d M, Y') }}</p>
                    <p class="text-muted">Original Invoice: <span class="fw-bold">{{ $saleReturn->invoice_id ? 'INV-LINKED' : 'N/A' }}</span></p>
                </div>
            </div>

            <!-- Bill To -->
            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-uppercase text-muted fw-bold small mb-2">Return From:</h6>
                    <h5 class="fw-bold mb-1">{{ $saleReturn->customer->name }}</h5>
                    <p class="mb-0">{{ $saleReturn->customer->address ?? 'No Address' }}</p>
                    <p class="mb-0">{{ $saleReturn->customer->phone }}</p>
                    <p class="mb-0">{{ $saleReturn->customer->email }}</p>
                </div>
            </div>

            <!-- Items -->
            <div class="table-responsive mb-4">
                <table class="table table-striped table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th scope="col" class="py-2">#</th>
                            <th scope="col" class="py-2">Item Description</th>
                            <th scope="col" class="text-center py-2">Qty</th>
                            <th scope="col" class="text-end py-2">Price</th>
                            <th scope="col" class="text-end py-2">Tax</th>
                            <th scope="col" class="text-end py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($saleReturn->items as $index => $item)
                        <tr>
                            <td class="text-center" style="width: 50px;">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->product_name }}</div>
                            </td>
                            <td class="text-center" style="width: 80px;">{{ $item->quantity }}</td>
                            <td class="text-end" style="width: 120px;">{{ number_format($item->price, 2) }}</td>
                            <td class="text-end" style="width: 100px;">{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="text-end" style="width: 120px;">{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row justify-content-end mb-5">
                <div class="col-5">
                    <table class="table table-sm table-borderless fs-6">
                        <tr>
                            <td class="text-end pe-4">Sub Total:</td>
                            <td class="text-end fw-bold">{{ number_format($saleReturn->sub_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4">Tax Total:</td>
                            <td class="text-end fw-bold">{{ number_format($saleReturn->tax_total, 2) }}</td>
                        </tr>
                        @if($saleReturn->discount > 0)
                        <tr>
                            <td class="text-end pe-4 text-danger">Discount:</td>
                            <td class="text-end text-danger">- {{ number_format($saleReturn->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top-2">
                            <td class="text-end pe-4 pt-2 h5">Grand Total:</td>
                            <td class="text-end pt-2 h5 fw-bold text-primary">₹ {{ number_format($saleReturn->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="row mt-5">
                <div class="col-12 text-center text-muted small">
                    <p class="mb-1">This is a computer-generated document. No signature is required.</p>
                    <p>Thank you for your business!</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
