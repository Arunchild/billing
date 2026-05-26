<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Product;
use App\Models\Expense;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Generate list of last 12 months for the recurring select options
        $monthsList = [];
        $current = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthsList[] = [
                'value' => $current->format('Y-m'),
                'label' => $current->format('F Y'),
                'month' => $current->month,
                'year' => $current->year
            ];
            $current->subMonth();
        }

        // Generate list of last 5 years
        $yearsList = [];
        $currentYear = Carbon::now()->year;
        for ($i = 0; $i < 5; $i++) {
            $yearsList[] = $currentYear - $i;
        }

        return view('reports.index', compact('monthsList', 'yearsList'));
    }

    public function export(Request $request)
    {
        $reportType = $request->input('report_type', 'invoices');
        $filterType = $request->input('filter_type', 'range');
        
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $reportDate = $request->input('report_date');
        $reportMonth = $request->input('report_month'); // e.g. "05" or "2026-05"
        $reportYear = $request->input('report_year');

        // Parse recurring month value (format YYYY-MM) if selected
        if ($filterType == 'recurring') {
            $recurringMonth = $request->input('recurring_month');
            if ($recurringMonth) {
                $parts = explode('-', $recurringMonth);
                $reportYear = $parts[0];
                $reportMonth = $parts[1];
                $filterType = 'month';
            } else {
                $filterType = 'range'; // fallback
            }
        }

        // Build query based on report type
        switch ($reportType) {
            case 'invoices':
                $query = Invoice::with('customer');
                $dateColumn = 'invoice_date';
                break;
            case 'quotations':
                $query = Quotation::with('customer');
                $dateColumn = 'quotation_date';
                break;
            case 'expenses':
                $query = Expense::query();
                $dateColumn = 'date';
                break;
            case 'inventory':
                $query = Product::query();
                $dateColumn = 'created_at';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type selected.');
        }

        // Apply filters
        if ($reportType !== 'inventory' || ($reportType == 'inventory' && ($request->filled('apply_date_filter') || ($fromDate && $toDate)))) {
            if ($filterType == 'date' && $reportDate) {
                $query->where($dateColumn, $reportDate);
            } elseif ($filterType == 'month' && $reportMonth && $reportYear) {
                $query->whereYear($dateColumn, $reportYear)
                      ->whereMonth($dateColumn, $reportMonth);
            } elseif ($filterType == 'year' && $reportYear) {
                $query->whereYear($dateColumn, $reportYear);
            } elseif ($filterType == 'range' && $fromDate && $toDate) {
                $query->whereBetween($dateColumn, [$fromDate, $toDate]);
            }
        }

        // Apply search keyword filter if present
        if ($request->filled('search')) {
            $search = $request->input('search');
            switch ($reportType) {
                case 'invoices':
                    $query->where(function($q) use ($search) {
                        $q->where('invoice_number', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                    });
                    break;
                case 'quotations':
                    $query->where(function($q) use ($search) {
                        $q->where('quotation_number', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                    });
                    break;
                case 'expenses':
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                    break;
                case 'inventory':
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('print_name', 'like', "%{$search}%")
                          ->orWhere('item_code', 'like', "%{$search}%")
                          ->orWhere('group', 'like', "%{$search}%");
                    });
                    break;
            }
        }

        $filename = $reportType . '_report_' . Carbon::now()->format('Ymd_His') . '.csv';
        $stream = fopen('php://temp', 'w+');

        // Generate content based on report type
        if ($reportType == 'invoices') {
            $headers = ['S. No.', 'Invoice Number', 'Date', 'Customer Name', 'Contact No.', 'Type', 'Subtotal (₹)', 'Tax (₹)', 'Discount (₹)', 'Total (₹)', 'Status'];
            fputcsv($stream, $headers);

            $invoices = $query->latest()->get();
            foreach ($invoices as $index => $invoice) {
                fputcsv($stream, [
                    $index + 1,
                    $invoice->invoice_number,
                    Carbon::parse($invoice->invoice_date)->format('d-M-Y'),
                    $invoice->customer ? $invoice->customer->name : ($invoice->customer_name ?? 'N/A'),
                    $invoice->customer ? $invoice->customer->phone : ($invoice->contact_no ?? '-'),
                    strtoupper($invoice->type ?? 'GST'),
                    number_format($invoice->sub_total, 2, '.', ''),
                    number_format($invoice->tax_total, 2, '.', ''),
                    number_format($invoice->discount, 2, '.', ''),
                    number_format($invoice->total, 2, '.', ''),
                    strtoupper($invoice->status)
                ]);
            }
        } elseif ($reportType == 'quotations') {
            $headers = ['S. No.', 'Quotation Number', 'Date', 'Customer Name', 'Contact No.', 'Subtotal (₹)', 'Tax (₹)', 'Discount (₹)', 'Total (₹)', 'Status'];
            fputcsv($stream, $headers);

            $quotations = $query->latest()->get();
            foreach ($quotations as $index => $quotation) {
                fputcsv($stream, [
                    $index + 1,
                    $quotation->quotation_number,
                    Carbon::parse($quotation->quotation_date)->format('d-M-Y'),
                    $quotation->customer ? $quotation->customer->name : ($quotation->customer_name ?? 'N/A'),
                    $quotation->customer ? $quotation->customer->phone : ($quotation->contact_no ?? '-'),
                    number_format($quotation->sub_total, 2, '.', ''),
                    number_format($quotation->tax_total, 2, '.', ''),
                    number_format($quotation->discount, 2, '.', ''),
                    number_format($quotation->total, 2, '.', ''),
                    strtoupper($quotation->status ?? 'ACTIVE')
                ]);
            }
        } elseif ($reportType == 'expenses') {
            $headers = ['S. No.', 'Date', 'Expense Title', 'Amount (₹)', 'Description'];
            fputcsv($stream, $headers);

            $expenses = $query->latest()->get();
            foreach ($expenses as $index => $expense) {
                fputcsv($stream, [
                    $index + 1,
                    Carbon::parse($expense->date)->format('d-M-Y'),
                    $expense->name,
                    number_format($expense->amount, 2, '.', ''),
                    $expense->description ?? '-'
                ]);
            }
        } elseif ($reportType == 'inventory') {
            $headers = ['S. No.', 'Product Group', 'Print Name', 'Item Code', 'Purchase Price (₹)', 'Sale Price (₹)', 'Current Stock', 'Stock Value (₹)'];
            fputcsv($stream, $headers);

            $products = $query->latest()->get();
            foreach ($products as $index => $product) {
                $stockValue = $product->current_stock * ($product->purchase_price ?? 0);
                fputcsv($stream, [
                    $index + 1,
                    $product->group ?? 'General',
                    $product->print_name ?? $product->name,
                    $product->item_code ?? '-',
                    number_format($product->purchase_price, 2, '.', ''),
                    number_format($product->sale_price, 2, '.', ''),
                    $product->current_stock,
                    number_format($stockValue, 2, '.', '')
                ]);
            }
        }

        rewind($stream);
        $csvContent = "\xEF\xBB\xBF" . stream_get_contents($stream);
        fclose($stream);

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
