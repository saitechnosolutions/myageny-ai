@extends('layouts.app')

@section('title', 'Leave Requests')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
    <style>
        .lr-grid { display:grid; grid-template-columns:1fr; gap:16px; }
        .lr-status { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; text-transform:capitalize; }
        .lr-status-pending { background:#fff7ed; color:#c2410c; }
        .lr-status-approved { background:#f0fdf4; color:#15803d; }
        .lr-status-rejected { background:#fef2f2; color:#b91c1c; }
        .lr-status-skipped { background:#f4f4f5; color:#71717a; }
        .lr-pill { display:inline-flex; align-items:center; padding:4px 9px; border-radius:999px; background:#eef4ff; color:#3355aa; font-size:11px; font-weight:800; }
        .lr-empty { padding:28px; text-align:center; color:#9e9e9e; font-size:13px; }
    </style>
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Leave Requests</div>
            <div class="eob-breadcrumb">HRMS > Leave Requests</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('hrms.dashboard') }}" class="eob-btn eob-btn-ghost">Back</a>
            <a href="{{ route('leave-requests.create') }}" class="eob-btn eob-btn-primary">New Leave Request</a>
        </div>
    </div>

    <div class="eob-body">
        @if(session('success'))
            <div class="eob-alert eob-alert-success">{!! session('success') !!}</div>
        @endif
        @if(session('error'))
            <div class="eob-alert eob-alert-error">{{ session('error') }}</div>
        @endif

        <div class="lr-grid">
            <div class="eob-table-card">
                <div class="eob-card-head">
                    <div>
                        <div class="eob-card-title">Waiting For My Approval</div>
                        <div class="eob-card-sub">Requests currently assigned to your approval stage.</div>
                    </div>
                    <div class="eob-results">{{ $pendingApprovals->count() }} pending</div>
                </div>

                @if($pendingApprovals->isEmpty())
                    <div class="lr-empty">No leave approvals are waiting for you.</div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="eob-list-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Leave Type</th>
                                    <th>Dates</th>
                                    <th>Stage</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingApprovals as $approval)
                                    <tr>
                                        <td>
                                            <div class="eob-cell-title">{{ $approval->leaveRequest->employee?->name ?: $approval->leaveRequest->user?->name }}</div>
                                            <div class="eob-cell-sub">{{ $approval->leaveRequest->employee?->employee_id ?: $approval->leaveRequest->user?->email }}</div>
                                        </td>
                                        <td>{{ $approval->leaveRequest->leaveType?->name }}</td>
                                        <td>
                                            <div class="eob-cell-title">{{ $approval->leaveRequest->start_date->format('d M Y') }} - {{ $approval->leaveRequest->end_date->format('d M Y') }}</div>
                                            <div class="eob-cell-sub">{{ $approval->leaveRequest->total_days }} day(s)</div>
                                        </td>
                                        <td><span class="lr-pill">{{ $approval->step_name }}</span></td>
                                        <td>{{ optional($approval->leaveRequest->submitted_at)->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('leave-requests.show', $approval->leaveRequest) }}" class="eob-btn eob-btn-primary eob-btn-sm">Review</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="eob-table-card">
                <div class="eob-card-head">
                    <div>
                        <div class="eob-card-title">My Leave Requests</div>
                        <div class="eob-card-sub">Track each approval stage from TL to HR.</div>
                    </div>
                    <div class="eob-results">{{ $leaveRequests->total() }} request(s)</div>
                </div>

                @if($leaveRequests->isEmpty())
                    <div class="lr-empty">No leave requests submitted yet.</div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="eob-list-table">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Current Stage</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveRequests as $leaveRequest)
                                    @php
                                        $currentApproval = $leaveRequest->approvals->firstWhere('step_key', $leaveRequest->current_step);
                                    @endphp
                                    <tr>
                                        <td>{{ $leaveRequest->leaveType?->name }}</td>
                                        <td>
                                            <div class="eob-cell-title">{{ $leaveRequest->start_date->format('d M Y') }} - {{ $leaveRequest->end_date->format('d M Y') }}</div>
                                            <div class="eob-cell-sub">{{ $leaveRequest->total_days }} day(s)</div>
                                        </td>
                                        <td><span class="lr-status lr-status-{{ $leaveRequest->status }}">{{ $leaveRequest->status }}</span></td>
                                        <td>
                                            @if($leaveRequest->status === 'pending')
                                                {{ $currentApproval?->step_name ?: 'Waiting' }}
                                            @elseif($leaveRequest->status === 'approved')
                                                Completed
                                            @else
                                                Rejected
                                            @endif
                                        </td>
                                        <td>{{ optional($leaveRequest->submitted_at)->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="eob-icon-btn" title="View">V</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($leaveRequests->hasPages())
                        @include('partials.table-pagination', ['paginator' => $leaveRequests])
                    @endif
                @endif
            </div>

            <div class="eob-table-card">
                <div class="eob-card-head">
                    <div>
                        <div class="eob-card-title">My Recent Decisions</div>
                        <div class="eob-card-sub">Approvals or rejections you completed recently.</div>
                    </div>
                    <div class="eob-results">{{ $handledApprovals->count() }} item(s)</div>
                </div>

                @if($handledApprovals->isEmpty())
                    <div class="lr-empty">No approval decisions recorded yet.</div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="eob-list-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Stage</th>
                                    <th>Decision</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($handledApprovals as $approval)
                                    <tr>
                                        <td>
                                            <div class="eob-cell-title">{{ $approval->leaveRequest->employee?->name ?: $approval->leaveRequest->user?->name }}</div>
                                            <div class="eob-cell-sub">{{ $approval->leaveRequest->leaveType?->name }}</div>
                                        </td>
                                        <td>{{ $approval->step_name }}</td>
                                        <td><span class="lr-status lr-status-{{ $approval->status }}">{{ $approval->status }}</span></td>
                                        <td>{{ optional($approval->actioned_at)->format('d M Y h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('leave-requests.show', $approval->leaveRequest) }}" class="eob-icon-btn" title="View">V</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
