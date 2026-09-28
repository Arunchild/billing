{{--
    Title (Mr / Mrs / ...) dropdown. Optional $selected, $id.
    Picking a title fills an empty gender select in the same form.
--}}
@php
    $titleGender = ['Mr' => 'M', 'Master' => 'M', 'Mrs' => 'F', 'Ms' => 'F', 'Miss' => 'F'];
@endphp
<select name="title" class="form-select" @isset($id) id="{{ $id }}" @endisset>
    <option value="">-</option>
    @foreach(\App\Models\Customer::TITLES as $value => $label)
        <option value="{{ $value }}" data-gender="{{ $titleGender[$value] ?? '' }}" {{ ($selected ?? null) === $value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>

@once
@push('scripts')
<script>
    $(document).on('change', 'select[name="title"]', function () {
        const gender = $(this).find(':selected').data('gender');
        const genderSelect = $(this).closest('form').find('select[name="gender"]');
        if (gender && !genderSelect.val()) {
            genderSelect.val(gender);
        }
    });
</script>
@endpush
@endonce
