@extends('layouts.app')

@section('title', 'Add Visitor')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Add Visitor</div>
            <div class="eob-breadcrumb">HRMS > Visitor Management > Create</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('visitor-management.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        @if($errors->any())
            <div class="eob-alert eob-alert-error">Please fix the highlighted fields and submit again.</div>
        @endif

        <form method="POST" action="{{ route('visitor-management.store') }}" class="eob-card">
            @csrf
            <div class="eob-card-head">
                <div>
                    <div class="eob-card-title">Visitor Details</div>
                    <div class="eob-card-sub">Create a visitor entry manually.</div>
                </div>
            </div>
            <div class="eob-card-body">
                @include('pages.hrms.visitor_management.form')
            </div>
            <div class="eob-foot">
                <a href="{{ route('visitor-management.index') }}" class="eob-btn eob-btn-ghost">Cancel</a>
                <button type="submit" class="eob-btn eob-btn-primary">Save Visitor</button>
            </div>
        </form>
    </div>
</div>
@endsection
