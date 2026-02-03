<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DebitNote;
use App\Models\Supplier;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = DebitNote::with('supplier');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('note_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $debitNotes = $query->latest()->paginate(20);
        return view('debit_notes.index', compact('debitNotes'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $nextId = DebitNote::max('id') + 1;
        $noteNumber = 'DN-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        return view('debit_notes.create', compact('suppliers', 'noteNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'note_date' => 'required|date',
            'note_number' => 'required|unique:debit_notes,note_number',
            'amount' => 'required|numeric',
            'reason' => 'nullable|string',
        ]);

        DebitNote::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Debit Note created successfully!']);
        }

        return redirect()->route('debit_notes.index')->with('success', 'Debit Note created successfully!');
    }

    public function edit(DebitNote $debitNote)
    {
        $suppliers = Supplier::all();
        // Return view or JSON depending on need, but for modal we might just pass data via JS attribute in Index
        return view('debit_notes.create', compact('suppliers', 'debitNote'));
    }

    public function update(Request $request, DebitNote $debitNote)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'note_date' => 'required|date',
            'amount' => 'required|numeric',
            'reason' => 'nullable|string',
        ]);

        $debitNote->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Debit Note updated successfully!']);
        }

        return redirect()->route('debit_notes.index')->with('success', 'Debit Note updated successfully!');
    }

    public function destroy(DebitNote $debitNote)
    {
        $debitNote->delete();
        return redirect()->route('debit_notes.index')->with('success', 'Debit Note deleted successfully!');
    }
}
