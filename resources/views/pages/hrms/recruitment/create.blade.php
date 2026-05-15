@extends('layouts.app')

@section('title', 'Add Recruitment Candidate')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
    @include('pages.hrms.recruitment.styles')
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Add Candidate</div>
            <div class="eob-breadcrumb">HRMS > Recruitment > Add Candidate</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('recruitment.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        @if($errors->any())
            <div class="eob-alert eob-alert-error">Please review the highlighted fields and try again.</div>
        @endif

        <form method="POST" action="{{ route('recruitment.store') }}" enctype="multipart/form-data" class="eob-card">
            @csrf
            <div class="eob-card-head">
                <div>
                    <div class="eob-card-title">Candidate Details</div>
                    <div class="eob-card-sub">Store the applicant profile, resume, and job applied details.</div>
                </div>
                <div class="rec-chip rec-chip-applied">{{ $candidateNo }}</div>
            </div>
            <div class="eob-card-body">
                <div class="eob-form-grid">
                    <div class="eob-group">
                        <label class="eob-label">Employee Name <span class="eob-label-required">*</span></label>
                        <input type="text" name="name" class="eob-input" value="{{ old('name') }}" required>
                        @error('name')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Mobile Number <span class="eob-label-required">*</span></label>
                        <input type="text" name="mobile_number" class="eob-input" value="{{ old('mobile_number') }}" required>
                        @error('mobile_number')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Email</label>
                        <input type="email" name="email" class="eob-input" value="{{ old('email') }}">
                        @error('email')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Location</label>
                        <input type="text" name="location" class="eob-input" value="{{ old('location') }}">
                        @error('location')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Job Applied For <span class="eob-label-required">*</span></label>
                        <input type="text" name="job_title" class="eob-input" value="{{ old('job_title') }}" placeholder="PHP Developer, HR Executive..." required>
                        @error('job_title')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Source</label>
                        <input type="text" name="source" class="eob-input" value="{{ old('source') }}" placeholder="LinkedIn, Naukri, Referral">
                        @error('source')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Experience Years</label>
                        <input type="number" name="experience_years" min="0" max="60" class="eob-input" value="{{ old('experience_years') }}">
                        @error('experience_years')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Notice Period</label>
                        <input type="text" name="notice_period" class="eob-input" value="{{ old('notice_period') }}" placeholder="Immediate, 30 days">
                        @error('notice_period')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Current CTC</label>
                        <input type="number" step="0.01" min="0" name="current_ctc" class="eob-input" value="{{ old('current_ctc') }}">
                        @error('current_ctc')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group">
                        <label class="eob-label">Expected CTC</label>
                        <input type="number" step="0.01" min="0" name="expected_ctc" class="eob-input" value="{{ old('expected_ctc') }}">
                        @error('expected_ctc')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group full">
                        <label class="eob-label">Resume</label>
                        <input type="file" name="resume" class="eob-input" accept=".pdf,.doc,.docx">
                        <div class="eob-help">PDF, DOC, DOCX up to 5 MB.</div>
                        @error('resume')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="eob-group full">
                        <label class="eob-label">Remarks</label>
                        <textarea name="remarks" class="eob-textarea" placeholder="Initial notes from HR, referral details, screening context...">{{ old('remarks') }}</textarea>
                        @error('remarks')<div class="eob-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="eob-foot">
                <a href="{{ route('recruitment.index') }}" class="eob-btn eob-btn-ghost">Cancel</a>
                <button type="submit" class="eob-btn eob-btn-primary">Save Candidate</button>
            </div>
        </form>
    </div>
</div>
@endsection
