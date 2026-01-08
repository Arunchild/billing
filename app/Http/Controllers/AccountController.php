<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = \App\Models\Account::with('transactions')->get();
        $totalBalance = $accounts->sum('current_balance');
        
        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,card',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['current_balance'] = $validated['opening_balance'];
        
        \App\Models\Account::create($validated);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function show(string $id)
    {
        $account = \App\Models\Account::with(['transactions' => function($q) {
            $q->orderBy('transaction_date', 'desc')->limit(50);
        }])->findOrFail($id);
        
        return view('accounts.show', compact('account'));
    }

    public function edit(string $id)
    {
        $account = \App\Models\Account::findOrFail($id);
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, string $id)
    {
        $account = \App\Models\Account::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,card',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(string $id)
    {
        $account = \App\Models\Account::findOrFail($id);
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }

    public function statements()
    {
        $accounts = \App\Models\Account::where('is_active', true)->get();
        $recentTransactions = \App\Models\Transaction::with('account')
            ->orderBy('transaction_date', 'desc')
            ->limit(20)
            ->get();
        
        // Calculate totals
        $totalIncome = \App\Models\Transaction::where('type', 'credit')->sum('amount');
        $totalExpenses = \App\Models\Transaction::where('type', 'debit')->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;
        
        return view('accounts.statements', compact('accounts', 'recentTransactions', 'totalIncome', 'totalExpenses', 'netBalance'));
    }
}
