@extends('layouts.app')

@section('title', 'Employee Onboarding')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Employee Onboarding</div>
            <div class="eob-breadcrumb">HRMS > Employee Onboarding</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('dashboard') }}" class="eob-btn eob-btn-ghost">Back</a>
            <a href="{{ route('employee-onboarding.create') }}" class="eob-btn eob-btn-primary">Add Employee</a>
        </div>
    </div>

    <div class="eob-body">
        @if(session('success'))
            <div class="eob-alert eob-alert-success">{!! session('success') !!}</div>
        @endif
        @if(session('error'))
            <div class="eob-alert eob-alert-error">{{ session('error') }}</div>
        @endif

        <div class="eob-filter-card">
            <form method="GET" action="{{ route('employee-onboarding.index') }}" class="eob-filter-form">
                <div class="eob-field">
                    <label class="eob-label">Search</label>
                    <input type="text" name="search" class="eob-input" value="{{ request('search') }}" placeholder="Employee ID, name, email, mobile, aadhaar">
                </div>
                <div class="eob-field" style="max-width:220px;">
                    <label class="eob-label">Status</label>
                    <select name="status" class="eob-select">
                        <option value="">All Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="verified" @selected(request('status') === 'verified')>Verified</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    </select>
                </div>
                <div class="eob-actions">
                    <button type="submit" class="eob-btn eob-btn-primary">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('employee-onboarding.index') }}" class="eob-btn eob-btn-ghost">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="eob-table-card">
            <div class="eob-card-head">
                <div>
                    <div class="eob-card-title">Onboarding Records</div>
                    <div class="eob-card-sub">Track candidate profiles, uploaded documents, and onboarding status.</div>
                </div>
                <div class="eob-results">{{ $employees->total() }} employee(s)</div>
            </div>

            @if($employees->isEmpty())
                <div class="eob-empty">No onboarding records found.</div>
            @else
                <div style="overflow-x:auto;">
                    <table class="eob-list-table">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee</th>
                                <th>Role / Department</th>
                                <th>Contact</th>
                                <th>DOB</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="eob-cell-title">{{ $employee->employee_id }}</div>
                                    </td>
                                    <td>
                                        <div class="eob-cell-title">{{ $employee->name }}</div>
                                        <div class="eob-cell-sub">{{ $employee->father_name ?: 'Father name not added' }}</div>
                                    </td>
                                    <td>
                                        <div class="eob-cell-title">{{ $employee->role?->display_name ?: ($employee->role?->name ?: 'No role') }}</div>
                                        <div class="eob-cell-sub">{{ $employee->department?->name ?: 'No department' }}</div>
                                    </td>
                                    <td>
                                        <div class="eob-cell-title">{{ $employee->mobile }}</div>
                                        <div class="eob-cell-sub">{{ $employee->email }}</div>
                                    </td>
                                    <td>{{ optional($employee->date_of_birth)->format('d M Y') }}</td>
                                    <td>
                                        <span class="eob-chip eob-chip-{{ $employee->status }}">{{ ucfirst($employee->status) }}</span>
                                    </td>
                                    <td>{{ $employee->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="eob-inline-actions">
                                            <a href="{{ route('employee-onboarding.show', $employee) }}" class="eob-icon-btn" title="View">V</a>
                                            <a href="{{ route('employee-onboarding.edit', $employee) }}" class="eob-icon-btn" title="Edit">E</a>
                                            <button
                                                type="button"
                                                class="eob-icon-btn danger"
                                                title="Delete"
                                                data-delete-trigger
                                                data-name="{{ $employee->name }}"
                                                data-action="{{ route('employee-onboarding.destroy', $employee) }}"
                                            >D</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($employees->hasPages())
                    @include('partials.table-pagination', ['paginator' => $employees])
                @endif
            @endif
        </div>
    </div>
</div>

<div class="eob-modal" id="deleteModal">
    <div class="eob-modal-card">
        <div class="eob-modal-head">
            <div class="eob-modal-title">Delete employee record?</div>
            <div class="eob-modal-copy">This will remove the onboarding profile, repeated details, and all uploaded files for <strong id="deleteEmployeeName">this employee</strong>.</div>
        </div>
        <div class="eob-modal-foot">
            <button type="button" class="eob-btn eob-btn-ghost" id="deleteModalCancel">Cancel</button>
            <form method="POST" id="deleteModalForm">
                @csrf
                @method('DELETE')
                <button type="submit" class="eob-btn eob-btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deleteModal');
    const modalForm = document.getElementById('deleteModalForm');
    const modalName = document.getElementById('deleteEmployeeName');
    const cancelButton = document.getElementById('deleteModalCancel');

    document.querySelectorAll('[data-delete-trigger]').forEach(function (button) {
        button.addEventListener('click', function () {
            modalForm.setAttribute('action', button.getAttribute('data-action'));
            modalName.textContent = button.getAttribute('data-name');
            modal.classList.add('is-open');
        });
    });

    cancelButton.addEventListener('click', function () {
        modal.classList.remove('is-open');
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.remove('is-open');
        }
    });
});
</script>
@endpush
