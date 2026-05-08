@php
    $employee = $employee ?? null;

    $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $statusOptions = ['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'];

    $defaultEducations = [
        ['qualification' => 'PG', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
        ['qualification' => 'UG', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
        ['qualification' => 'HSC / 12th', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
        ['qualification' => 'SSLC / 10th', 'institution_name' => '', 'year_of_passing' => '', 'percentage' => '', 'specialization' => ''],
    ];
    $defaultEmployments = [
        ['organisation' => '', 'designation' => '', 'period_from' => '', 'period_to' => '', 'annual_ctc' => ''],
        ['organisation' => '', 'designation' => '', 'period_from' => '', 'period_to' => '', 'annual_ctc' => ''],
        ['organisation' => '', 'designation' => '', 'period_from' => '', 'period_to' => '', 'annual_ctc' => ''],
    ];
    $defaultFamilyDetails = [
        ['name' => '', 'relation' => '', 'occupation' => '', 'date_of_birth' => '', 'mobile_no' => ''],
    ];

    $educations = old('educations');
    if (!is_array($educations)) {
        $educations = $employee
            ? $employee->educations->map(fn ($row) => [
                'qualification' => $row->qualification,
                'institution_name' => $row->institution_name,
                'year_of_passing' => $row->year_of_passing,
                'percentage' => $row->percentage,
                'specialization' => $row->specialization,
            ])->toArray()
            : $defaultEducations;
    }
    if ($educations === []) {
        $educations = $defaultEducations;
    }

    $employments = old('employments');
    if (!is_array($employments)) {
        $employments = $employee
            ? $employee->employments->map(fn ($row) => [
                'organisation' => $row->organisation,
                'designation' => $row->designation,
                'period_from' => optional($row->period_from)->format('Y-m-d'),
                'period_to' => optional($row->period_to)->format('Y-m-d'),
                'annual_ctc' => $row->annual_ctc,
            ])->toArray()
            : $defaultEmployments;
    }
    if ($employments === []) {
        $employments = $defaultEmployments;
    }

    $familyDetails = old('family_details');
    if (!is_array($familyDetails)) {
        $familyDetails = $employee
            ? $employee->familyDetails->map(fn ($row) => [
                'name' => $row->name,
                'relation' => $row->relation,
                'occupation' => $row->occupation,
                'date_of_birth' => optional($row->date_of_birth)->format('Y-m-d'),
                'mobile_no' => $row->mobile_no,
            ])->toArray()
            : $defaultFamilyDetails;
    }
    if ($familyDetails === []) {
        $familyDetails = $defaultFamilyDetails;
    }

    $wizardSteps = [
        ['key' => 'personal', 'number' => '01', 'title' => 'Personal Details', 'sub' => 'Basic profile and identity information.'],
        ['key' => 'emergency', 'number' => '02', 'title' => 'Emergency Contact', 'sub' => 'Primary emergency reach-out person.'],
        ['key' => 'education', 'number' => '03', 'title' => 'Educational Details', 'sub' => 'Academic records and qualifications.'],
        ['key' => 'employment', 'number' => '04', 'title' => 'Employment Details', 'sub' => 'Previous organisations and CTC history.'],
        ['key' => 'family', 'number' => '05', 'title' => 'Family Details', 'sub' => 'Immediate family and contact details.'],
        ['key' => 'reference', 'number' => '06', 'title' => 'Professional Reference', 'sub' => 'Reference contact information.'],
        ['key' => 'salary', 'number' => '07', 'title' => 'Employee Salaries', 'sub' => 'Salary, statutory deductions, and payroll setup.'],
        ['key' => 'bank', 'number' => '08', 'title' => 'Bank Account Details', 'sub' => 'Payroll and bank information.'],
        ['key' => 'documents', 'number' => '09', 'title' => 'Document Uploads', 'sub' => 'Certificates and identity proofs.'],
        ['key' => 'declaration', 'number' => '10', 'title' => 'Declaration', 'sub' => 'Final declaration and signature.'],
    ];

    $errorStepMap = [
        'name' => 'personal',
        'role_id' => 'personal',
        'department_id' => 'personal',
        'father_name' => 'personal',
        'correspondence_address' => 'personal',
        'permanent_address' => 'personal',
        'mobile' => 'personal',
        'email' => 'personal',
        'date_of_birth' => 'personal',
        'blood_group' => 'personal',
        'marital_status' => 'personal',
        'date_of_marriage' => 'personal',
        'aadhaar_card_no' => 'personal',
        'pan_card_no' => 'personal',
        'photograph' => 'personal',
        'status' => 'personal',
        'emergency_contact_name' => 'emergency',
        'emergency_relation' => 'emergency',
        'emergency_contact_no' => 'emergency',
        'reference_name' => 'reference',
        'reference_organization_name' => 'reference',
        'reference_designation' => 'reference',
        'reference_contact_no' => 'reference',
        'reference_mail_id' => 'reference',
        'salary_effective_from' => 'salary',
        'gross_salary' => 'salary',
        'basic_salary' => 'salary',
        'hra' => 'salary',
        'special_allowance' => 'salary',
        'other_allowance' => 'salary',
        'esi_enabled' => 'salary',
        'esi_no' => 'salary',
        'esi_employee_contribution' => 'salary',
        'esi_employer_contribution' => 'salary',
        'pf_enabled' => 'salary',
        'uan_no' => 'salary',
        'pf_account_no' => 'salary',
        'pf_employee_contribution' => 'salary',
        'pf_employer_contribution' => 'salary',
        'professional_tax' => 'salary',
        'tds_amount' => 'salary',
        'loan_deduction' => 'salary',
        'other_deduction' => 'salary',
        'total_deduction' => 'salary',
        'net_salary' => 'salary',
        'salary_payment_mode' => 'salary',
        'deduction_notes' => 'salary',
        'bank_name' => 'bank',
        'bank_account_name' => 'bank',
        'bank_account_no' => 'bank',
        'bank_ifsc_code' => 'bank',
        'bank_branch' => 'bank',
        'declaration_date' => 'declaration',
        'declaration_place' => 'declaration',
        'signature' => 'declaration',
    ];

    foreach (array_keys($errors->toArray()) as $errorKey) {
        if (str_starts_with($errorKey, 'educations.')) {
            $errorStepMap[$errorKey] = 'education';
        } elseif (str_starts_with($errorKey, 'employments.')) {
            $errorStepMap[$errorKey] = 'employment';
        } elseif (str_starts_with($errorKey, 'family_details.')) {
            $errorStepMap[$errorKey] = 'family';
        } elseif (str_starts_with($errorKey, 'document_')) {
            $errorStepMap[$errorKey] = 'documents';
        }
    }

    $currentStepKey = 'personal';
    foreach (array_keys($errors->toArray()) as $errorKey) {
        if (isset($errorStepMap[$errorKey])) {
            $currentStepKey = $errorStepMap[$errorKey];
            break;
        }
    }
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="employeeWizardForm" data-initial-step="{{ $currentStepKey }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="eob-wizard-shell">
        <aside class="eob-wizard-nav">
            <div class="eob-wizard-nav-head">
                <div class="eob-wizard-kicker">Onboarding Wizard</div>
                <div class="eob-wizard-title">Complete one section at a time</div>
                <div class="eob-wizard-copy">Move through each onboarding block step by step and submit everything together at the end.</div>
            </div>

            <div class="eob-wizard-steps">
                @foreach($wizardSteps as $index => $step)
                    <button type="button" class="eob-wizard-step-btn" data-step-target="{{ $step['key'] }}" data-step-index="{{ $index }}">
                        <span class="eob-wizard-step-no">{{ $step['number'] }}</span>
                        <span class="eob-wizard-step-text">
                            <span class="eob-wizard-step-title">{{ $step['title'] }}</span>
                            <span class="eob-wizard-step-sub">{{ $step['sub'] }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

        <div class="eob-wizard-main">
            <div class="eob-wizard-progress-card">
                <div>
                    <div class="eob-card-title" id="wizardActiveTitle">Personal Details</div>
                    <div class="eob-card-sub" id="wizardActiveSub">Basic profile and identity information.</div>
                    <div class="eob-required-note"><span class="eob-label-required">*</span> indicates mandatory fields.</div>
                </div>
                <div class="eob-wizard-progress-meta">
                    <span id="wizardStepCounter">Step 1 of {{ count($wizardSteps) }}</span>
                    <div class="eob-wizard-progress-track">
                        <span id="wizardProgressBar"></span>
                    </div>
                </div>
            </div>

            <div class="eob-wizard-panels">
                <section class="eob-card eob-wizard-panel" data-step="personal" data-step-title="Personal Details" data-step-sub="Basic profile and identity information.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Personal Details</div>
                            <div class="eob-card-sub">Basic profile, identification, and contact information.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-form-grid">
                            <div class="eob-group">
                                <label class="eob-label">Employee ID</label>
                                <input type="text" class="eob-input" value="{{ $generatedEmployeeId }}" readonly>
                                <div class="eob-help">Auto-generated by the system and cannot be edited.</div>
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Name <span class="eob-label-required">*</span></label>
                                <input type="text" name="name" class="eob-input" value="{{ old('name', $employee?->name) }}" required>
                                @error('name')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Department</label>
                                <select name="department_id" class="eob-select" id="employeeDepartmentSelect">
                                    <option value="">Select department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @selected((string) old('department_id', $employee?->department_id) === (string) $department->id)>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Role</label>
                                <select name="role_id" class="eob-select" id="employeeRoleSelect">
                                    <option value="">Select role</option>
                                    @foreach($roles as $role)
                                        <option
                                            value="{{ $role->id }}"
                                            data-department-id="{{ $role->department_id }}"
                                            @selected((string) old('role_id', $employee?->role_id) === (string) $role->id)
                                        >
                                            {{ $role->display_name ?: ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="eob-help">After selecting a department, only roles from that department will be shown.</div>
                                @error('role_id')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Father's Name</label>
                                <input type="text" name="father_name" class="eob-input" value="{{ old('father_name', $employee?->father_name) }}">
                                @error('father_name')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group full">
                                <label class="eob-label">Correspondence Address</label>
                                <textarea name="correspondence_address" class="eob-textarea">{{ old('correspondence_address', $employee?->correspondence_address) }}</textarea>
                                @error('correspondence_address')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group full">
                                <label class="eob-label">Permanent Address</label>
                                <textarea name="permanent_address" class="eob-textarea">{{ old('permanent_address', $employee?->permanent_address) }}</textarea>
                                @error('permanent_address')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Mobile <span class="eob-label-required">*</span></label>
                                <input type="text" name="mobile" class="eob-input" value="{{ old('mobile', $employee?->mobile) }}" required>
                                @error('mobile')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Email ID <span class="eob-label-required">*</span></label>
                                <input type="email" name="email" class="eob-input" value="{{ old('email', $employee?->email) }}" required>
                                @error('email')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Date of Birth <span class="eob-label-required">*</span></label>
                                <input type="date" name="date_of_birth" class="eob-input" value="{{ old('date_of_birth', optional($employee?->date_of_birth)->format('Y-m-d')) }}" required>
                                @error('date_of_birth')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Blood Group</label>
                                <select name="blood_group" class="eob-select">
                                    <option value="">Select blood group</option>
                                    @foreach($bloodGroups as $bloodGroup)
                                        <option value="{{ $bloodGroup }}" @selected(old('blood_group', $employee?->blood_group) === $bloodGroup)>{{ $bloodGroup }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Marital Status <span class="eob-label-required">*</span></label>
                                <div class="eob-radio-group">
                                    <label class="eob-radio">
                                        <input type="radio" name="marital_status" value="single" @checked(old('marital_status', $employee?->marital_status ?? 'single') === 'single')>
                                        <span>Single</span>
                                    </label>
                                    <label class="eob-radio">
                                        <input type="radio" name="marital_status" value="married" @checked(old('marital_status', $employee?->marital_status) === 'married')>
                                        <span>Married</span>
                                    </label>
                                </div>
                                @error('marital_status')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group" id="marriageDateGroup" style="{{ old('marital_status', $employee?->marital_status ?? 'single') === 'married' ? '' : 'display:none;' }}">
                                <label class="eob-label">Date of Marriage</label>
                                <input type="date" name="date_of_marriage" class="eob-input" value="{{ old('date_of_marriage', optional($employee?->date_of_marriage)->format('Y-m-d')) }}">
                                @error('date_of_marriage')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Aadhaar Card No</label>
                                <input type="text" name="aadhaar_card_no" class="eob-input" value="{{ old('aadhaar_card_no', $employee?->aadhaar_card_no) }}">
                                @error('aadhaar_card_no')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Pan Card No</label>
                                <input type="text" name="pan_card_no" class="eob-input" value="{{ old('pan_card_no', $employee?->pan_card_no) }}">
                                @error('pan_card_no')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Photograph</label>
                                <input type="file" name="photograph" class="eob-input" accept=".jpg,.jpeg,.png">
                                @if($employee?->photograph)
                                    <div class="eob-file-links">
                                        <a href="{{ asset('storage/' . $employee->photograph) }}" target="_blank" class="eob-btn eob-btn-ghost eob-btn-sm">View Existing</a>
                                    </div>
                                @endif
                                @error('photograph')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Status <span class="eob-label-required">*</span></label>
                                <select name="status" class="eob-select" required>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $employee?->status ?? 'pending') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="emergency" data-step-title="Emergency Contact" data-step-sub="Primary emergency reach-out person.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Emergency Contact</div>
                            <div class="eob-card-sub">Store the primary person to reach during emergencies.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-form-grid">
                            <div class="eob-group">
                                <label class="eob-label">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="eob-input" value="{{ old('emergency_contact_name', $employee?->emergency_contact_name) }}">
                                @error('emergency_contact_name')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Relation</label>
                                <input type="text" name="emergency_relation" class="eob-input" value="{{ old('emergency_relation', $employee?->emergency_relation) }}">
                                @error('emergency_relation')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Contact No</label>
                                <input type="text" name="emergency_contact_no" class="eob-input" value="{{ old('emergency_contact_no', $employee?->emergency_contact_no) }}">
                                @error('emergency_contact_no')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="education" data-step-title="Educational Details" data-step-sub="Academic records and qualifications.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Educational Details</div>
                            <div class="eob-card-sub">Default rows are included and you can add more if needed.</div>
                        </div>
                        <button type="button" class="eob-btn eob-btn-ghost eob-btn-sm" data-add-row="education">Add Row</button>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-table-wrap">
                            <table class="eob-table">
                                <thead>
                                    <tr>
                                        <th>Qualification</th>
                                        <th>Institution Name</th>
                                        <th>Year of Passing</th>
                                        <th>Percentage</th>
                                        <th>Specialization</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="educationRows">
                                    @foreach($educations as $index => $education)
                                        <tr>
                                            <td>
                                                <input type="text" name="educations[{{ $index }}][qualification]" class="eob-input" value="{{ $education['qualification'] ?? '' }}">
                                                @error("educations.$index.qualification")<div class="eob-error">{{ $message }}</div>@enderror
                                            </td>
                                            <td><input type="text" name="educations[{{ $index }}][institution_name]" class="eob-input" value="{{ $education['institution_name'] ?? '' }}"></td>
                                            <td><input type="text" name="educations[{{ $index }}][year_of_passing]" class="eob-input" value="{{ $education['year_of_passing'] ?? '' }}"></td>
                                            <td><input type="text" name="educations[{{ $index }}][percentage]" class="eob-input" value="{{ $education['percentage'] ?? '' }}"></td>
                                            <td><input type="text" name="educations[{{ $index }}][specialization]" class="eob-input" value="{{ $education['specialization'] ?? '' }}"></td>
                                            <td><button type="button" class="eob-btn eob-btn-danger eob-btn-sm" data-remove-row>Remove</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="employment" data-step-title="Employment Details" data-step-sub="Previous organisations and CTC history.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Employment Details</div>
                            <div class="eob-card-sub">Capture the employee's previous organisations and compensation history.</div>
                        </div>
                        <button type="button" class="eob-btn eob-btn-ghost eob-btn-sm" data-add-row="employment">Add Row</button>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-table-wrap">
                            <table class="eob-table">
                                <thead>
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
                                    @foreach($employments as $index => $employment)
                                        <tr>
                                            <td><input type="text" name="employments[{{ $index }}][organisation]" class="eob-input" value="{{ $employment['organisation'] ?? '' }}"></td>
                                            <td><input type="text" name="employments[{{ $index }}][designation]" class="eob-input" value="{{ $employment['designation'] ?? '' }}"></td>
                                            <td><input type="date" name="employments[{{ $index }}][period_from]" class="eob-input" value="{{ $employment['period_from'] ?? '' }}"></td>
                                            <td><input type="date" name="employments[{{ $index }}][period_to]" class="eob-input" value="{{ $employment['period_to'] ?? '' }}"></td>
                                            <td><input type="text" name="employments[{{ $index }}][annual_ctc]" class="eob-input" value="{{ $employment['annual_ctc'] ?? '' }}"></td>
                                            <td><button type="button" class="eob-btn eob-btn-danger eob-btn-sm" data-remove-row>Remove</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="family" data-step-title="Family Details" data-step-sub="Immediate family and contact details.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Family Details</div>
                            <div class="eob-card-sub">Add immediate family details with occupation and mobile information.</div>
                        </div>
                        <button type="button" class="eob-btn eob-btn-ghost eob-btn-sm" data-add-row="family">Add Row</button>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-table-wrap">
                            <table class="eob-table">
                                <thead>
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
                                    @foreach($familyDetails as $index => $family)
                                        <tr>
                                            <td><input type="text" name="family_details[{{ $index }}][name]" class="eob-input" value="{{ $family['name'] ?? '' }}"></td>
                                            <td><input type="text" name="family_details[{{ $index }}][relation]" class="eob-input" value="{{ $family['relation'] ?? '' }}"></td>
                                            <td><input type="text" name="family_details[{{ $index }}][occupation]" class="eob-input" value="{{ $family['occupation'] ?? '' }}"></td>
                                            <td><input type="date" name="family_details[{{ $index }}][date_of_birth]" class="eob-input" value="{{ $family['date_of_birth'] ?? '' }}"></td>
                                            <td><input type="text" name="family_details[{{ $index }}][mobile_no]" class="eob-input" value="{{ $family['mobile_no'] ?? '' }}"></td>
                                            <td><button type="button" class="eob-btn eob-btn-danger eob-btn-sm" data-remove-row>Remove</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="reference" data-step-title="Professional Reference" data-step-sub="Reference contact information.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Professional Reference</div>
                            <div class="eob-card-sub">One reference is enough for now, but you can expand later if needed.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-form-grid">
                            <div class="eob-group">
                                <label class="eob-label">Name</label>
                                <input type="text" name="reference_name" class="eob-input" value="{{ old('reference_name', $employee?->reference_name) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Organization Name</label>
                                <input type="text" name="reference_organization_name" class="eob-input" value="{{ old('reference_organization_name', $employee?->reference_organization_name) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Designation</label>
                                <input type="text" name="reference_designation" class="eob-input" value="{{ old('reference_designation', $employee?->reference_designation) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Contact No</label>
                                <input type="text" name="reference_contact_no" class="eob-input" value="{{ old('reference_contact_no', $employee?->reference_contact_no) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Mail ID</label>
                                <input type="email" name="reference_mail_id" class="eob-input" value="{{ old('reference_mail_id', $employee?->reference_mail_id) }}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="salary" data-step-title="Employee Salaries" data-step-sub="Salary, statutory deductions, and payroll setup.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Employee Salaries</div>
                            <div class="eob-card-sub">Capture gross salary, PF, ESI, deductions, and payroll-related account details.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-form-grid">
                            <div class="eob-group">
                                <label class="eob-label">Salary Effective From</label>
                                <input type="date" name="salary_effective_from" class="eob-input" value="{{ old('salary_effective_from', optional($employee?->salary_effective_from)->format('Y-m-d')) }}">
                                @error('salary_effective_from')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Salary Payment Mode</label>
                                <select name="salary_payment_mode" class="eob-select">
                                    <option value="">Select payment mode</option>
                                    @foreach(['bank_transfer' => 'Bank Transfer', 'cash' => 'Cash', 'cheque' => 'Cheque', 'upi' => 'UPI'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('salary_payment_mode', $employee?->salary_payment_mode) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('salary_payment_mode')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Gross Salary</label>
                                <input type="number" step="0.01" min="0" name="gross_salary" class="eob-input" data-salary-input="gross_salary" value="{{ old('gross_salary', $employee?->gross_salary) }}">
                                @error('gross_salary')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Basic Salary</label>
                                <input type="number" step="0.01" min="0" name="basic_salary" class="eob-input" data-salary-input="basic_salary" value="{{ old('basic_salary', $employee?->basic_salary) }}">
                                <div class="eob-help">PF is auto-estimated as 12% of basic salary when enabled.</div>
                                @error('basic_salary')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">HRA</label>
                                <input type="number" step="0.01" min="0" name="hra" class="eob-input" value="{{ old('hra', $employee?->hra) }}">
                                @error('hra')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Special Allowance</label>
                                <input type="number" step="0.01" min="0" name="special_allowance" class="eob-input" value="{{ old('special_allowance', $employee?->special_allowance) }}">
                                @error('special_allowance')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Other Allowance</label>
                                <input type="number" step="0.01" min="0" name="other_allowance" class="eob-input" value="{{ old('other_allowance', $employee?->other_allowance) }}">
                                @error('other_allowance')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Professional Tax</label>
                                <input type="number" step="0.01" min="0" name="professional_tax" class="eob-input" data-salary-input="professional_tax" value="{{ old('professional_tax', $employee?->professional_tax) }}">
                                @error('professional_tax')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">TDS Amount</label>
                                <input type="number" step="0.01" min="0" name="tds_amount" class="eob-input" data-salary-input="tds_amount" value="{{ old('tds_amount', $employee?->tds_amount) }}">
                                @error('tds_amount')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Loan Deduction</label>
                                <input type="number" step="0.01" min="0" name="loan_deduction" class="eob-input" data-salary-input="loan_deduction" value="{{ old('loan_deduction', $employee?->loan_deduction) }}">
                                @error('loan_deduction')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Other Deduction</label>
                                <input type="number" step="0.01" min="0" name="other_deduction" class="eob-input" data-salary-input="other_deduction" value="{{ old('other_deduction', $employee?->other_deduction) }}">
                                @error('other_deduction')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>

                            <div class="eob-group full">
                                <div class="eob-radio-group">
                                    <label class="eob-radio">
                                        <input type="hidden" name="pf_enabled" value="0">
                                        <input type="checkbox" name="pf_enabled" value="1" data-salary-toggle="pf" @checked((bool) old('pf_enabled', $employee?->pf_enabled))>
                                        <span>Enable PF Calculation</span>
                                    </label>
                                    <label class="eob-radio">
                                        <input type="hidden" name="esi_enabled" value="0">
                                        <input type="checkbox" name="esi_enabled" value="1" data-salary-toggle="esi" @checked((bool) old('esi_enabled', $employee?->esi_enabled))>
                                        <span>Enable ESI Calculation</span>
                                    </label>
                                </div>
                            </div>

                            <div class="eob-group">
                                <label class="eob-label">UAN No</label>
                                <input type="text" name="uan_no" class="eob-input" value="{{ old('uan_no', $employee?->uan_no) }}">
                                @error('uan_no')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">PF Account No</label>
                                <input type="text" name="pf_account_no" class="eob-input" value="{{ old('pf_account_no', $employee?->pf_account_no) }}">
                                @error('pf_account_no')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">PF Employee Contribution</label>
                                <input type="number" step="0.01" min="0" name="pf_employee_contribution" class="eob-input" data-salary-output="pf_employee_contribution" value="{{ old('pf_employee_contribution', $employee?->pf_employee_contribution) }}" readonly>
                                @error('pf_employee_contribution')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">PF Employer Contribution</label>
                                <input type="number" step="0.01" min="0" name="pf_employer_contribution" class="eob-input" data-salary-output="pf_employer_contribution" value="{{ old('pf_employer_contribution', $employee?->pf_employer_contribution) }}" readonly>
                                @error('pf_employer_contribution')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>

                            <div class="eob-group">
                                <label class="eob-label">ESI No</label>
                                <input type="text" name="esi_no" class="eob-input" value="{{ old('esi_no', $employee?->esi_no) }}">
                                <div class="eob-help">ESI is estimated only when gross salary is at or below 21,000.</div>
                                @error('esi_no')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">ESI Employee Contribution</label>
                                <input type="number" step="0.01" min="0" name="esi_employee_contribution" class="eob-input" data-salary-output="esi_employee_contribution" value="{{ old('esi_employee_contribution', $employee?->esi_employee_contribution) }}" readonly>
                                @error('esi_employee_contribution')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">ESI Employer Contribution</label>
                                <input type="number" step="0.01" min="0" name="esi_employer_contribution" class="eob-input" data-salary-output="esi_employer_contribution" value="{{ old('esi_employer_contribution', $employee?->esi_employer_contribution) }}" readonly>
                                @error('esi_employer_contribution')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Total Deduction</label>
                                <input type="number" step="0.01" min="0" name="total_deduction" class="eob-input" data-salary-output="total_deduction" value="{{ old('total_deduction', $employee?->total_deduction) }}" readonly>
                                @error('total_deduction')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Net Salary</label>
                                <input type="number" step="0.01" min="0" name="net_salary" class="eob-input" data-salary-output="net_salary" value="{{ old('net_salary', $employee?->net_salary) }}" readonly>
                                @error('net_salary')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="eob-group full">
                                <label class="eob-label">Deduction Notes</label>
                                <textarea name="deduction_notes" class="eob-textarea">{{ old('deduction_notes', $employee?->deduction_notes) }}</textarea>
                                @error('deduction_notes')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="bank" data-step-title="Bank Account Details" data-step-sub="Payroll and bank information.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Bank Account Details</div>
                            <div class="eob-card-sub">Optional banking details for payroll and reimbursements.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-form-grid">
                            <div class="eob-group">
                                <label class="eob-label">Bank Name</label>
                                <input type="text" name="bank_name" class="eob-input" value="{{ old('bank_name', $employee?->bank_name) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Account Name</label>
                                <input type="text" name="bank_account_name" class="eob-input" value="{{ old('bank_account_name', $employee?->bank_account_name) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Account No</label>
                                <input type="text" name="bank_account_no" class="eob-input" value="{{ old('bank_account_no', $employee?->bank_account_no) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">IFSC Code</label>
                                <input type="text" name="bank_ifsc_code" class="eob-input" value="{{ old('bank_ifsc_code', $employee?->bank_ifsc_code) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Branch</label>
                                <input type="text" name="bank_branch" class="eob-input" value="{{ old('bank_branch', $employee?->bank_branch) }}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="documents" data-step-title="Document Uploads" data-step-sub="Certificates and identity proofs.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Document Uploads</div>
                            <div class="eob-card-sub">Allowed formats: PDF, JPG, JPEG, PNG. Maximum size: 5MB.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-file-grid">
                            @foreach($documentLabels as $field => $label)
                                @continue(in_array($field, ['photograph', 'signature'], true))
                                <div class="eob-file-card">
                                    <div>
                                        <label class="eob-label">{{ $label }}</label>
                                        <div class="eob-help">Upload a fresh file or keep the existing one.</div>
                                    </div>
                                    <input type="file" name="{{ $field }}" class="eob-input" accept=".pdf,.jpg,.jpeg,.png">
                                    @if($employee?->{$field})
                                        <div class="eob-file-meta">
                                            <div class="eob-file-name">{{ basename($employee->{$field}) }}</div>
                                            <div class="eob-file-links">
                                                <a href="{{ asset('storage/' . $employee->{$field}) }}" target="_blank" class="eob-btn eob-btn-ghost eob-btn-sm">View</a>
                                                <a href="{{ asset('storage/' . $employee->{$field}) }}" download class="eob-btn eob-btn-ghost eob-btn-sm">Download</a>
                                            </div>
                                        </div>
                                    @endif
                                    @error($field)<div class="eob-error">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="eob-card eob-wizard-panel" data-step="declaration" data-step-title="Declaration" data-step-sub="Final declaration and signature.">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Declaration</div>
                            <div class="eob-card-sub">Capture declaration date, place, and signed proof.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-form-grid">
                            <div class="eob-group">
                                <label class="eob-label">Date</label>
                                <input type="date" name="declaration_date" class="eob-input" value="{{ old('declaration_date', optional($employee?->declaration_date)->format('Y-m-d')) }}">
                            </div>
                            <div class="eob-group">
                                <label class="eob-label">Place</label>
                                <input type="text" name="declaration_place" class="eob-input" value="{{ old('declaration_place', $employee?->declaration_place) }}">
                            </div>
                            <div class="eob-group full">
                                <label class="eob-label">Signature</label>
                                <input type="file" name="signature" class="eob-input" accept=".pdf,.jpg,.jpeg,.png">
                                @if($employee?->signature)
                                    <div class="eob-file-links">
                                        <a href="{{ asset('storage/' . $employee->signature) }}" target="_blank" class="eob-btn eob-btn-ghost eob-btn-sm">View Existing</a>
                                        <a href="{{ asset('storage/' . $employee->signature) }}" download class="eob-btn eob-btn-ghost eob-btn-sm">Download Existing</a>
                                    </div>
                                @endif
                                @error('signature')<div class="eob-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="eob-foot eob-wizard-footer">
                <a href="{{ $cancelRoute }}" class="eob-btn eob-btn-ghost">Cancel</a>
                <div class="eob-wizard-footer-actions">
                    <button type="button" class="eob-btn eob-btn-ghost" id="wizardPrevBtn">Previous</button>
                    <button type="button" class="eob-btn eob-btn-primary" id="wizardNextBtn">Next Step</button>
                    <button type="submit" class="eob-btn eob-btn-primary" id="wizardSubmitBtn">{{ $submitLabel }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<template id="educationRowTemplate">
    <tr>
        <td><input type="text" name="educations[__INDEX__][qualification]" class="eob-input"></td>
        <td><input type="text" name="educations[__INDEX__][institution_name]" class="eob-input"></td>
        <td><input type="text" name="educations[__INDEX__][year_of_passing]" class="eob-input"></td>
        <td><input type="text" name="educations[__INDEX__][percentage]" class="eob-input"></td>
        <td><input type="text" name="educations[__INDEX__][specialization]" class="eob-input"></td>
        <td><button type="button" class="eob-btn eob-btn-danger eob-btn-sm" data-remove-row>Remove</button></td>
    </tr>
</template>

<template id="employmentRowTemplate">
    <tr>
        <td><input type="text" name="employments[__INDEX__][organisation]" class="eob-input"></td>
        <td><input type="text" name="employments[__INDEX__][designation]" class="eob-input"></td>
        <td><input type="date" name="employments[__INDEX__][period_from]" class="eob-input"></td>
        <td><input type="date" name="employments[__INDEX__][period_to]" class="eob-input"></td>
        <td><input type="text" name="employments[__INDEX__][annual_ctc]" class="eob-input"></td>
        <td><button type="button" class="eob-btn eob-btn-danger eob-btn-sm" data-remove-row>Remove</button></td>
    </tr>
</template>

<template id="familyRowTemplate">
    <tr>
        <td><input type="text" name="family_details[__INDEX__][name]" class="eob-input"></td>
        <td><input type="text" name="family_details[__INDEX__][relation]" class="eob-input"></td>
        <td><input type="text" name="family_details[__INDEX__][occupation]" class="eob-input"></td>
        <td><input type="date" name="family_details[__INDEX__][date_of_birth]" class="eob-input"></td>
        <td><input type="text" name="family_details[__INDEX__][mobile_no]" class="eob-input"></td>
        <td><button type="button" class="eob-btn eob-btn-danger eob-btn-sm" data-remove-row>Remove</button></td>
    </tr>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('employeeWizardForm');
    const maritalInputs = document.querySelectorAll('input[name="marital_status"]');
    const marriageDateGroup = document.getElementById('marriageDateGroup');
    const roleSelect = document.getElementById('employeeRoleSelect');
    const departmentSelect = document.getElementById('employeeDepartmentSelect');
    const stepButtons = Array.from(document.querySelectorAll('.eob-wizard-step-btn'));
    const panels = Array.from(document.querySelectorAll('.eob-wizard-panel'));
    const prevButton = document.getElementById('wizardPrevBtn');
    const nextButton = document.getElementById('wizardNextBtn');
    const submitButton = document.getElementById('wizardSubmitBtn');
    const stepCounter = document.getElementById('wizardStepCounter');
    const progressBar = document.getElementById('wizardProgressBar');
    const activeTitle = document.getElementById('wizardActiveTitle');
    const activeSub = document.getElementById('wizardActiveSub');

    function toggleMarriageDate() {
        const selected = document.querySelector('input[name="marital_status"]:checked');
        marriageDateGroup.style.display = selected && selected.value === 'married' ? '' : 'none';
    }

    maritalInputs.forEach(function (input) {
        input.addEventListener('change', toggleMarriageDate);
    });
    toggleMarriageDate();

    const allRoleOptions = roleSelect ? Array.from(roleSelect.querySelectorAll('option')).map(function (option) {
        return {
            value: option.value,
            label: option.textContent,
            departmentId: option.getAttribute('data-department-id') || '',
            selected: option.selected
        };
    }) : [];

    function syncDepartmentFromRole(force) {
        if (!roleSelect || !departmentSelect) {
            return;
        }

        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const departmentId = selectedOption ? selectedOption.getAttribute('data-department-id') : '';

        if (!departmentId) {
            return;
        }

        if (force || !departmentSelect.value) {
            departmentSelect.value = departmentId;
        }
    }

    function filterRolesByDepartment() {
        if (!roleSelect || !departmentSelect) {
            return;
        }

        const selectedDepartmentId = departmentSelect.value;
        const selectedRoleId = roleSelect.value;
        const filteredRoleOptions = allRoleOptions.filter(function (option) {
            if (option.value === '') {
                return true;
            }

            if (selectedDepartmentId === '') {
                return true;
            }

            return option.departmentId === selectedDepartmentId;
        });

        roleSelect.innerHTML = '';

        filteredRoleOptions.forEach(function (option) {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.label;

            if (option.departmentId) {
                optionElement.setAttribute('data-department-id', option.departmentId);
            }

            if (option.value === selectedRoleId) {
                optionElement.selected = true;
            }

            roleSelect.appendChild(optionElement);
        });

        const selectedRoleStillExists = filteredRoleOptions.some(function (option) {
            return option.value === selectedRoleId;
        });

        if (!selectedRoleStillExists) {
            roleSelect.value = '';
        }
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            syncDepartmentFromRole(true);
        });
    }

    if (departmentSelect) {
        departmentSelect.addEventListener('change', function () {
            filterRolesByDepartment();
        });
    }

    if (departmentSelect && !departmentSelect.value && roleSelect && roleSelect.value) {
        syncDepartmentFromRole(true);
    }

    filterRolesByDepartment();

    const salaryInputs = {
        grossSalary: form.querySelector('[data-salary-input="gross_salary"]'),
        basicSalary: form.querySelector('[data-salary-input="basic_salary"]'),
        professionalTax: form.querySelector('[data-salary-input="professional_tax"]'),
        tdsAmount: form.querySelector('[data-salary-input="tds_amount"]'),
        loanDeduction: form.querySelector('[data-salary-input="loan_deduction"]'),
        otherDeduction: form.querySelector('[data-salary-input="other_deduction"]'),
        pfToggle: form.querySelector('[data-salary-toggle="pf"]'),
        esiToggle: form.querySelector('[data-salary-toggle="esi"]'),
        pfEmployeeContribution: form.querySelector('[data-salary-output="pf_employee_contribution"]'),
        pfEmployerContribution: form.querySelector('[data-salary-output="pf_employer_contribution"]'),
        esiEmployeeContribution: form.querySelector('[data-salary-output="esi_employee_contribution"]'),
        esiEmployerContribution: form.querySelector('[data-salary-output="esi_employer_contribution"]'),
        totalDeduction: form.querySelector('[data-salary-output="total_deduction"]'),
        netSalary: form.querySelector('[data-salary-output="net_salary"]')
    };

    function parseAmount(input) {
        const value = input ? parseFloat(input.value) : 0;
        return Number.isFinite(value) ? value : 0;
    }

    function formatAmount(value) {
        return (Math.round(value * 100) / 100).toFixed(2);
    }

    function updateSalaryCalculations() {
        if (!salaryInputs.grossSalary || !salaryInputs.netSalary) {
            return;
        }

        const grossSalary = parseAmount(salaryInputs.grossSalary);
        const basicSalary = parseAmount(salaryInputs.basicSalary);
        const professionalTax = parseAmount(salaryInputs.professionalTax);
        const tdsAmount = parseAmount(salaryInputs.tdsAmount);
        const loanDeduction = parseAmount(salaryInputs.loanDeduction);
        const otherDeduction = parseAmount(salaryInputs.otherDeduction);
        const pfEnabled = salaryInputs.pfToggle ? salaryInputs.pfToggle.checked : false;
        const esiEnabled = salaryInputs.esiToggle ? salaryInputs.esiToggle.checked : false;

        const pfEmployee = pfEnabled ? basicSalary * 0.12 : 0;
        const pfEmployer = pfEnabled ? basicSalary * 0.12 : 0;
        const esiApplicableGross = grossSalary <= 21000 ? grossSalary : 0;
        const esiEmployee = esiEnabled ? esiApplicableGross * 0.0075 : 0;
        const esiEmployer = esiEnabled ? esiApplicableGross * 0.0325 : 0;
        const totalDeduction = pfEmployee + esiEmployee + professionalTax + tdsAmount + loanDeduction + otherDeduction;
        const netSalary = Math.max(grossSalary - totalDeduction, 0);

        if (salaryInputs.pfEmployeeContribution) {
            salaryInputs.pfEmployeeContribution.value = formatAmount(pfEmployee);
        }
        if (salaryInputs.pfEmployerContribution) {
            salaryInputs.pfEmployerContribution.value = formatAmount(pfEmployer);
        }
        if (salaryInputs.esiEmployeeContribution) {
            salaryInputs.esiEmployeeContribution.value = formatAmount(esiEmployee);
        }
        if (salaryInputs.esiEmployerContribution) {
            salaryInputs.esiEmployerContribution.value = formatAmount(esiEmployer);
        }
        if (salaryInputs.totalDeduction) {
            salaryInputs.totalDeduction.value = formatAmount(totalDeduction);
        }
        if (salaryInputs.netSalary) {
            salaryInputs.netSalary.value = formatAmount(netSalary);
        }
    }

    [
        salaryInputs.grossSalary,
        salaryInputs.basicSalary,
        salaryInputs.professionalTax,
        salaryInputs.tdsAmount,
        salaryInputs.loanDeduction,
        salaryInputs.otherDeduction,
        salaryInputs.pfToggle,
        salaryInputs.esiToggle
    ].forEach(function (input) {
        input?.addEventListener('input', updateSalaryCalculations);
        input?.addEventListener('change', updateSalaryCalculations);
    });

    updateSalaryCalculations();

    const repeaters = {
        education: { tbody: document.getElementById('educationRows'), template: document.getElementById('educationRowTemplate') },
        employment: { tbody: document.getElementById('employmentRows'), template: document.getElementById('employmentRowTemplate') },
        family: { tbody: document.getElementById('familyRows'), template: document.getElementById('familyRowTemplate') }
    };

    document.querySelectorAll('[data-add-row]').forEach(function (button) {
        button.addEventListener('click', function () {
            const type = button.getAttribute('data-add-row');
            const config = repeaters[type];
            if (!config) {
                return;
            }

            const index = config.tbody.querySelectorAll('tr').length;
            const html = config.template.innerHTML.split('__INDEX__').join(index);
            config.tbody.insertAdjacentHTML('beforeend', html);
        });
    });

    document.addEventListener('click', function (event) {
        const removeButton = event.target.closest('[data-remove-row]');
        if (!removeButton) {
            return;
        }

        const row = removeButton.closest('tr');
        const tbody = row ? row.parentElement : null;

        if (!row || !tbody || tbody.querySelectorAll('tr').length === 1) {
            row?.querySelectorAll('input').forEach(function (input) {
                input.value = '';
            });
            return;
        }

        row.remove();
    });

    let activeIndex = Math.max(0, panels.findIndex(function (panel) {
        return panel.getAttribute('data-step') === form.getAttribute('data-initial-step');
    }));

    function updateWizard() {
        panels.forEach(function (panel, index) {
            panel.classList.toggle('is-active', index === activeIndex);
        });

        stepButtons.forEach(function (button, index) {
            button.classList.toggle('is-active', index === activeIndex);
            button.classList.toggle('is-complete', index < activeIndex);
        });

        const activePanel = panels[activeIndex];
        const percent = ((activeIndex + 1) / panels.length) * 100;

        stepCounter.textContent = 'Step ' + (activeIndex + 1) + ' of ' + panels.length;
        progressBar.style.width = percent + '%';
        activeTitle.textContent = activePanel.getAttribute('data-step-title');
        activeSub.textContent = activePanel.getAttribute('data-step-sub');

        prevButton.style.display = activeIndex === 0 ? 'none' : 'inline-flex';
        nextButton.style.display = activeIndex === panels.length - 1 ? 'none' : 'inline-flex';
        submitButton.style.display = activeIndex === panels.length - 1 ? 'inline-flex' : 'none';
    }

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

    stepButtons.forEach(function (button, index) {
        button.addEventListener('click', function () {
            if (index <= activeIndex) {
                activeIndex = index;
                updateWizard();
                return;
            }

            if (validateCurrentStep()) {
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

    updateWizard();
});
</script>
@endpush
