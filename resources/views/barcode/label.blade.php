<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Barcode - {{ $customer->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: 2.5in 1.5in;
            margin: 0;
        }
        
        @media print {
            html, body {
                width: 2.5in;
                height: 1.5in;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            width: 2.5in;
            height: 1.5in;
            padding: 0.1in;
            margin: 0 auto;
            border: 1px solid #ccc;
        }
        
        .label {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        
        .company {
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
            margin-bottom: 2px;
        }
        
        .barcode-container {
            margin: 3px 0;
        }
        
        #barcode {
            max-width: 2.2in;
        }
        
        .barcode-text {
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            margin-top: 2px;
        }
        
        .info {
            font-size: 7pt;
            line-height: 1.2;
            text-align: center;
            width: 100%;
        }
        
        .info-row {
            margin: 1px 0;
        }
        
        .name {
            font-weight: bold;
            font-size: 9pt;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        
        .print-btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Label
        </button>
        <button class="print-btn" onclick="window.close()" style="background: #6c757d; margin-left: 5px;">
            Close
        </button>
    </div>

    <div class="label">
        <div class="company">BIOFIX HEALTHCARE PVT LTD</div>
        
        <div class="barcode-container">
            <svg id="barcode"></svg>
        </div>
        
        <div class="info">
            <div class="info-row name">Mr. {{ strtoupper($customer->name) }}</div>
            <div class="info-row"><strong>Reg No:</strong> {{ $customer->reg_no }}</div>
            <div class="info-row"><strong>Gender/Age:</strong> {{ $customer->gender ?? 'M' }} / {{ $customer->age ?? '-' }} Y</div>
            <div class="info-row"><strong>Mobile:</strong> {{ $customer->phone ?? '-' }}</div>
            <div class="info-row"><strong>City:</strong> {{ $customer->city ?? 'MARTHANDAM' }}</div>
        </div>
    </div>
    
    <script>
        // Generate barcode
        var barcodeValue = '{{ $customer->barcode ?? $customer->phone ?? "8270000000" }}';
        
        JsBarcode("#barcode", barcodeValue, {
            format: "CODE128",
            width: 1.5,
            height: 35,
            displayValue: true,
            fontSize: 12,
            margin: 0,
            background: "#ffffff",
            lineColor: "#000000"
        });
        
        // Auto print when page loads (optional - comment out if not needed)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };
    </script>
</body>
</html>
