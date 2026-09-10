{{--
    Remarks repeater (Date | Purpose | Solution).
    Optional $remarks: existing rows to pre-fill. Omit it and call loadRemarks(rows) from JS
    (the customer list modal does this when opening an existing customer).
--}}
<hr class="my-3">

<div class="d-flex justify-content-between align-items-center mb-2">
    <label class="form-label mb-0 fw-bold text-primary"><i class="fas fa-comment-medical me-1"></i> Remarks</label>
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRemarkRow()">
        <i class="fas fa-plus"></i> Add Remark
    </button>
</div>

<input type="hidden" name="remarks_submitted" value="1">

<div class="table-responsive mb-3">
    <table class="table table-sm align-middle mb-0 remarks-table">
        <thead class="table-light">
            <tr>
                <th style="width: 150px;">Date</th>
                <th>Purpose</th>
                <th>Solution</th>
                <th style="width: 40px;"></th>
            </tr>
        </thead>
        <tbody id="remarksBody"></tbody>
    </table>
    <div class="text-muted small py-2" id="remarksEmpty">No remarks yet. Click "Add Remark" to record one.</div>
</div>

<style>
    .remarks-table th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.4px; color: #64748b; }
    .remarks-table textarea { resize: vertical; }
</style>

@push('scripts')
<script>
    let remarkIndex = 0;

    function escapeRemarkHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function toggleRemarksEmpty() {
        $('#remarksEmpty').toggle($('#remarksBody tr').length === 0);
    }

    function addRemarkRow(remark) {
        remark = remark || {};
        const i = remarkIndex++;
        const today = new Date().toISOString().slice(0, 10);
        const date = remark.remark_date ? String(remark.remark_date).slice(0, 10) : today;

        $('#remarksBody').append(
            '<tr>' +
                '<td class="align-top">' +
                    '<input type="hidden" name="remarks[' + i + '][id]" value="' + escapeRemarkHtml(remark.id || '') + '">' +
                    '<input type="date" name="remarks[' + i + '][remark_date]" class="form-control form-control-sm" value="' + escapeRemarkHtml(date) + '">' +
                '</td>' +
                '<td><textarea name="remarks[' + i + '][purpose]" class="form-control form-control-sm" rows="2" placeholder="Why they came in">' + escapeRemarkHtml(remark.purpose) + '</textarea></td>' +
                '<td><textarea name="remarks[' + i + '][solution]" class="form-control form-control-sm" rows="2" placeholder="What was done">' + escapeRemarkHtml(remark.solution) + '</textarea></td>' +
                '<td class="text-end align-top">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRemarkRow(this)" title="Remove"><i class="fas fa-times"></i></button>' +
                '</td>' +
            '</tr>'
        );
        toggleRemarksEmpty();
    }

    function removeRemarkRow(btn) {
        $(btn).closest('tr').remove();
        toggleRemarksEmpty();
    }

    function loadRemarks(remarks) {
        $('#remarksBody').empty();
        remarkIndex = 0;
        (remarks || []).forEach(function(remark) { addRemarkRow(remark); });
        toggleRemarksEmpty();
    }

    $(function() {
        loadRemarks(@json(isset($remarks) ? $remarks : []));
    });
</script>
@endpush
