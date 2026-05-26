<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductGroup;

class ProductGroupController extends Controller
{
    public function index()
    {
        return response()->json(ProductGroup::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_groups,name',
        ]);

        $group = ProductGroup::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Group added successfully!',
            'group' => $group
        ]);
    }

    public function destroy($id)
    {
        $group = ProductGroup::findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Group removed successfully!'
        ]);
    }
}
