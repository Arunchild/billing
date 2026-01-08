<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // For now, just redirect back with success
        // TODO: Create Purchase model and save data
        
        return redirect()->route('purchases.index')->with('success', 'Purchase order created successfully.');
    }

    public function show(string $id)
    {
        // TODO: Implement purchase order view
        return redirect()->route('purchases.index');
    }

    public function edit(string $id)
    {
        // TODO: Implement purchase order edit
        return redirect()->route('purchases.index');
    }

    public function update(Request $request, string $id)
    {
        // TODO: Implement purchase order update
        return redirect()->route('purchases.index')->with('success', 'Purchase order updated.');
    }

    public function destroy(string $id)
    {
        // TODO: Implement purchase order delete
        return redirect()->route('purchases.index')->with('success', 'Purchase order deleted.');
    }
}
