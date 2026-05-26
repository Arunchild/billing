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
            'permissions' => 'nullable|array',
        ]);

        // If login credentials provided, create a User account
        $userId = null;
        if (!empty($request->username) && !empty($request->password)) {
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $request->username, // Using username field as email for login
                'password' => bcrypt($request->password),
            ]);
            $userId = $user->id;
        }

        // Create the Staff record
        $staff = \App\Models\Staff::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'user_id' => $userId,
            'permissions' => $request->input('permissions', []),
        ]);

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
            'username' => 'nullable|string|unique:users,email,' . ($staff->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
            'permissions' => 'nullable|array',
        ]);

        // Handle User login account update or creation
        $userId = $staff->user_id;
        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $userUpdates = ['name' => $validated['name']];
                if (!empty($request->username)) {
                    $userUpdates['email'] = $request->username;
                }
                if (!empty($request->password)) {
                    $userUpdates['password'] = bcrypt($request->password);
                }
                $user->update($userUpdates);
            }
        } elseif (!empty($request->username) && !empty($request->password)) {
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $request->username,
                'password' => bcrypt($request->password),
            ]);
            $userId = $user->id;
        }

        // Update Staff details and permissions
        $staff->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'user_id' => $userId,
            'permissions' => $request->input('permissions', []),
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff details updated successfully.');
    }

    public function destroy(string $id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        $userId = $staff->user_id;
        
        $staff->delete();

        // Remove linked user login account
        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->delete();
            }
        }

        return redirect()->route('staff.index')->with('success', 'Staff member removed successfully.');
    }
}
