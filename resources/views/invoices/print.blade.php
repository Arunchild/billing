<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-name { font-size: 16pt; font-weight: bold; margin-bottom: 5px; }
        .company-contact { font-size: 9pt; margin: 3px 0; }
        .invoice-title { font-size: 14pt; font-weight: bold; margin: 15px 0; }
        .invoice-header { display: table; width: 100%; margintop: 15px; }
        .invoice-header > div { display: table-cell; width: 50%; vertical-align: top; }
        .invoice-header .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-section { margin-top: 10px; }
        .total-row { text-align: right; margin: 5px 0; }
        .grand-total { font-weight: bold; font-size: 12pt; }
        .terms { font-size: 8pt; margin-top: 20px; padding: 10px; border: 1px solid #000; }
        .signature-section { margin-top: 30px; display: table; width: 100%; }
        .signature { display: table-cell; width: 33%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
            Print Invoice
        </button>
    </div>

    <div class="header">
        <div class="company-name">BIOFIX HEALTHCARE PVT. LTD.</div>
        <div class="company-contact">27/18A, Sathia Complex, Sinclair Street, Marthandam (PO), PIN: 629165</div>
        <div class="company-contact">Ph No: 9442384497 | Web: www.biofixhealthcare.com</div>
    </div>

    <div class="invoice-title text-center">TAX INVOICE</div>

    <div class="invoice-header">
        <div>
            <strong>Mr./Mrs:</strong> {{ $customer->name }}<br>
            <strong>Billing Address:</strong><br>
            {{ $customer->address }}<br>
            {{ $customer->city }} - {{ $customer->pincode }}
        </div>
        <div class="right">
            <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $invoice->invoice_date }}<br>
            <strong>Patient Reg No:</strong> {{ $customer->reg_no }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%">Description</th>
                <th style="width: 15%" class="text-center">HSN/SAC</th>
                <th style="width: 10%" class="text-center">Quantity</th>
                <th style="width: 15%" class="text-right">Rate</th>
                <th style="width: 20%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-center">{{ $item->item_code ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">₹ {{ number_format($item->price, 2) }}</td>
                <td class="text-right">₹ {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" style="height: 100px;"></td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row"><strong>Total:</strong> ₹ {{ number_format($invoice->sub_total, 2) }}</div>
        <div class="total-row">CGST Collected @ 2.5%: ₹ {{ number_format($invoice->tax_total / 2, 2) }}</div>
        <div class="total-row">SGST Collected @ 2.5%: ₹ {{ number_format($invoice->tax_total / 2, 2) }}</div>
        <div class="total-row grand-total">Grand Total: ₹ {{ number_format($invoice->total, 2) }}</div>
        <div class="total-row"><strong>Amount Charged (In Words):</strong> {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($invoice->total)) }} Rupees Only</div>
    </div>

    <div class="terms">
        <strong>Delivery Acknowledgement:</strong><br>
        I acknowledge that on today's date, I received the referenced product. I am satisfied with both workmanship and fit of my device and will schedule a return visit if I experience any problem with my device or if I have any questions regarding my service.<br><br>
        
        I understand that the custom components of my device are fully guaranteed under normal use for 90 days and that Biofix Healthcare Pvt Ltd will make any repairs to my device as needed and free of charge during the warranty period. I also understand that the prefabricated components of my device are fully guaranteed as per the manufacturer. I understand that this does not apply to change in my physical weight condition nor any other physiological changes that may occur, or to any alterations made by anyone other than Biofix Healthcare Pvt Ltd. In addition, Biofix Healthcare Pvt Ltd will not be responsible for abuse, neglect or normal wear and tear.<br><br>
        
        I acknowledge that I have received care and use guidelines as well as discussed the precautions and risks to this device (including skin inspection and care). Additionally we have discussed supplier standards.<br><br>
        
        <em>Certified that the particulars given above are true and correct and the amount represents the price actually charged from the buyer</em>
    </div>

    <div class="signature-section">
        <div class="signature">
            <div class="signature-line">Patient Signature</div>
        </div>
        <div class="signature">
            <div class="signature-line">Authorised Signature</div>
        </div>
    </div>
</body>
</html>
