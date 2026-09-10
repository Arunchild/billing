<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; line-height: 1.4; color: #000; background: #fff; margin: 0; padding: 10px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; border: 1px solid #000; box-sizing: border-box; }
        .no-print { margin-bottom: 20px; text-align: right; padding: 10px; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* Header */
        .header-section { display: flex; border-bottom: 1px solid #000; }
        .logo-box { width: 22%; padding: 10px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #000; }
        .company-box { width: 52%; padding: 10px; text-align: center; border-right: 1px solid #000; }
        .company-box h2 { margin: 0; font-size: 17px; font-weight: bold; text-transform: uppercase; }
        .company-box .address { font-size: 12px; margin-top: 4px; }
        .gst-box { width: 26%; padding: 10px; display: flex; flex-direction: column; justify-content: center; font-size: 12px; }

        /* Title Bar */
        .title-bar {
            background: #333;
            color: #fff;
            display: flex;
            border-bottom: 1px solid #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .title-left { width: 30%; }
        .title-center { width: 40%; text-align: center; padding: 6px 0; font-size: 17px; font-weight: bold; letter-spacing: 1px; }
        .title-right { width: 30%; padding: 5px; font-size: 11px; display: flex; flex-direction: column; justify-content: center; }

        /* Meta grid */
        .meta-grid { display: flex; border-bottom: 1px solid #000; }
        .meta-left { width: 55%; padding: 8px; border-right: 1px solid #000; }
        .meta-right { width: 45%; padding: 8px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 3px 2px; vertical-align: top; }
        .label { color: #333; }

        /* Amount block */
        .amount-table { width: 100%; border-collapse: collapse; }
        .amount-table th { border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 7px; font-size: 13px; font-weight: normal; background: #f2f2f2; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .amount-table th:last-child { border-right: none; }
        .amount-table td { border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 8px; vertical-align: top; }
        .amount-table td:last-child { border-right: none; }
        .big-amount { font-size: 18px; font-weight: bold; }

        .words-box { padding: 8px; border-bottom: 1px solid #000; min-height: 34px; }

        /* Ledger */
        .ledger-table { width: 100%; border-collapse: collapse; }
        .ledger-table td { padding: 6px 8px; border-right: 1px solid #000; }
        .ledger-table td:last-child { border-right: none; }

        /* Footer */
        .footer-boxes { display: flex; border-top: 1px solid #000; }
        .left-footer { width: 55%; padding: 8px; border-right: 1px solid #000; font-size: 11px; }
        .right-footer { width: 45%; padding: 8px; text-align: center; height: 80px; position: relative; }
        .sig-text { position: absolute; bottom: 6px; width: 100%; left: 0; font-size: 12px; }

        @media print {
            .no-print { display: none; }
            @page { margin: 8mm; }
            body { padding: 0; }
            .container { border: 2px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; cursor: pointer; border-radius: 5px;">Print Receipt</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">Close</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header-section">
            <div class="logo-box">
                <img src="{{ asset('images/biofix-logo.jpg') }}" alt="BIOFIX" style="max-height: 70px; max-width: 100%;">
            </div>
            <div class="company-box">
                <h2>BIOFIX HEALTHCARE PVT. LTD.</h2>
                <div class="address">
                    27/18A,<br>
                    Sathia Complex, Sinclair Street, Marthandam (PO)<br>
                    <u>PIN</u> : 629165
                </div>
                <div style="margin-top: 4px; font-size: 12px;">
                    Ph No: 9442384497 &nbsp;&nbsp; Web: www.biofixhealthcare.com
                </div>
            </div>
            <div class="gst-box">
                <div style="margin-bottom: 10px;">GSTIN: 33AANCB5605Q1Z9</div>
                <div>PAN: AANCB5605Q</div>
            </div>
        </div>

        <!-- Title Bar -->
        <div class="title-bar">
            <div class="title-left"></div>
            <div class="title-center">PAYMENT RECEIPT</div>
            <div class="title-right">
                <div style="margin-bottom: 3px;"><span style="font-size: 14px; margin-right: 5px; vertical-align: middle;">&#9744;</span>Original-Customer Copy</div>
                <div><span style="font-size: 14px; margin-right: 5px; vertical-align: middle;">&#9744;</span>Office Copy</div>
            </div>
        </div>

        <!-- Meta -->
        <div class="meta-grid">
            <div class="meta-left">
                <div class="text-bold" style="margin-bottom: 4px;">Received With Thanks From</div>
                <div class="text-bold" style="font-size: 14px;">{{ $receipt->customer->name ?? '-' }}</div>
                @if($receipt->customer)
                    @if($receipt->customer->address)
                        <div>{{ $receipt->customer->address }}</div>
                    @endif
                    @if($receipt->customer->city || $receipt->customer->pincode)
                        <div>{{ $receipt->customer->city }} {{ $receipt->customer->pincode }}</div>
                    @endif
                    @if($receipt->customer->phone)
                        <div>Ph: {{ $receipt->customer->phone }}</div>
                    @endif
                    @if($receipt->customer->gst_number)
                        <div>GSTIN: {{ $receipt->customer->gst_number }}</div>
                    @endif
                    @if($receipt->customer->reg_no)
                        <div>Reg No: {{ $receipt->customer->reg_no }}</div>
                    @endif
                @endif
            </div>
            <div class="meta-right">
                <table class="meta-table">
                    <tr>
                        <td class="label">Receipt No.</td>
                        <td class="text-bold">: {{ $receipt->receipt_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Receipt Date</td>
                        <td class="text-bold">: {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d-M-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Mode</td>
                        <td class="text-bold">: {{ strtoupper(str_replace('_', ' ', $receipt->payment_mode)) }}</td>
                    </tr>
                    @if($receipt->reference_no)
                    <tr>
                        <td class="label">Reference No.</td>
                        <td>: {{ $receipt->reference_no }}</td>
                    </tr>
                    @endif
                    @if($receipt->bank_name)
                    <tr>
                        <td class="label">Bank</td>
                        <td>: {{ $receipt->bank_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Against Invoice</td>
                        <td>: {{ $receipt->invoice->invoice_number ?? 'On Account' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Amount -->
        <table class="amount-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Particulars</th>
                    <th style="width: 30%;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $receipt->notes ?: 'Payment received towards outstanding dues.' }}
                        @if($receipt->invoice)
                            <div style="margin-top: 6px;">
                                Invoice {{ $receipt->invoice->invoice_number }}
                                dated {{ \Carbon\Carbon::parse($receipt->invoice->invoice_date)->format('d-M-Y') }},
                                invoice total ₹ {{ number_format($receipt->invoice->total, 2) }}
                            </div>
                        @endif
                    </td>
                    <td class="text-right big-amount">{{ number_format($receipt->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Amount in words -->
        <div class="words-box">
            <span class="text-bold">Amount in Words: </span>
            @if(class_exists('NumberFormatter'))
                {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($receipt->amount)) }} Rupees Only
            @else
                {{ number_format($receipt->amount, 2) }} Rupees Only
            @endif
        </div>

        <!-- Ledger position -->
        <table class="ledger-table">
            <tr>
                <td style="width: 34%;">Total Billed: <span class="text-bold">₹ {{ number_format($totalInvoiced, 2) }}</span></td>
                <td style="width: 33%;">Total Received: <span class="text-bold">₹ {{ number_format($totalReceived, 2) }}</span></td>
                <td style="width: 33%;">Balance Due: <span class="text-bold">₹ {{ number_format($balanceDue, 2) }}</span></td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer-boxes">
            <div class="left-footer">
                Cheque / online payments are subject to realisation.<br>
                This receipt is valid only for the amount stated above.<br>
                @if($receipt->received_by)
                    <div style="margin-top: 8px;">Received By: <span class="text-bold">{{ $receipt->received_by }}</span></div>
                @endif
            </div>
            <div class="right-footer">
                <div class="sig-text">For BIOFIX HEALTHCARE PVT. LTD.<br><br>Authorised Signatory</div>
            </div>
        </div>
    </div>
</body>
</html>
