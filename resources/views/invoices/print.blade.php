<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; line-height: 1.4; color: #000; }
        .container { width: 100%; max-width: 900px; margin: 0 auto; padding: 10px; border: 1px solid #000; }
        .no-print { margin-bottom: 20px; text-align: right; }
        
        /* Layout Utils */
        .w-100 { width: 100%; }
        .w-50 { width: 50%; }
        .d-flex { display: flex; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px solid #000; }
        .border-right { border-right: 1px solid #000; }
        .border-top { border-top: 1px solid #000; }
        
        /* Section Styles */
        .header-section { display: flex; border-bottom: 1px solid #000; }
        .logo-box { width: 20%; padding: 10px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #000; }
        .company-box { width: 50%; padding: 10px; text-align: center; border-right: 1px solid #000; }
        .gst-box { width: 30%; padding: 10px; display: flex; flex-direction: column; justify-content: center; }
        
        .invoice-title { background: #333; color: #fff; text-align: center; padding: 5px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; -webkit-print-color-adjust: exact; }
        
        .info-grid { display: flex; border-bottom: 1px solid #000; }
        .billing-to { width: 50%; padding: 10px; border-right: 1px solid #000; }
        .invoice-meta { width: 50%; }
        .meta-row { display: flex; border-bottom: 1px solid #ccc; }
        .meta-row:last-child { border-bottom: none; }
        .meta-label { width: 40%; padding: 5px; font-weight: bold; border-right: 1px solid #ccc; }
        .meta-value { width: 60%; padding: 5px; }
        
        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 0; }
        .items-table th { border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 8px; background: #eee; -webkit-print-color-adjust: exact; font-size: 12px; }
        .items-table td { border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 8px; vertical-align: top; }
        .items-table th:last-child, .items-table td:last-child { border-right: none; }
        .items-table .col-desc { width: 40%; }
        
        /* Totals */
        .totals-section { display: flex; justify-content: flex-end; border-bottom: 1px solid #000; }
        .totals-table { width: 40%; border-left: 1px solid #000; border-collapse: collapse; }
        .totals-table td { padding: 5px 10px; border-bottom: 1px solid #ccc; }
        .totals-table tr:last-child td { border-bottom: none; font-weight: bold; font-size: 14px; }
        
        /* Footer */
        .footer-section { display: flex; border-top: 1px solid #000; }
        .amount-words { width: 50%; padding: 10px; border-right: 1px solid #000; font-style: italic; }
        .ack-section { width: 100%; padding: 10px; font-size: 10px; line-height: 1.3; text-align: justify; }
        
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; padding: 0 20px 20px 20px; }
        .sig-box { text-align: center; width: 40%; border-top: 1px solid #000; padding-top: 5px; }

        @media print {
            .no-print { display: none; }
            .container { border: 2px solid #000; height: 100%; max-width: 100%; width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; cursor: pointer; border-radius: 5px;">Print Invoice</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">Close</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header-section">
            <div class="logo-box">
                <img src="{{ asset('images/biofix-logo.jpg') }}" alt="BIOFIX" style="max-width: 100%; max-height: 100px;">
            </div>
            <div class="company-box">
                <h2 style="margin: 0; color: #000; font-weight: bold;">BIOFIX HEALTHCARE PVT. LTD.</h2>
                <div style="font-size: 12px; margin-top: 5px;">
                    27/18A, Sathia Complex, Sinclair Street,<br>
                    Marthandam (PO), PIN : 629165<br>
                    <strong>Ph No:</strong> 9442384497 &nbsp; <strong>Web:</strong> www.biofixhealthcare.com
                </div>
            </div>
            <div class="gst-box" style="font-size: 12px;">
                <div style="margin-bottom: 5px;"><strong>GSTIN:</strong> 33AANCB5605Q1Z9</div>
                <div><strong>PAN:</strong> AANCB5605Q</div>
            </div>
        </div>

        <div class="invoice-title">TAX INVOICE</div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="billing-to">
                <div style="background: #eee; padding: 2px 5px; font-weight: bold; margin-bottom: 5px; -webkit-print-color-adjust: exact;">Billing Address:</div>
                <div style="padding-left: 5px;">
                    <strong>{{ $invoice->customer_name ?? $invoice->customer->name }}</strong><br>
                    {!! nl2br(e($invoice->customer_address ?? $invoice->customer->address)) !!}<br>
                    {{ $invoice->customer->city ?? '' }} - {{ $invoice->customer->pincode ?? '' }}<br>
                    Ph: {{ $invoice->customer->phone ?? '' }}
                </div>
            </div>
            <div class="invoice-meta">
                <div class="meta-row">
                    <div class="meta-label">Invoice Number :</div>
                    <div class="meta-value">{{ $invoice->invoice_number }}</div>
                </div>
                <div class="meta-row">
                    <div class="meta-label">Date :</div>
                    <div class="meta-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</div>
                </div>
                <div class="meta-row">
                    <div class="meta-label">Patient Reg No :</div>
                    <div class="meta-value">{{ $invoice->reference_number ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div style="min-height: 300px;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">Description</th>
                        <th>HSN/SAC</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if($item->item_description)
                                <br><small>{{ $item->item_description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->item_code ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                    <!-- Fill empty rows if needed for layout height -->
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="text-right">Total</td>
                    <td class="text-right">{{ number_format($invoice->sub_total, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right">CGST Collected @ 2.5%</td>
                    <td class="text-right">{{ number_format($invoice->tax_total / 2, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right">SGST Collected @ 2.5%</td>
                    <td class="text-right">{{ number_format($invoice->tax_total / 2, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right">Grand Total</td>
                    <td class="text-right">{{ number_format($invoice->total, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <div style="padding: 10px; border-bottom: 1px solid #000;">
            <strong>Amount Charged (In Words):</strong> 
            <span style="text-transform: capitalize;">
                @if(class_exists('NumberFormatter'))
                    {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($invoice->total) }} Rupees Only
                @else
                    {{ $invoice->total }} Rupees Only
                @endif
            </span>
        </div>

        <!-- Acknowledgement -->
        <div class="ack-section">
            <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">Delivery Acknowledgement:</div>
            <p style="margin: 0 0 5px 0;">
                I acknowledge that on today’s date, I received the referenced product. I am satisfied with both workmanship and fit of my device and will schedule a return visit if I experience any problem with my device or if I have any questions regarding my service.
            </p>
            <p style="margin: 0 0 5px 0;">
                I understand that the custom components of my device are fully guaranteed under normal use for 90 days and that Biofix Healthcare Pvt Ltd will make any repairs to my device as needed and free of charge during the warranty period. I also understand that the prefabricated components of my device are fully guaranteed as per the manufacturer. I understand that this does not apply to change in my physical weight condition nor any other physiological changes that may occur, or to any alterations made by anyone other than Biofix Healthcare Pvt Ltd. In addition, Biofix Healthcare Pvt Ltd will not be responsible for abuse, neglect or normal wear and tear.
            </p>
            <p style="margin: 0 0 5px 0;">
                I acknowledge that I have received care and use guidelines as well as discussed the precautions and risks to this device (including skin inspection and care). Additionally we have discussed supplier standards.
            </p>
            <p style="margin: 10px 0 0 0; font-style: italic;">
                Certified that the particulars given above are true and correct and the amount represents the price actually charged from the buyer.
            </p>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box">
                Patient Signature
            </div>
            <div class="sig-box">
                Authorised Signature
            </div>
        </div>
    </div>
</body>
</html>
