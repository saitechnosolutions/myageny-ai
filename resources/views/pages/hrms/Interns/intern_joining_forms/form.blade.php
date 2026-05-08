@php
    $form = $form ?? null;

    $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $educationDefaults = [
        ['qualification' => 'PG', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
        ['qualification' => 'UG', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
        ['qualification' => 'HSC / 12th', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
        ['qualification' => 'SSLC / 10th', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
    ];
    $employmentDefaults = [
        ['organisation' => '', 'designation' => '', 'period_from' => '', 'period_to' => '', 'annual_ctc' => ''],
    ];
    $familyDefaults = [
        ['name' => '', 'relation' => '', 'occupation' => '', 'date_of_birth' => '', 'mobile_no' => ''],
    ];

    $educationRows = old('educational_details');
    if (! is_array($educationRows)) {
        $educationRows = $form
            ? $form->educationalDetails->map(fn ($row) => [
                'qualification' => $row->qualification,
                'institution_name' => $row->institution_name,
                'year_of_passing' => $row->year_of_passing,
                'percentage' => $row->percentage,
                'specialization' => $row->specialization,
            ])->toArray()
            : $educationDefaults;
    }
    if ($educationRows === []) {
        $educationRows = $educationDefaults;
    }

    $employmentRows = old('employment_details');
    if (! is_array($employmentRows)) {
        $employmentRows = $form
            ? $form->employmentDetails->map(fn ($row) => [
                'organisation' => $row->organisation,
                'designation' => $row->designation,
                'period_from' => optional($row->period_from)->format('Y-m-d'),
                'period_to' => optional($row->period_to)->format('Y-m-d'),
                'annual_ctc' => $row->annual_ctc,
            ])->toArray()
            : $employmentDefaults;
    }
    if ($employmentRows === []) {
        $employmentRows = $employmentDefaults;
    }

    $familyRows = old('family_details');
    if (! is_array($familyRows)) {
        $familyRows = $form
            ? $form->familyDetails->map(fn ($row) => [
                'name' => $row->name,
                'relation' => $row->relation,
                'occupation' => $row->occupation,
                'date_of_birth' => optional($row->date_of_birth)->format('Y-m-d'),
                'mobile_no' => $row->mobile_no,
            ])->toArray()
            : $familyDefaults;
    }
    if ($familyRows === []) {
        $familyRows = $familyDefaults;
    }

    $steps = [
        ['key' => 'personal', 'label' => 'Personal Details'],
        ['key' => 'education', 'label' => 'Educational Details'],
        ['key' => 'employment', 'label' => 'Employment Details'],
        ['key' => 'family', 'label' => 'Family Details'],
        ['key' => 'documents', 'label' => 'Document Uploads'],
        ['key' => 'declaration', 'label' => 'Declaration'],
        ['key' => 'review', 'label' => 'Review & Submit'],
    ];
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="internWizardForm">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="intern-card wizard-layout">
        <aside class="wizard-sidebar">
            <div class="small text-uppercase fw-bold text-secondary mb-2">Interns</div>
            <div class="fs-5 fw-bold text-dark mb-2">Complete one section at a time</div>
            <div class="text-secondary small mb-4">Fill one section at a time and review everything before final submission.</div>
            <div class="wizard-sidebar-note">
                <strong>Quick tip</strong>
                <span>Keep documents ready before moving to the final review step.</span>
            </div>
            @foreach($steps as $index => $step)
                <button type="button" class="wizard-step-btn" data-step-button="{{ $step['key'] }}" data-step-index="{{ $index }}">
                    <span class="wizard-step-no">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="wizard-step-copy">
                        <strong>{{ $step['label'] }}</strong>
                        <small>Step {{ $index + 1 }}</small>
                    </span>
                </button>
            @endforeach
        </aside>

        <div class="wizard-main">
            <div class="wizard-header-card">
                <div>
                    <div class="small text-uppercase text-secondary fw-bold">Progress</div>
                    <div class="fw-bold fs-5" id="wizardStepTitle">Personal Details</div>
                </div>
                <div class="wizard-header-meta">
                    <span id="wizardStepCounter">Step 1 of {{ count($steps) }}</span>
                    <div class="wizard-progress"><span id="wizardProgressBar" style="width:14%;"></span></div>
                </div>
            </div>

            <section class="wizard-panel intern-card p-4" data-step-panel="personal">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 1</div>
                        <h3 class="panel-title">Personal Details</h3>
                        <p class="panel-subtitle">Start with the intern's identity, contact details, and emergency information.</p>
                        <div class="intern-required-note"><span class="intern-label-required">*</span> indicates mandatory fields.</div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="grid-half">
                        <div class="field-surface upload-surface">
                            <label class="intern-label">Photograph</label>
                            <p class="field-help">JPG or PNG works best for the profile preview.</p>
                            <input type="file" name="photograph" class="intern-input intern-file-input" accept=".jpg,.jpeg,.png">
                        </div>
                        @if($form?->photograph)
                            <img src="{{ asset('storage/' . $form->photograph) }}" alt="Photograph" class="intern-avatar-preview mt-2">
                        @endif
                        @error('photograph')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Name <span class="intern-label-required">*</span></label>
                        <input type="text" name="name" class="intern-input" value="{{ old('name', $form?->name) }}" required>
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Father's Name <span class="intern-label-required">*</span></label>
                        <input type="text" name="father_name" class="intern-input" value="{{ old('father_name', $form?->father_name) }}" required>
                        @error('father_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                      <div class="grid-half">
                        <label class="intern-label">Mobile <span class="intern-label-required">*</span></label>
                        <input type="tel" name="mobile" class="intern-input" value="{{ old('mobile', $form?->mobile) }}" required>
                        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Correspondence Address <span class="intern-label-required">*</span></label>
                        <textarea name="correspondence_address" class="intern-input intern-textarea" rows="3" required>{{ old('correspondence_address', $form?->correspondence_address) }}</textarea>
                        @error('correspondence_address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Permanent Address <span class="intern-label-required">*</span></label>
                        <textarea name="permanent_address" class="intern-input intern-textarea" rows="3" required>{{ old('permanent_address', $form?->permanent_address) }}</textarea>
                        @error('permanent_address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="grid-half">
                        <label class="intern-label">Email ID <span class="intern-label-required">*</span></label>
                        <input type="email" name="email" class="intern-input" value="{{ old('email', $form?->email) }}" required>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Date of Birth <span class="intern-label-required">*</span></label>
                        <input type="date" name="date_of_birth" class="intern-input" value="{{ old('date_of_birth', optional($form?->date_of_birth)->format('Y-m-d')) }}" required>
                        @error('date_of_birth')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Blood Group</label>
                        <select name="blood_group" class="intern-select">
                            <option value="">Select</option>
                            @foreach($bloodGroups as $group)
                                <option value="{{ $group }}" @selected(old('blood_group', $form?->blood_group) === $group)>{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Marital Status <span class="intern-label-required">*</span></label>
                        <div class="intern-choice intern-choice-inline">
                            <input class="intern-choice-input" type="radio" name="marital_status" value="single" @checked(old('marital_status', $form?->marital_status ?? 'single') === 'single')>
                            <label class="intern-choice-label">Single</label>
                        </div>
                        <div class="intern-choice intern-choice-inline">
                            <input class="intern-choice-input" type="radio" name="marital_status" value="married" @checked(old('marital_status', $form?->marital_status) === 'married')>
                            <label class="intern-choice-label">Married</label>
                        </div>
                        @error('marital_status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half" id="marriageDateField" style="{{ old('marital_status', $form?->marital_status ?? 'single') === 'married' ? '' : 'display:none;' }}">
                        <label class="intern-label">Date of Marriage</label>
                        <input type="date" name="date_of_marriage" class="intern-input" value="{{ old('date_of_marriage', optional($form?->date_of_marriage)->format('Y-m-d')) }}">
                        @error('date_of_marriage')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Aadhaar Card No <span class="intern-label-required">*</span></label>
                        <input type="text" name="aadhaar_card_no" class="intern-input" value="{{ old('aadhaar_card_no', $form?->aadhaar_card_no) }}" required>
                        @error('aadhaar_card_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Pan Card No <span class="intern-label-required">*</span></label>
                        <input type="text" name="pan_card_no" class="intern-input" value="{{ old('pan_card_no', $form?->pan_card_no) }}" required>
                        @error('pan_card_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Emergency Contact Name <span class="intern-label-required">*</span></label>
                        <input type="text" name="emergency_contact_name" class="intern-input" value="{{ old('emergency_contact_name', $form?->emergency_contact_name) }}" required>
                        @error('emergency_contact_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Emergency Contact Relation <span class="intern-label-required">*</span></label>
                        <input type="text" name="emergency_contact_relation" class="intern-input" value="{{ old('emergency_contact_relation', $form?->emergency_contact_relation) }}" required>
                        @error('emergency_contact_relation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Emergency Contact No <span class="intern-label-required">*</span></label>
                        <input type="tel" name="emergency_contact_no" class="intern-input" value="{{ old('emergency_contact_no', $form?->emergency_contact_no) }}" required>
                        @error('emergency_contact_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <section class="wizard-panel intern-card p-4" data-step-panel="education">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 2</div>
                        <h3 class="panel-title">Educational Details</h3>
                        <p class="panel-subtitle">Capture academic history in a simple, scan-friendly format.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Qualification</th>
                                <th>Institution Name</th>
                                <th>Year of Passing</th>
                                <th>Percentage</th>
                                <th>Specialization</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($educationRows as $index => $row)
                                <tr>
                                    <td><input type="text" name="educational_details[{{ $index }}][qualification]" class="intern-input" value="{{ $row['qualification'] ?? '' }}" required readonly></td>
                                    <td><input type="text" name="educational_details[{{ $index }}][institution_name]" class="intern-input" value="{{ $row['institution_name'] ?? '' }}" required></td>
                                    <td><input type="text" name="educational_details[{{ $index }}][year_of_passing]" class="intern-input" value="{{ $row['year_of_passing'] ?? '' }}" required></td>
                                    <td><input type="text" name="educational_details[{{ $index }}][percentage]" class="intern-input" value="{{ $row['percentage'] ?? '' }}" required></td>
                                    <td><input type="text" name="educational_details[{{ $index }}][specialization]" class="intern-input" value="{{ $row['specialization'] ?? '' }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="wizard-panel intern-card p-4" data-step-panel="employment">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 3</div>
                        <h3 class="panel-title">Employment Details</h3>
                        <p class="panel-subtitle">Add previous experience only where applicable.</p>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-secondary small">Add up to 3 previous organisations.</div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addEmploymentRow">Add Row</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Organisation</th>
                                <th>Designation</th>
                                <th>Period From</th>
                                <th>Period To</th>
                                <th>Annual CTC</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="employmentRows">
                            @foreach($employmentRows as $index => $row)
                                <tr>
                                    <td><input type="text" name="employment_details[{{ $index }}][organisation]" class="intern-input" value="{{ $row['organisation'] ?? '' }}"></td>
                                    <td><input type="text" name="employment_details[{{ $index }}][designation]" class="intern-input" value="{{ $row['designation'] ?? '' }}"></td>
                                    <td><input type="date" name="employment_details[{{ $index }}][period_from]" class="intern-input" value="{{ $row['period_from'] ?? '' }}"></td>
                                    <td><input type="date" name="employment_details[{{ $index }}][period_to]" class="intern-input" value="{{ $row['period_to'] ?? '' }}"></td>
                                    <td><input type="text" name="employment_details[{{ $index }}][annual_ctc]" class="intern-input" value="{{ $row['annual_ctc'] ?? '' }}"></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="wizard-panel intern-card p-4" data-step-panel="family">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 4</div>
                        <h3 class="panel-title">Family Details</h3>
                        <p class="panel-subtitle">List immediate family contacts that may be useful for records.</p>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-secondary small">Add up to 5 family members.</div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addFamilyRow">Add Row</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Relation</th>
                                <th>Occupation</th>
                                <th>Date of Birth</th>
                                <th>Mobile No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="familyRows">
                            @foreach($familyRows as $index => $row)
                                <tr>
                                    <td><input type="text" name="family_details[{{ $index }}][name]" class="intern-input" value="{{ $row['name'] ?? '' }}"></td>
                                    <td><input type="text" name="family_details[{{ $index }}][relation]" class="intern-input" value="{{ $row['relation'] ?? '' }}"></td>
                                    <td><input type="text" name="family_details[{{ $index }}][occupation]" class="intern-input" value="{{ $row['occupation'] ?? '' }}"></td>
                                    <td><input type="date" name="family_details[{{ $index }}][date_of_birth]" class="intern-input" value="{{ $row['date_of_birth'] ?? '' }}"></td>
                                    <td><input type="text" name="family_details[{{ $index }}][mobile_no]" class="intern-input" value="{{ $row['mobile_no'] ?? '' }}"></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="wizard-panel intern-card p-4" data-step-panel="documents">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 5</div>
                        <h3 class="panel-title">Document Uploads</h3>
                        <p class="panel-subtitle">Upload the required proofs and certificates for onboarding.</p>
                    </div>
                </div>
                <div class="intern-doc-grid">
                    @foreach($documentLabels as $field => $label)
                        <div class="intern-doc-card">
                            <label class="intern-label fw-semibold">{{ $label }}</label>
                            <input type="file" name="{{ $field }}" class="intern-input intern-file-input" accept=".pdf,.jpg,.jpeg,.png">
                            @if($form?->documents?->{$field})
                                <a href="{{ asset('storage/' . $form->documents->{$field}) }}" target="_blank" class="btn btn-link px-0 mt-2">View Existing</a>
                            @endif
                            @error($field)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="wizard-panel intern-card p-4" data-step-panel="declaration">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 6</div>
                        <h3 class="panel-title">Declaration</h3>
                        <p class="panel-subtitle">Confirm the information and add a signature for approval.</p>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="grid-full">
                        <div class="intern-check-card border rounded-4 p-3 bg-light">
                            <input class="intern-choice-input mt-1" type="checkbox" value="1" name="declaration_accepted" id="declarationAccepted" @checked(old('declaration_accepted', $form?->declaration_accepted)) required>
                            <label class="intern-choice-label fw-semibold" for="declarationAccepted">
                                I hereby declare that the above statements are true, complete and correct to the best of my knowledge and belief.
                            </label>
                        </div>
                        @error('declaration_accepted')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Date <span class="intern-label-required">*</span></label>
                        <input type="date" name="declaration_date" class="intern-input" value="{{ old('declaration_date', optional($form?->declaration_date)->format('Y-m-d')) }}" required>
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Place <span class="intern-label-required">*</span></label>
                        <input type="text" name="declaration_place" class="intern-input" value="{{ old('declaration_place', $form?->declaration_place) }}" required>
                    </div>
                    <div class="grid-half">
                        <label class="intern-label">Signature Upload</label>
                        <input type="file" name="signature_upload" class="intern-input intern-file-input" accept=".pdf,.jpg,.jpeg,.png">
                        @if($form?->documents?->signature_path)
                            <a href="{{ asset('storage/' . $form->documents->signature_path) }}" target="_blank" class="btn btn-link px-0 mt-2">View Existing Signature</a>
                        @endif
                    </div>
                    <div class="grid-full">
                        <label class="intern-label">Or Draw Signature</label>
                        <div class="signature-pad-wrap">
                            <canvas id="signaturePad" class="signature-pad"></canvas>
                            <input type="hidden" name="signature_data" id="signatureData">
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSignatureBtn">Clear Signature</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="wizard-panel intern-card p-4" data-step-panel="review">
                <div class="panel-intro">
                    <div>
                        <div class="panel-eyebrow">Step 7</div>
                        <h3 class="panel-title">Review & Submit</h3>
                        <p class="panel-subtitle">Take one final pass before creating the intern joining record.</p>
                    </div>
                </div>
                <div class="review-grid">
                    <div class="grid-half">
                        <div class="border rounded-4 p-4 h-100">
                            <h5 class="fw-bold mb-3">Review Summary</h5>
                            <dl class="review-list mb-0">
                                <dt>Name</dt><dd data-review="name">-</dd>
                                <dt>Email</dt><dd data-review="email">-</dd>
                                <dt>Mobile</dt><dd data-review="mobile">-</dd>
                                <dt>Marital Status</dt><dd data-review="marital_status">-</dd>
                                <dt>Emergency Contact</dt><dd data-review="emergency_contact_name">-</dd>
                                <dt>Declaration Place</dt><dd data-review="declaration_place">-</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="grid-half">
                        <div class="border rounded-4 p-4 h-100 bg-light">
                            <h5 class="fw-bold mb-3">Before You Submit</h5>
                            <ul class="mb-0 text-secondary">
                                <li>Use Previous to revisit any step.</li>
                                <li>Uploaded files will be stored in public storage.</li>
                                <li>Editing later allows document replacement.</li>
                                <li>Submit only after confirming all details are correct.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <div class="intern-card p-3 wizard-footer d-flex justify-content-between align-items-center">
                <a href="{{ $cancelRoute }}" class="btn btn-outline-secondary">Cancel</a>
                <div class="d-flex gap-2 wizard-footer-actions">
                    <button type="button" class="btn btn-outline-secondary" id="wizardPrevBtn">Previous</button>
                    <button type="button" class="btn btn-primary" id="wizardNextBtn">Next</button>
                    <button type="submit" class="btn btn-success d-none" id="wizardSubmitBtn">{{ $submitLabel }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<template id="employmentRowTemplate">
    <tr>
        <td><input type="text" name="employment_details[__INDEX__][organisation]" class="intern-input"></td>
        <td><input type="text" name="employment_details[__INDEX__][designation]" class="intern-input"></td>
        <td><input type="date" name="employment_details[__INDEX__][period_from]" class="intern-input"></td>
        <td><input type="date" name="employment_details[__INDEX__][period_to]" class="intern-input"></td>
        <td><input type="text" name="employment_details[__INDEX__][annual_ctc]" class="intern-input"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>Remove</button></td>
    </tr>
</template>

<template id="familyRowTemplate">
    <tr>
        <td><input type="text" name="family_details[__INDEX__][name]" class="intern-input"></td>
        <td><input type="text" name="family_details[__INDEX__][relation]" class="intern-input"></td>
        <td><input type="text" name="family_details[__INDEX__][occupation]" class="intern-input"></td>
        <td><input type="date" name="family_details[__INDEX__][date_of_birth]" class="intern-input"></td>
        <td><input type="text" name="family_details[__INDEX__][mobile_no]" class="intern-input"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>Remove</button></td>
    </tr>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('internWizardForm');
    const panels = Array.from(document.querySelectorAll('[data-step-panel]'));
    const stepButtons = Array.from(document.querySelectorAll('[data-step-button]'));
    const prevButton = document.getElementById('wizardPrevBtn');
    const nextButton = document.getElementById('wizardNextBtn');
    const submitButton = document.getElementById('wizardSubmitBtn');
    const stepTitle = document.getElementById('wizardStepTitle');
    const stepCounter = document.getElementById('wizardStepCounter');
    const progressBar = document.getElementById('wizardProgressBar');
    const marriageDateField = document.getElementById('marriageDateField');
    const employmentRows = document.getElementById('employmentRows');
    const familyRows = document.getElementById('familyRows');
    const addEmploymentRow = document.getElementById('addEmploymentRow');
    const addFamilyRow = document.getElementById('addFamilyRow');
    const signaturePad = document.getElementById('signaturePad');
    const signatureData = document.getElementById('signatureData');
    const clearSignatureBtn = document.getElementById('clearSignatureBtn');
    let activeIndex = 0;

    function updateReview() {
        document.querySelectorAll('[data-review]').forEach(function (node) {
            const field = node.getAttribute('data-review');
            const source = form.querySelector('[name="' + field + '"]');
            if (source) {
                if (source.type === 'radio') {
                    const checked = form.querySelector('[name="' + field + '"]:checked');
                    node.textContent = checked ? checked.value : '-';
                } else {
                    node.textContent = source.value || '-';
                }
            }
        });
    }

    function toggleMarriageDate() {
        const selected = form.querySelector('input[name="marital_status"]:checked');
        marriageDateField.style.display = selected && selected.value === 'married' ? '' : 'none';
    }

    form.querySelectorAll('input[name="marital_status"]').forEach(function (input) {
        input.addEventListener('change', toggleMarriageDate);
    });
    toggleMarriageDate();

    function validateCurrentStep() {
        const inputs = panels[activeIndex].querySelectorAll('input, select, textarea');
        for (const input of inputs) {
            if (typeof input.reportValidity === 'function' && !input.reportValidity()) {
                input.focus();
                return false;
            }
        }
        return true;
    }

    function updateWizard() {
        panels.forEach(function (panel, index) {
            panel.classList.toggle('is-active', index === activeIndex);
        });

        stepButtons.forEach(function (button, index) {
            button.classList.toggle('is-active', index === activeIndex);
            button.classList.toggle('is-complete', index < activeIndex);
        });

        const stepLabel = stepButtons[activeIndex].querySelector('.wizard-step-copy strong');
        stepTitle.textContent = stepLabel ? stepLabel.textContent : 'Intern Joining Form';
        stepCounter.textContent = 'Step ' + (activeIndex + 1) + ' of ' + panels.length;
        progressBar.style.width = (((activeIndex + 1) / panels.length) * 100) + '%';
        prevButton.disabled = activeIndex === 0;
        nextButton.classList.toggle('d-none', activeIndex === panels.length - 1);
        submitButton.classList.toggle('d-none', activeIndex !== panels.length - 1);

        if (panels[activeIndex].getAttribute('data-step-panel') === 'review') {
            updateReview();
        }
    }

    stepButtons.forEach(function (button, index) {
        button.addEventListener('click', function () {
            if (index <= activeIndex || validateCurrentStep()) {
                activeIndex = index;
                updateWizard();
            }
        });
    });

    prevButton.addEventListener('click', function () {
        if (activeIndex > 0) {
            activeIndex -= 1;
            updateWizard();
        }
    });

    nextButton.addEventListener('click', function () {
        if (validateCurrentStep() && activeIndex < panels.length - 1) {
            activeIndex += 1;
            updateWizard();
        }
    });

    if (addEmploymentRow && employmentRows) {
        addEmploymentRow.addEventListener('click', function () {
            const currentRows = employmentRows.querySelectorAll('tr').length;
            if (currentRows >= 3) {
                return;
            }
            employmentRows.insertAdjacentHTML('beforeend', document.getElementById('employmentRowTemplate').innerHTML.replaceAll('__INDEX__', currentRows));
        });
    }

    if (addFamilyRow && familyRows) {
        addFamilyRow.addEventListener('click', function () {
            const currentRows = familyRows.querySelectorAll('tr').length;
            if (currentRows >= 5) {
                return;
            }
            familyRows.insertAdjacentHTML('beforeend', document.getElementById('familyRowTemplate').innerHTML.replaceAll('__INDEX__', currentRows));
        });
    }

    document.addEventListener('click', function (event) {
        const removeButton = event.target.closest('[data-remove-row]');
        if (!removeButton) {
            return;
        }
        const row = removeButton.closest('tr');
        row.remove();
    });

    if (signaturePad) {
        const ctx = signaturePad.getContext('2d');
        let drawing = false;

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = signaturePad.getBoundingClientRect();
            signaturePad.width = rect.width * ratio;
            signaturePad.height = rect.height * ratio;
            ctx.scale(ratio, ratio);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#111827';
        }

        function position(event) {
            const rect = signaturePad.getBoundingClientRect();
            const point = event.touches ? event.touches[0] : event;
            return { x: point.clientX - rect.left, y: point.clientY - rect.top };
        }

        function start(event) {
            drawing = true;
            const point = position(event);
            ctx.beginPath();
            ctx.moveTo(point.x, point.y);
        }

        function move(event) {
            if (!drawing) {
                return;
            }
            event.preventDefault();
            const point = position(event);
            ctx.lineTo(point.x, point.y);
            ctx.stroke();
            signatureData.value = signaturePad.toDataURL('image/png');
        }

        function stop() {
            drawing = false;
        }

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        signaturePad.addEventListener('mousedown', start);
        signaturePad.addEventListener('mousemove', move);
        signaturePad.addEventListener('mouseup', stop);
        signaturePad.addEventListener('mouseleave', stop);
        signaturePad.addEventListener('touchstart', start, { passive: false });
        signaturePad.addEventListener('touchmove', move, { passive: false });
        signaturePad.addEventListener('touchend', stop);
        clearSignatureBtn.addEventListener('click', function () {
            ctx.clearRect(0, 0, signaturePad.width, signaturePad.height);
            signatureData.value = '';
        });
    }

    updateWizard();
});
</script>
@endpush
