@extends('layouts.app')

@section('title', 'Create Employee Onboarding')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Create Employee Onboarding</div>
            <div class="eob-breadcrumb">HRMS > Employee Onboarding > Create</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('employee-onboarding.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        @if($errors->any())
            <div class="eob-alert eob-alert-error">Please review the highlighted fields and try again.</div>
        @endif

        @include('pages.hrms.employee_onboarding.form', [
            'employee' => null,
            'generatedEmployeeId' => $generatedEmployeeId,
            'action' => route('employee-onboarding.store'),
            'method' => 'POST',
            'submitLabel' => 'Create Employee',
            'cancelRoute' => route('employee-onboarding.index'),
            'documentLabels' => $documentLabels,
            'roles' => $roles,
            'departments' => $departments,
            'branches' => $branches,
            'tlUsers' => $tlUsers,
        ])
    </div>
</div>
@endsection
