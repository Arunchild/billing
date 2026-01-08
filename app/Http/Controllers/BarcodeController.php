<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class BarcodeController extends Controller
{
    public function scanner()
    {
        return view('barcode.scanner');
    }

    public function lookup(Request $request)
    {
        $searchTerm = $request->barcode;
        
        // Search by barcode, phone, or reg_no
        $customer = Customer::where('barcode', $searchTerm)
            ->orWhere('phone', $searchTerm)
            ->orWhere('reg_no', $searchTerm)
            ->first();
        
        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }

    public function printLabel($id)
    {
        $customer = Customer::findOrFail($id);
        return view('barcode.label', compact('customer'));
    }
}
