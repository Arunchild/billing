<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 14px; line-height: 1.5; color: #000; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        .no-print { margin-bottom: 20px; text-align: right; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; text-decoration: underline; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .table th { background-color: #f0f0f0; -webkit-print-color-adjust: exact; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .correspondence { margin-top: 20px; border: 1px solid #000; display: flex; }
        .corr-col { width: 50%; padding: 10px; }
        .corr-col.right { border-left: 1px solid #000; }
        
        .company-header { display: flex; margin-bottom: 20px; align-items: flex-start; }
        .logo-img { max-width: 150px; margin-right: 20px; }
        
        .payment-mode { margin-top: 20px; border: 1px solid #000; padding: 10px; }
        .validity { margin-top: 20px; font-weight: bold; }
        
        .footer { margin-top: 50px; text-align: right; }
        
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .container { max-width: 100%; width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; cursor: pointer; border-radius: 5px;">Print Quotation</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">Close</button>
    </div>

    <div class="container">
        <!-- Logo and Company Info Header for print -->
        <div class="company-header">
             <img src="{{ asset('images/biofix-logo.jpg') }}" alt="BIOFIX" class="logo-img">
             <div>
                <h2 style="margin: 00;">Biofix Healthcare Pvt. Ltd.</h2>
                <div>
                    27/18A, Sathia Complex, Sinclair Street,<br>
                    Marthandam (PO) - 629165<br>
                    Mobile: +91 9442384497<br>
                    E-Mail: support@biofixhealthcare.com
                </div>
             </div>
        </div>

        <div class="header">
            <h1>Assistive Technology Service Proposal</h1>
        </div>

        <!-- Meta Info -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <div><strong>Quotation Reference Number:</strong> {{ $quotation->quotation_number }}</div>
            <div><strong>Date :</strong> {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d-m-Y') }}</div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60%;">Description</th>
                    <th style="width: 10%;">Unit</th>
                    <th style="width: 30%;" class="text-right">Sub Total (INR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" style="background: #fafafa; font-weight: bold;">Provision For</td>
                </tr>
                @foreach($quotation->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                
                <tr>
                    <td><strong>Taxes & Others</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>CGST @ 2.5%</td>
                    <td></td>
                    <td class="text-right">{{ number_format($quotation->tax_total / 2, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST @ 2.5%</td>
                    <td></td>
                    <td class="text-right">{{ number_format($quotation->tax_total / 2, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right" colspan="2"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($quotation->total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div>
            <strong>Grand Total (In Words) -</strong> <span style="text-transform: capitalize;">
                @if(class_exists('NumberFormatter'))
                    {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($quotation->total) }} Rupees Only
                @else
                    {{ $quotation->total }} Rupees Only
                @endif
            </span>
        </div>

        <div class="correspondence">
            <div class="corr-col">
                <strong>Correspondence : Client</strong><br><br>
                <strong>{{ $quotation->customer->name }}</strong><br>
                {!! nl2br(e($quotation->customer->address)) !!}<br>
                Ph: {{ $quotation->customer->phone }}
            </div>
            <div class="corr-col right">
                <strong>Correspondence : Service Provider</strong><br><br>
                <strong>Biofix Healthcare</strong>, 27/18A,<br>
                Sathia Complex, Sinclair Street, Marthandam (PO)<br>
                PIN : 629165<br>
                Mobile : +91 9442384497<br>
                E-Mail : support@biofixhealthcare.com
            </div>
        </div>

        <div class="payment-mode">
            <strong>Payment Mode</strong><br>
            <ul>
                <li>Cheque / DD Payable to “Biofix Healthcare Pvt. Ltd.”</li>
                <li>Master / Visa credit or Debit</li>
                <li>Direct Cash Payment</li>
                <li>NEFT/RTGS : Biofix Healthcare Pvt. Ltd, A/C no: 8098880196, Indian Bank, Marthandam Branch.<br>
                    IFSC Code: IDIB000M218</li>
            </ul>
        </div>
        
        <div class="validity">
            Validity: <span style="font-weight: normal;">The Proposal is valid for 30 days from the date of issue.</span>
        </div>
        
        <div style="margin-top: 20px;">
            Thank you for enquiring with us.
        </div>
        
        <div class="footer">
            <p>BIOFIX Authorized Signature:______________________</p>
        </div>
    </div>
</body>
</html>
