<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
        public function index()
    {
        // Calculate dynamic stats
        $grossSale = \App\Models\Invoice::sum('total');
        $invoiceCount = \App\Models\Invoice::count();
        $amountReceived = \App\Models\Invoice::where('status', 'paid')->sum('total');
        $amountPending = \App\Models\Invoice::where('status', '!=', 'paid')->sum('total'); // Simple approximation

        // Vital Stats
        $overdueInvoices = \App\Models\Invoice::where('due_date', '<', now())->where('status', '!=', 'paid')->count();
        $unpaidInvoices = \App\Models\Invoice::where('status', '!=', 'paid')->count();

        // Chart Data (Last 30 Days)
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $chartData = \App\Models\Invoice::selectRaw('DATE(invoice_date) as date, SUM(total) as total')
            ->where('invoice_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->pluck('total', 'date');
            
        return view('dashboard.index', compact(
            'grossSale', 
            'invoiceCount', 
            'amountReceived', 
            'amountPending',
            'overdueInvoices',
            'unpaidInvoices'
        ));
    }
}
