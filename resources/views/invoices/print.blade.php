<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; line-height: 1.4; color: #000; background: #fff; margin: 0; }
        .container { width: 100%; max-width: 900px; margin: 0 auto; border: 1px solid #000; box-sizing: border-box; }
        .no-print { margin-bottom: 20px; text-align: right; padding: 10px; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }
        
        /* Header */
        .header-section { display: flex; border-bottom: 1px solid #000; }
        .logo-box { width: 20%; padding: 10px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #000; }
        .company-box { width: 50%; padding: 10px; text-align: center; border-right: 1px solid #000; }
        .company-box h2 { margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .company-box .address { font-size: 13px; margin-top: 5px; }
        .gst-box { width: 30%; padding: 10px; display: flex; flex-direction: column; justify-content: center; }
        
        /* Title Bar */
        .invoice-title-bar {
            background: #333; 
            color: #fff; 
            display: flex; 
            border-bottom: 1px solid #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .title-left { width: 30%; }
        .title-center { width: 40%; text-align: center; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; letter-spacing: 1px; }
        .title-right { width: 30%; padding: 5px; font-size: 11px; display: flex; flex-direction: column; justify-content: center; }
        
        /* Billing Section */
        
        .billing-grid { display: flex; border-bottom: 1px solid #000; }
        .billing-address-box { width: 50%; padding: 5px; border-right: 1px solid #000; }
        .billing-meta-box { width: 50%; padding: 5px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 3px; }
        
        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 8px; font-size: 13px; font-weight: normal; }
        .items-table th:last-child { border-right: none; }
        
        .items-table td { border-right: 1px solid #000; padding: 5px 8px; vertical-align: top; }
        .items-table td:last-child { border-right: none; }
        
        .bottom-totals td { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 8px; }
        
        /* Footer Boxes */
        .footer-boxes { display: flex; }
        .left-footer { width: 50%; border-right: 1px solid #000; display: flex; flex-direction: column; }
        .right-footer { width: 50%; padding: 10px; font-size: 11px; text-align: justify; line-height: 1.4; }
        
        .amount-words-box { padding: 10px; border-bottom: 1px solid #000; min-height: 60px; }
        .certified-box { padding: 10px; border-bottom: 1px solid #000; font-size: 11px; text-align: center; display: flex; align-items: center; justify-content: center; min-height: 50px; }
        .signatures-box { display: flex; flex-grow: 1; min-height: 80px; align-items: flex-end; }
        .sig-patient { width: 50%; text-align: center; padding-bottom: 5px; border-right: 1px solid #000; height: 100%; position: relative; }
        .sig-auth { width: 50%; text-align: center; padding-bottom: 5px; height: 100%; position: relative; }
        .sig-text { position: absolute; bottom: 5px; width: 100%; }

        @media print {
            .no-print { display: none; }
            @page { margin: 5mm; }
            body { padding: 0; }
            .container { border: 2px solid #000; box-sizing: border-box; }
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
                <img src="{{ asset('images/biofix-logo.jpg') }}" alt="BIOFIX" style="max-height: 80px; max-width: 100%;">
            </div>
            <div class="company-box">
                <h2>BIOFIX HEALTHCARE PVT. LTD.</h2>
                <div class="address">
                    27/18A,<br>
                    Sathia Complex, Sinclair Street, Marthandam (PO)<br>
                    <u>PIN</u> : 629165<br>
                </div>
                <div style="margin-top: 5px; font-size: 13px;">
                    Ph No: 9442384497 &nbsp;&nbsp; Web: www.biofixhealthcare.com
                </div>
            </div>
            <div class="gst-box">
                <div style="margin-bottom: 15px;">GSTIN: 33AANCB5605Q1Z9</div>
                <div>PAN: AANCB5605Q</div>
            </div>
        </div>

        <!-- Title Bar -->
        <div class="invoice-title-bar">
            <div class="title-left"></div>
            <div class="title-center">TAX INVOICE</div>
            <div class="title-right">
                <div style="margin-bottom: 3px;"><span style="font-size: 14px; margin-right: 5px; vertical-align: middle;">&#9744;</span>Original-Buyers Copy</div>
                <div><span style="font-size: 14px; margin-right: 5px; vertical-align: middle;">&#9744;</span>Duplicate Copy</div>
            </div>
        </div>


        <div class="billing-grid">
            <div class="billing-address-box">
                <div>Billing Address:</div>
                <div style="margin-top: 10px; padding-left: 10px;">
                    <strong>{{ $invoice->customer_name ?? $invoice->customer->name }}</strong><br>
                    {!! nl2br(e($invoice->customer_address ?? $invoice->customer->address)) !!}<br>
                    {{ $invoice->customer->city ?? '' }} - {{ $invoice->customer->pincode ?? '' }}<br>
                    Ph: {{ $invoice->customer->phone ?? '' }}
                </div>
            </div>
            <div class="billing-meta-box">
                <table class="meta-table">
                    <tr>
                        <td style="width: 35%;">Invoice Number</td>
                        <td style="width: 5%;">:</td>
                        <td>{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td>Patient Reg No</td>
                        <td>:</td>
                        <td>{{ $invoice->reference_number ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 38%;">Description</th>
                    <th style="width: 15%;">HSN/SAC</th>
                    <th style="width: 12%;">Quantity</th>
                    <th style="width: 15%;">Rate</th>
                    <th style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->item_description)
                            <br><small>{!! $item->item_description !!}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->item_code ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                
                <!-- Spacer Row to push totals down -->
                <tr>
                    <td style="height: 150px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- Freight Row inside values area -->
                <tr>
                    <td style="text-align: center; padding-bottom: 20px;">
                        <span style="display: inline-block; border: 1px solid #000; padding: 5px 40px; background: #fff; color: #000; font-weight: bold; font-size: 13px;">FREIGHT</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Totals -->
                <tr class="bottom-totals">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($invoice->sub_total, 2) }}</td>
                </tr>
                <tr class="bottom-totals">
                    <td colspan="4" class="text-right">CGST Collected @ 2.5%</td>
                    <td class="text-right">{{ number_format($invoice->tax_total / 2, 2) }}</td>
                </tr>
                <tr class="bottom-totals">
                    <td colspan="4" class="text-right">SGST Collected @ 2.5%</td>
                    <td class="text-right">{{ number_format($invoice->tax_total / 2, 2) }}</td>
                </tr>
                <tr class="bottom-totals">
                    <td colspan="4" class="text-right">Grand Total</td>
                    <td class="text-right">{{ number_format($invoice->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer-boxes">
            <div class="left-footer">
                <div class="amount-words-box">
                    Amount Charged (In Words)<br><br>
                    <span style="text-transform: capitalize;">
                        @if(class_exists('NumberFormatter'))
                            {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($invoice->total) }} Rupees Only
                        @else
                            {{ $invoice->total }} Rupees Only
                        @endif
                    </span>
                </div>
                <div class="certified-box">
                    Certified that the particulars given above are true and correct and the amount represents the price actually charged from the buyer
                </div>
                <div class="signatures-box">
                    <div class="sig-patient">
                        <div class="sig-text">Patient Signature</div>
                    </div>
                    <div class="sig-auth">
                        <div class="sig-text">Authorised Signature</div>
                    </div>
                </div>
            </div>
            <div class="right-footer">
                <div>Delivery Acknowledgement:</div><br>
                <p style="margin: 0 0 10px 0;">
                    I acknowledge that on today’s date, I received the referenced product. I am satisfied with both workmanship and fit of my device and will schedule a return visit if I experience any problem with my device or if I have any questions regarding my service.
                </p>
                <p style="margin: 0 0 10px 0;">
                    I understand that the custom components of my device are fully guaranteed under normal use for 90 days and that Biofix Healthcare Pvt Ltd will make any repairs to my device as needed and free of charge during the warranty period. I also understand that the prefabricated components of my device are fully guaranteed as per the manufacturer. I understand that this does not apply to change in my physical weight condition nor any other physiological changes that may occur, or to any alterations made by anyone other than Biofix Healthcare Pvt Ltd. In addition, Biofix Healthcare Pvt Ltd will not be responsible for abuse, neglect or normal wear and tear.
                </p>
                <p style="margin: 0;">
                    I acknowledge that I have received care and use guidelines as well as discussed the precautions and risks to this device (including skin inspection and care). Additionally we have discussed supplier standards.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
