@extends('layouts.app')

@section('content')
<div class="card mb-3 animate__animated animate__fadeIn">
    <div class="card-body p-2">
        <form action="{{ route('debit_notes.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search Note # or Supplier..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="col-md-2 text-end ms-auto">
                <button type="button" class="btn btn-primary btn-sm w-100" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> New Debit Note
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 400px; padding-bottom: 150px;">
            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-2">Note #</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Amount</th>
                        <th class="py-2">Reason</th>
                        <th class="py-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debitNotes as $note)
                    <tr>
                        <td class="fw-bold text-primary">{{ $note->note_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($note->note_date)->format('d-M-Y') }}</td>
                        <td class="fw-bold">{{ $note->supplier->name }}</td>
                        <td class="fw-bold">{{ number_format($note->amount, 2) }}</td>
                        <td>{{ Str::limit($note->reason, 30) }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" onclick="openEditModal({{ $note }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('debit_notes.destroy', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('form').submit()"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3 opacity-50"></i>
                                <p>No debit notes found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $debitNotes->withQueryString()->links() }}
    </div>
</div>

<!-- Debit Note Modal -->
<div class="modal fade" id="debitNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="debitNoteModalTitle">Create Debit Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="debitNoteForm" method="POST">
                    @csrf
                    <div id="methodField"></div>

                    <div class="row mb-3">
                         <div class="col-md-6">
                            <label class="form-label">Note #</label>
                            <input type="text" name="note_number" id="noteNumber" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                             <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="note_date" id="noteDate" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplierSelect" class="form-select select2-modal" required style="width: 100%;">
                            <option value="">Select Supplier</option>
                            @foreach(\App\Models\Supplier::all() as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                         <label class="form-label">Amount <span class="text-danger">*</span></label>
                         <input type="number" name="amount" id="noteAmount" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason / Notes</label>
                        <textarea name="reason" id="noteReason" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveNoteBtn">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple ID generator for client-side display before save
    function generateNoteId() {
        return 'DN-' + Date.now().toString().slice(-6); 
    }

    function openCreateModal() {
        $('#debitNoteModalTitle').text('Create Debit Note');
        $('#debitNoteForm').attr('action', '{{ route('debit_notes.store') }}');
        $('#methodField').html('');
        
        $('#noteNumber').val('AUTO-GEN'); // Helper placeholder, backend generates real one
        $('#noteDate').val(new Date().toISOString().split('T')[0]);
        $('#supplierSelect').val('').trigger('change');
        $('#noteAmount').val('');
        $('#noteReason').val('');
        
        $('#debitNoteModal').modal('show');
    }

    function openEditModal(note) {
        $('#debitNoteModalTitle').text('Edit Debit Note');
        $('#debitNoteForm').attr('action', '/debit_notes/' + note.id);
        $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
        
        $('#noteNumber').val(note.note_number);
        $('#noteDate').val(note.note_date);
        $('#supplierSelect').val(note.supplier_id).trigger('change');
        $('#noteAmount').val(note.amount);
        $('#noteReason').val(note.reason);
        
        $('#debitNoteModal').modal('show');
    }

    $(document).ready(function() {
        $('.select2-modal').select2({
            dropdownParent: $('#debitNoteModal'), // Important for modal
            theme: 'bootstrap-5'
        });

        // Ajax handling
         $('#debitNoteForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveNoteBtn');
            const form = $(this);
            
            btn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 500);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error saving note. Please check fields.');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Note');
                }
            });
        });
    });
</script>
@endpush
@endsection
