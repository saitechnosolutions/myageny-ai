@extends('layouts.app')
@section('title', 'Create Holiday')

@push('styles')
@include('pages.hrms.employee_onboarding.styles')
@include('pages.settings.partials.table-styles')
<style>
.holiday-page { display:flex; flex-direction:column; min-height:100%; background:#f4f5f7; font-family:var(--font-family, 'Inter', sans-serif); }
.crm-form-wrap { max-width:760px; margin:0 auto; background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.crm-form-head { padding:20px 24px; border-bottom:1px solid #f1f1f1; }
.crm-form-body { padding:24px; display:flex; flex-direction:column; gap:18px; }
.crm-form-foot { padding:20px 24px; border-top:1px solid #f1f1f1; display:flex; justify-content:flex-end; gap:10px; }
.crm-textarea { width:100%; min-height:120px; padding:10px 14px; border:1px solid #e1dee3; border-radius:10px; font-size:14px; outline:none; font-family:inherit; resize:vertical; }
.crm-textarea:focus { border-color:#fe5f04; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
</style>
@endpush

@section('content')
<div class="holiday-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Create Holiday</div>
            <div class="eob-breadcrumb">HRMS > Holiday Calendar > Create</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('settings.holiday-calendars.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        <div class="holiday-card" style="max-width:760px; margin:0 auto; width:100%;">
            <div class="holiday-card-head">
                <div>
                    <h2 style="margin:0;font-size:18px;font-weight:800;color:#121212;">Create Holiday</h2>
                    <p style="margin:6px 0 0;color:#7c7c7c;font-size:13px;">Add a new holiday to the company calendar.</p>
                </div>
            </div>
        </div>

        @include('pages.settings.partials.alert')

        <form method="POST" action="{{ route('settings.holiday-calendars.store') }}">
            @csrf
            <div class="crm-form-wrap">
                <div class="crm-form-head">
                    <h3 style="margin:0; font-size:16px; font-weight:700;">Holiday Details</h3>
                </div>
                <div class="crm-form-body">
                    <div>
                        <label class="crm-label">Holiday Date <span class="req">*</span></label>
                        <input type="date" name="holiday_date" class="crm-input" value="{{ old('holiday_date') }}" required>
                    </div>
                    <div>
                        <label class="crm-label">Reason for Holiday <span class="req">*</span></label>
                        <textarea name="reason" class="crm-textarea" placeholder="Example: Diwali, Christmas, Company Annual Retreat" required>{{ old('reason') }}</textarea>
                    </div>
                </div>
                <div class="crm-form-foot">
                    <a href="{{ route('settings.holiday-calendars.index') }}" class="crm-btn crm-btn-ghost">Cancel</a>
                    <button type="submit" class="crm-btn crm-btn-primary">Create Holiday</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
