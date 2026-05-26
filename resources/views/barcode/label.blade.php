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
                border: none !important;
                overflow: hidden !important;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            width: 2.5in;
            height: 1.5in;
            padding: 0.05in;
            margin: 0 auto;
            border: 1px solid #ccc;
            overflow: hidden;
        }
        
        .label {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .company {
            font-weight: bold;
            font-size: 7.5pt;
            text-align: center;
            margin-bottom: 2px;
        }
        
        .barcode-container {
            margin: 2px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        
        #barcode {
            max-width: 2.3in;
            display: block;
        }
        
        .barcode-text {
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
            margin-top: 1px;
        }
        
        .info {
            font-size: 6.5pt;
            line-height: 1.2;
            text-align: center;
            width: 100%;
        }
        
        .info-row {
            margin: 0.5px 0;
        }
        
        .name {
            font-weight: bold;
            font-size: 8pt;
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
            Print Label
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
            <div class="info-row"><strong>Reg No:</strong> {{ $customer->reg_no }} &nbsp;|&nbsp; <strong>Gen/Age:</strong> {{ $customer->gender ?? 'M' }} / {{ $customer->age ?? '-' }} Y</div>
            <div class="info-row"><strong>Mobile:</strong> {{ $customer->phone ?? '-' }} &nbsp;|&nbsp; <strong>City:</strong> {{ $customer->city ?? 'MARTHANDAM' }}</div>
        </div>
    </div>
    
    <script>
        // Generate barcode
        var barcodeValue = '{{ $customer->barcode ?? $customer->phone ?? "8270000000" }}';
        
        JsBarcode("#barcode", barcodeValue, {
            format: "CODE128",
            width: 1.2,
            height: 25,
            displayValue: true,
            fontSize: 9,
            margin: 0,
            background: "#ffffff",
            lineColor: "#000000"
        });
    </script>
</body>
</html>
