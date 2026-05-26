<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11px; 
            line-height: 1.25; 
            color: #000; 
            background-color: #fff; 
            margin: 0;
            padding: 5px;
            box-sizing: border-box;
        }
        .container { 
            width: 100%; 
            max-width: 100%; 
            margin: 0 auto; 
            border: 1px solid #000; 
            background-color: #fff;
        }
        .no-print { margin-bottom: 10px; text-align: right; }
        
        /* Typography */
        h2 { font-size: 13px; margin: 0 0 3px 0; font-weight: bold; }
        p { margin: 0 0 2px 0; }
        
        /* Layout */
        .w-100 { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-bottom { border-bottom: 1px solid #000; }
        .border-right { border-right: 1px solid #000; }
        .border-top { border-top: 1px solid #000; }
        
        /* Section Styles */
        .top-section {
            display: flex;
            border-bottom: 1px solid #000;
        }
        
        .left-col {
            width: 60%;
            border-right: 1px solid #000;
            display: flex;
            flex-direction: column;
        }
        
        .right-col {
            width: 40%;
            padding: 10px 0 0 10px;
        }
        
        .company-header {
            display: flex;
            border-bottom: 1px solid #000;
        }
        
        .logo-box {
            width: 110px;
            background-color: #fff; 
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #000;
        }
        
        .company-info {
            flex: 1;
            padding: 5px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .billed-to {
            padding: 5px 10px;
            min-height: 60px;
        }
        
        .invoice-meta-item {
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        /* Items Table */
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .items-table th { 
            border-bottom: 1px solid #000; 
            border-right: 1px solid #000; 
            padding: 6px 3px; 
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            text-transform: uppercase;
        }
        .items-table td { 
            border-right: 1px solid #000; 
            padding: 4px 6px; 
            vertical-align: middle;
        }
        .items-table th:last-child, .items-table td:last-child { border-right: none; }
        
        /* Instead of one row with divs, use multiple TRs for data, but no horizontal border */
        .item-data-row td {
            text-align: center;
        }
        .item-data-row td.text-left { text-align: left; }
        
        /* Spacer row that stretches the empty space */
        .spacer-row td {
            height: 30px; /* Compact height for the item area */
        }
        
        /* Totals / Words row */
        .totals-row {
            display: flex;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .amount-words-col {
            width: 73%;
            padding: 6px 8px;
            border-right: 1px solid #000;
        }
        .total-label-col {
            width: 12%;
            padding: 6px 8px;
            text-align: center;
            border-right: 1px solid #000;
        }
        .total-val-col {
            width: 15%;
            padding: 6px 8px;
            text-align: right;
        }
        
        /* Footer Tax row */
        .tax-footer {
            padding: 6px 8px;
            min-height: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .tax-line {
            margin-bottom: 3px;
            font-size: 11px;
        }

        @media print {
            .no-print { display: none; }
            @page {
                size: A5 landscape;
                margin: 3mm;
            }
            body { 
                background-color: #fff;
                color: #000;
                padding: 0;
                margin: 0;
                width: 100%;
            }
            .container { 
                border: 1px solid #000; 
                margin: 0; 
                width: 100%;
                max-width: 100%;
                background-color: #fff;
                box-sizing: border-box;
                page-break-inside: avoid;
            }
            .logo-box { background-color: #fff !important; }
            .spacer-row td { height: 20px !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #fff; color: #222; border: none; cursor: pointer; border-radius: 5px; font-weight:bold;">Print Invoice</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">Close</button>
    </div>

    <div class="container">
        <!-- Top Section -->
        <div class="top-section">
            <div class="left-col">
                <div class="company-header">
                    <div class="logo-box">
                        <img src="{{ asset('images/biofix-logo.jpg') }}" alt="BIOFIX" style="max-width: 90px; max-height: 50px;">
                    </div>
                    <div class="company-info">
                        <h2>BIOFIX HEALTHCARE PVT. LTD.</h2>
                        <p>27/18A, Sathia Complex, Sinclair Street, Marthandam (PO)</p>
                        <p>PIN : 629165</p>
                        <p>GSTIN: 33AANCB5605Q1Z9</p>
                    </div>
                </div>
                <div class="billed-to">
                    <p style="margin-left: 10px; font-size: 12px;">To,</p>
                    <div style="margin-left: 30px; margin-top: 5px; margin-bottom: 8px;">
                        <p><strong>{{ $invoice->customer_name ?? $invoice->customer->name }}</strong></p>
                        <p>{!! nl2br(e($invoice->customer_address ?? $invoice->customer->address)) !!}</p>
                        <p>{{ $invoice->customer->city ?? '' }} {{ $invoice->customer->pincode ? '- '.$invoice->customer->pincode : '' }}</p>
                        <p>Phone: {{ $invoice->customer->phone ?? '' }}</p>
                    </div>
                    <p style="margin-left: 10px; margin-top: 5px;">GSTIN: &nbsp;&nbsp; {{ $invoice->customer_gstin ?? $invoice->customer->gstin ?? '' }}</p>
                </div>
            </div>
            
            <div class="right-col">
                <div class="invoice-meta-item">
                    INVOICE NO : &nbsp;&nbsp; {{ $invoice->invoice_number }}
                </div>
                <div class="invoice-meta-item">
                    INVOICE DATE: &nbsp;&nbsp; {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">SL<br>NO</th>
                    <th style="width: 35%;">Description / Size</th>
                    <th style="width: 12%;">HSN CODE</th>
                    <th style="width: 10%;">QUANTITY</th>
                    <th style="width: 10%;">MRP</th>
                    <th style="width: 10%;">TAXABLE<br>VALUE</th>
                    <th style="width: 8%;">GST%</th>
                    <th style="width: 10%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data Rows -->
                @foreach($invoice->items as $index => $item)
                <tr class="item-data-row">
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        {{ $item->product_name }} 
                        @if($item->item_description)
                            - {{ $item->item_description }}
                        @endif
                    </td>
                    <td>{{ $item->product->hsn_sac_code ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->product->mrp ?? 0, 2) }}</td>
                    <td>{{ number_format(($item->quantity * $item->price), 2) }}</td>
                    <td>{{ number_format($item->tax_rate, 0) }}%</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                
                <!-- Spacer Row to fill height -->
                <tr class="spacer-row">
                    <td style="height: 20px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- Totals Row -->
        <div class="totals-row">
            <div class="amount-words-col">
                <strong>Total Amount(In Words) :</strong> 
                <span style="text-transform: capitalize; margin-left: 10px;">
                    @if(class_exists('NumberFormatter'))
                        {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($invoice->total) }} Rupees Only
                    @else
                        {{ $invoice->total }} Rupees Only
                    @endif
                </span>
            </div>
            <div class="total-label-col">
                TOTAL
            </div>
            <div class="total-val-col">
                {{ number_format($invoice->total, 2) }}
            </div>
        </div>

        <!-- Footer Tax Info -->
        <div class="tax-footer bordertop" style="border-top: 1px solid #000;">
            @php
                // Typically CGST and SGST are half of total tax. If Interstate, it's IGST. Assuming local CGST/SGST split for now as per screenshot.
                $cgst = $invoice->tax_total / 2;
                $sgst = $invoice->tax_total / 2;
            @endphp
            <div class="tax-line">CGST : &nbsp;&nbsp; {{ number_format($cgst, 2) }}</div>
            <div class="tax-line">SGST : &nbsp;&nbsp; {{ number_format($sgst, 2) }}</div>
            <div class="tax-line" style="margin-top: 15px;">TOTAL GST : &nbsp;&nbsp; {{ number_format($invoice->tax_total, 2) }}</div>
        </div>
    </div>
</body>
</html>
