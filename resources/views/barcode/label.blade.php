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
                overflow: hidden !important;
            }
            .stage {
                border: none !important;
            }
            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            width: 2.5in;
            height: 1.5in;
            margin: 0 auto;
            overflow: hidden;
        }

        /* Physical label: 2.5in wide x 1.5in tall. */
        .stage {
            position: relative;
            width: 2.5in;
            height: 1.5in;
            border: 1px solid #ccc;
            overflow: hidden;
        }

        /* Logical label laid out in PORTRAIT (1.4in x 2.4in), then rotated 90deg
           to sit on the landscape stock. Everything inside - company, barcode,
           name and details - therefore prints vertically. */
        .label {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 1.4in;
            height: 2.4in;
            transform: translate(-50%, -50%) rotate(90deg);
            transform-origin: center center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.03in;
        }

        .company {
            font-weight: bold;
            font-size: 6.5pt;
            line-height: 1.15;
            text-align: center;
            white-space: nowrap;
            margin-bottom: 3px;
        }

        .barcode-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2px 0;
        }

        /* Scaling wrapper: JsBarcode writes its own inline transform onto the
           <svg>, so any transform of ours has to live on a wrapper element. */
        .barcode-fit {
            transform-origin: center center;
            line-height: 0;
        }

        #barcode {
            display: block;
        }

        .info {
            font-size: 6.5pt;
            line-height: 1.3;
            text-align: center;
            width: 100%;
            margin-top: 2px;
        }

        .info-row {
            margin: 0.5px 0;
        }

        .name {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 1px;
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

    <div class="stage">
        <div class="label">
            <div class="company">BIOFIX HEALTHCARE PVT LTD</div>

            <div class="barcode-container">
                <div class="barcode-fit">
                    <svg id="barcode"></svg>
                </div>
            </div>

            <div class="info">
                <div class="info-row name">{{ trim($customer->salutation . ' ' . strtoupper($customer->name)) }}</div>
                <div class="info-row"><strong>Reg No:</strong> {{ $customer->reg_no }}</div>
                <div class="info-row"><strong>Gen/Age:</strong> {{ $customer->gender ?? '-' }} / {{ $customer->age ?? '-' }} Y</div>
                <div class="info-row"><strong>Mobile:</strong> {{ $customer->phone ?? '-' }}</div>
                <div class="info-row"><strong>City:</strong> {{ $customer->city ?? 'MARTHANDAM' }}</div>
            </div>
        </div>
    </div>

    <script>
        // Generate barcode
        var barcodeValue = '{{ $customer->barcode ?? $customer->phone ?? "8270000000" }}';

        JsBarcode("#barcode", barcodeValue, {
            format: "CODE128",
            width: 1.2,
            height: 34,
            displayValue: true,
            fontSize: 9,
            textMargin: 1,
            margin: 0,
            background: "#ffffff",
            lineColor: "#000000"
        });

        // The whole label is rotated, so the barcode runs along the label's
        // long edge. Its run length is limited by the logical column width.
        (function fitBarcode() {
            var svg = document.getElementById('barcode');
            var fit = document.querySelector('.barcode-fit');
            var box = document.querySelector('.barcode-container');
            var runLength = parseFloat(svg.getAttribute('width')) || 0;
            var available = box.clientWidth;

            if (runLength > available && runLength > 0) {
                fit.style.transform = 'scale(' + (available / runLength) + ')';
            }
        })();
    </script>
</body>
</html>
