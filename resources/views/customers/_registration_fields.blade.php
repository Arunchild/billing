{{--
    Extra fields from the paper registration form (alternate phone, body stats,
    medical, employment, referral). All optional.
    Optional $customer: record to pre-fill from. The customer list modal omits it
    and fills the fields from JS by their name attribute instead.
--}}
@php
    $customer = $customer ?? null;
    $val = fn ($field) => old($field, $customer?->$field);
    $yesNo = function ($field) use ($val) {
        $v = $val($field);
        return ($v === null || $v === '') ? '' : (string) (int) (bool) $v;
    };
@endphp

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label">Alternate Phone</label>
        <input type="tel" name="alternate_phone" class="form-control" value="{{ $val('alternate_phone') }}"
               inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}"
               title="10-digit Indian mobile number starting with 6-9">
    </div>
    <div class="col-md-4">
        <label class="form-label">Weight (kg)</label>
        <input type="number" name="weight" class="form-control" value="{{ $val('weight') }}" min="1" max="300" step="0.1">
    </div>
    <div class="col-md-4">
        <label class="form-label">Height (cm)</label>
        <input type="number" name="height" class="form-control" value="{{ $val('height') }}" min="30" max="250" step="0.1">
    </div>
</div>

<hr class="my-3">
<label class="form-label fw-bold text-primary"><i class="fas fa-notes-medical me-1"></i> Medical Information</label>
<div class="row g-3 mb-3">
    @foreach(['is_diabetic' => 'Diabetic', 'on_insulin' => 'Insulin', 'latex_allergy' => 'Latex Allergy'] as $field => $label)
    <div class="col-md-2">
        <label class="form-label">{{ $label }}</label>
        <select name="{{ $field }}" class="form-select">
            <option value="">-</option>
            <option value="1" {{ $yesNo($field) === '1' ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ $yesNo($field) === '0' ? 'selected' : '' }}>No</option>
        </select>
    </div>
    @endforeach
    <div class="col-md-6">
        <label class="form-label">Specify</label>
        <input type="text" name="medical_notes" class="form-control" value="{{ $val('medical_notes') }}" maxlength="500">
    </div>
</div>

<hr class="my-3">
<label class="form-label fw-bold text-primary"><i class="fas fa-briefcase me-1"></i> Employment &amp; Referral</label>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label">Employment</label>
        <select name="employment_status" class="form-select">
            <option value="">Select</option>
            @foreach(\App\Models\Customer::EMPLOYMENT_STATUSES as $key => $label)
                <option value="{{ $key }}" {{ $val('employment_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Specify</label>
        <input type="text" name="employment_details" class="form-control" value="{{ $val('employment_details') }}" maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="form-label">You know us through</label>
        <select name="referral_source" class="form-select">
            <option value="">Select</option>
            @foreach(\App\Models\Customer::REFERRAL_SOURCES as $key => $label)
                <option value="{{ $key }}" {{ $val('referral_source') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Referral Name &amp; Details</label>
        <input type="text" name="referral_details" class="form-control" value="{{ $val('referral_details') }}" maxlength="500">
    </div>
</div>
