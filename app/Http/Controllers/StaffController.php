<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = \App\Models\Staff::latest()->paginate(10);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'role' => 'nullable|string',
            'username' => 'nullable|string|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        // Create the Staff record
        $staff = \App\Models\Staff::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        // If login credentials provided, create a User account
        if (!empty($request->username) && !empty($request->password)) {
            \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $request->username, // Using username field as email for login
                'password' => bcrypt($request->password),
            ]);
        }

        return redirect()->route('staff.index')->with('success', 'Staff member and login access created successfully.');
    }

    public function edit(string $id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, string $id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'role' => 'nullable|string',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff details updated successfully.');
    }

    public function destroy(string $id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff member removed successfully.');
    }
}
