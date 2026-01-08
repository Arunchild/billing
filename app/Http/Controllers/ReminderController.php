<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index()
    {
        return view('reminders.index');
    }

    public function create()
    {
        return view('reminders.create');
    }

    public function store(Request $request)
    {
        // TODO: Create Reminder model and save
        return redirect()->route('dashboard')->with('success', 'Reminder set successfully!');
    }

    public function show(string $id)
    {
        return redirect()->route('reminders.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('reminders.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('reminders.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('reminders.index');
    }
}
