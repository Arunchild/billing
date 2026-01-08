<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = \App\Models\Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:M,F,Other',
            'city' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'pincode' => 'nullable|string',
        ]);

        // Auto-generate reg_no and barcode
        $validated['reg_no'] = \App\Models\Customer::generateRegNo();
        $validated['barcode'] = \App\Models\Customer::generateBarcode();

        \App\Models\Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully with barcode.');
    }

    public function show(string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit(string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:M,F,Other',
            'city' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'pincode' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
