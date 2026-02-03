<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::latest()->paginate(20);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'unit' => 'required|string',
        ]);

        $data = $request->except('_token');
        
        // Set current_stock to opening_stock initially
        $data['current_stock'] = $request->opening_stock ?? 0;
        
        // Convert checkbox values
        $data['print_description'] = $request->has('print_description');
        $data['print_serial_no'] = $request->has('print_serial_no');
        $data['one_click_sale'] = $request->has('one_click_sale');
        $data['not_for_sale'] = $request->has('not_for_sale');
        $data['enable_tracking'] = $request->has('enable_tracking');

        if ($request->wantsJson() || $request->ajax()) {
            $product = \App\Models\Product::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully!',
                'redirect' => route('products.index'),
                'product' => $product
            ]);
        }

        \App\Models\Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'unit' => 'required|string',
        ]);

        $data = $request->except('_token', '_method');
        
        // Convert checkbox values
        $data['print_description'] = $request->has('print_description');
        $data['print_serial_no'] = $request->has('print_serial_no');
        $data['one_click_sale'] = $request->has('one_click_sale');
        $data['not_for_sale'] = $request->has('not_for_sale');
        $data['enable_tracking'] = $request->has('enable_tracking');

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
