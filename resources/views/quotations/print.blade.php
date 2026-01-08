<!DOCTYPE html>
<html>
<head>
    <title>Service Quotation</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-name { font-size: 16pt; font-weight: bold; margin-bottom: 5px; }
        .title { font-size: 14pt; font-weight: bold; margin: 20px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .info-section { margin: 20px 0; }
        .info-row { margin: 8px 0; }
        .payment-box { border: 1px solid #000; padding: 15px; margin: 20px 0; }
        .validity-box { border: 1px solid #000; padding: 15px; margin: 20px 0; }
        .signature { margin-top: 60px; text-align: right; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
            Print Quotation
        </button>
    </div>

    <div class="header">
        <div class="company-name">BIOFIX HEALTHCARE PVT. LTD.</div>
        <div style="font-size: 9pt;">27/18A, Sathia Complex, Sinclair Street, Marthandam (PO), PIN: 629165</div>
        <div style="font-size: 9pt;">Mobile: +91 9442384497 | Email: info@biofixhealthcare.com</div>
    </div>

    <div class="title">Assistive Technology Service Proposal</div>

    <div class="info-section">
        <div class="info-row"><strong>Date:</strong> {{ date('d/m/Y') }}</div>
        <div class="info-row"><strong>Quotation Reference Number:</strong> QT-{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}</div>
    </div>

    <table style="margin-bottom: 0;">
        <thead>
            <tr>
                <th style="width: 60%">Description</th>
                <th style="width: 15%" class="text-right">Unit</th>
                <th style="width: 25%" class="text-right">Sub Total (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3"><strong>Provision For</strong></td>
            </tr>
            <tr>
                <td>Item Description Here</td>
                <td class="text-right">1</td>
                <td class="text-right">0.00</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 200px;"></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Taxes & Others</strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">CGST @ 2.5%</td>
                <td class="text-right">0.00</td>
            </tr>
            <tr>
                <td colspan="2">SGST @ 2.5%</td>
                <td class="text-right">0.00</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Grand Total (In Words)</strong></td>
                <td class="text-right"><strong>₹ 0.00</strong></td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 20px;">
        <tr>
            <th style="width: 50%">Correspondence: Client</th>
            <th style="width: 50%">Service Provider</th>
        </tr>
        <tr>
            <td style="vertical-align: top; height: 80px;">
                <strong>Name:</strong><br>
                <strong>Address:</strong><br>
            </td>
            <td style="vertical-align: top;">
                <strong>Biofix Healthcare</strong><br>
                27/18A, Sathia Complex<br>
                Sinclair Street, Marthandam (PO)<br>
                PIN: 629165<br>
                <strong>Mobile:</strong> +91 9442384497<br>
                <strong>E-Mail:</strong> info@biofixhealthcare.com
            </td>
        </tr>
    </table>

    <div class="payment-box">
        <strong>Payment Mode</strong><br><br>
        • Cheque / DD Payable to "Biofix Healthcare Pvt. Ltd."<br>
        • Master / Visa credit or Debit<br>
        • Direct Cash Payment<br>
        • NEFT/RTGS: Biofix Healthcare Pvt. Ltd<br>
        &nbsp;&nbsp;&nbsp;A/C no: 8098880196, Indian Bank, Marthandam Branch<br>
        &nbsp;&nbsp;&nbsp;IFSC Code: IDIB000M218
    </div>

    <div class="validity-box">
        <strong>Validity</strong><br><br>
        The Proposal is valid for 30 days from the date of issue.<br><br>
        <em>Thank you for enquiring with us.</em>
    </div>

    <div class="signature">
        BIOFIX Authorized Signature: ______________________
    </div>
</body>
</html>
