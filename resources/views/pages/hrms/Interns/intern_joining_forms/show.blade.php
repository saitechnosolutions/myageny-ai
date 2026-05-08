@extends('layouts.app')

@section('title', 'View Intern Joining Form')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
    @include('pages.hrms.Interns.intern_joining_forms.styles')
@endpush

@section('content')
<div class="intern-page">
    <div class="intern-shell">
        <div class="eob-topbar">
            <div>
                <div class="eob-title">{{ $form->name }}</div>
                <div class="eob-breadcrumb">HRMS > Intern Joining Forms > View</div>
            </div>
            <div class="eob-actions">
                <a href="{{ route('interns.edit', $form) }}" class="eob-btn eob-btn-primary">Edit</a>
                <a href="{{ route('interns.index') }}" class="eob-btn eob-btn-ghost">Back</a>
            </div>
        </div>
        <div class="intern-body">
            @if(session('success'))
                <div class="intern-alert intern-alert-success">{!! session('success') !!}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="intern-card p-4 h-100">
                        @if($form->photograph)
                            <img src="{{ asset('storage/' . $form->photograph) }}" alt="{{ $form->name }}" class="intern-avatar-preview mb-3">
                        @endif
                        <h4 class="fw-bold">{{ $form->name }}</h4>
                        <div class="text-secondary">{{ $form->email }}</div>
                        <hr>
                        <dl class="review-list mb-0">
                            <dt>Mobile</dt><dd>{{ $form->mobile }}</dd>
                            <dt>Date of Birth</dt><dd>{{ optional($form->date_of_birth)->format('d M Y') ?: 'N/A' }}</dd>
                            <dt>Blood Group</dt><dd>{{ $form->blood_group ?: 'N/A' }}</dd>
                            <dt>Marital Status</dt><dd>{{ ucfirst($form->marital_status) }}</dd>
                            <dt>Date of Marriage</dt><dd>{{ optional($form->date_of_marriage)->format('d M Y') ?: 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="intern-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">Personal Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Father's Name:</strong> {{ $form->father_name }}</div>
                            <div class="col-md-6"><strong>Aadhaar Card No:</strong> {{ $form->aadhaar_card_no }}</div>
                            <div class="col-md-6"><strong>Pan Card No:</strong> {{ $form->pan_card_no }}</div>
                            <div class="col-md-6"><strong>Emergency Contact Name:</strong> {{ $form->emergency_contact_name }}</div>
                            <div class="col-md-6"><strong>Emergency Relation:</strong> {{ $form->emergency_contact_relation }}</div>
                            <div class="col-md-6"><strong>Emergency Contact No:</strong> {{ $form->emergency_contact_no }}</div>
                            <div class="col-md-6"><strong>Correspondence Address:</strong><br>{{ $form->correspondence_address }}</div>
                            <div class="col-md-6"><strong>Permanent Address:</strong><br>{{ $form->permanent_address }}</div>
                        </div>
                    </div>

                    <div class="intern-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">Educational Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Qualification</th>
                                        <th>Institution</th>
                                        <th>Year</th>
                                        <th>Percentage</th>
                                        <th>Specialization</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($form->educationalDetails as $row)
                                        <tr>
                                            <td>{{ $row->qualification }}</td>
                                            <td>{{ $row->institution_name }}</td>
                                            <td>{{ $row->year_of_passing }}</td>
                                            <td>{{ $row->percentage }}</td>
                                            <td>{{ $row->specialization ?: 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="intern-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">Employment Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Organisation</th>
                                        <th>Designation</th>
                                        <th>Period From</th>
                                        <th>Period To</th>
                                        <th>Annual CTC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($form->employmentDetails as $row)
                                        <tr>
                                            <td>{{ $row->organisation ?: 'N/A' }}</td>
                                            <td>{{ $row->designation ?: 'N/A' }}</td>
                                            <td>{{ optional($row->period_from)->format('d M Y') ?: 'N/A' }}</td>
                                            <td>{{ optional($row->period_to)->format('d M Y') ?: 'N/A' }}</td>
                                            <td>{{ $row->annual_ctc ?: 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-secondary">No employment history added.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="intern-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">Family Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Relation</th>
                                        <th>Occupation</th>
                                        <th>Date of Birth</th>
                                        <th>Mobile No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($form->familyDetails as $row)
                                        <tr>
                                            <td>{{ $row->name ?: 'N/A' }}</td>
                                            <td>{{ $row->relation ?: 'N/A' }}</td>
                                            <td>{{ $row->occupation ?: 'N/A' }}</td>
                                            <td>{{ optional($row->date_of_birth)->format('d M Y') ?: 'N/A' }}</td>
                                            <td>{{ $row->mobile_no ?: 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-secondary">No family details added.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="intern-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">Document Downloads</h5>
                        <div class="intern-doc-grid">
                            @foreach($documentLabels as $field => $label)
                                <div class="intern-doc-card">
                                    <div class="fw-semibold mb-2">{{ $label }}</div>
                                    @if($form->documents?->{$field})
                                        <a href="{{ asset('storage/' . $form->documents->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download</a>
                                    @else
                                        <div class="text-secondary small">Not uploaded</div>
                                    @endif
                                </div>
                            @endforeach
                            <div class="intern-doc-card">
                                <div class="fw-semibold mb-2">Signature</div>
                                @if($form->documents?->signature_path)
                                    <a href="{{ asset('storage/' . $form->documents->signature_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download</a>
                                @else
                                    <div class="text-secondary small">Not uploaded</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="intern-card p-4">
                        <h5 class="fw-bold mb-3">Declaration</h5>
                        <div><strong>Accepted:</strong> {{ $form->declaration_accepted ? 'Yes' : 'No' }}</div>
                        <div><strong>Date:</strong> {{ optional($form->declaration_date)->format('d M Y') }}</div>
                        <div><strong>Place:</strong> {{ $form->declaration_place }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
