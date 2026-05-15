@extends('layouts.app')

@section('title', 'Edit Employee Onboarding - ' . $employee->name)

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Edit Employee Onboarding</div>
            <div class="eob-breadcrumb">HRMS > Employee Onboarding > {{ $employee->name }} > Edit</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('employee-onboarding.show', $employee) }}" class="eob-btn eob-btn-ghost">View</a>
            <a href="{{ route('employee-onboarding.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        @if($errors->any())
            <div class="eob-alert eob-alert-error">Please review the highlighted fields and try again.</div>
        @endif

        @include('pages.hrms.employee_onboarding.form', [
            'employee' => $employee,
            'generatedEmployeeId' => $generatedEmployeeId,
            'action' => route('employee-onboarding.update', $employee),
            'method' => 'PUT',
            'submitLabel' => 'Save Changes',
            'cancelRoute' => route('employee-onboarding.show', $employee),
            'documentLabels' => $documentLabels,
            'roles' => $roles,
            'departments' => $departments,
            'branches' => $branches,
            'tlUsers' => $tlUsers,
        ])
    </div>
</div>
@endsection
