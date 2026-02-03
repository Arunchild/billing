<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditNote;
use App\Models\Supplier;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditNote::with('supplier');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('note_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $creditNotes = $query->latest()->paginate(20);
        return view('credit_notes.index', compact('creditNotes'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $nextId = CreditNote::max('id') + 1;
        $noteNumber = 'CN-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        return view('credit_notes.create', compact('suppliers', 'noteNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'note_date' => 'required|date',
            'note_number' => 'required|unique:credit_notes,note_number',
            'amount' => 'required|numeric',
            'reason' => 'nullable|string',
        ]);

        CreditNote::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Credit Note created successfully!']);
        }

        return redirect()->route('credit_notes.index')->with('success', 'Credit Note created successfully!');
    }

    public function edit(CreditNote $creditNote)
    {
        $suppliers = Supplier::all();
        return view('credit_notes.create', compact('suppliers', 'creditNote'));
    }

    public function update(Request $request, CreditNote $creditNote)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'note_date' => 'required|date',
            'amount' => 'required|numeric',
            'reason' => 'nullable|string',
        ]);

        $creditNote->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Credit Note updated successfully!']);
        }

        return redirect()->route('credit_notes.index')->with('success', 'Credit Note updated successfully!');
    }

    public function destroy(CreditNote $creditNote)
    {
        $creditNote->delete();
        return redirect()->route('credit_notes.index')->with('success', 'Credit Note deleted successfully!');
    }
}
