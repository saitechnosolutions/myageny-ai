@extends('layouts.app')

@section('title', 'Permission Requests')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
    <style>
        .pr-grid { display:grid; grid-template-columns:1fr; gap:16px; }
        .pr-status { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; text-transform:capitalize; }
        .pr-status-pending { background:#fff7ed; color:#c2410c; }
        .pr-status-approved { background:#f0fdf4; color:#15803d; }
        .pr-status-rejected { background:#fef2f2; color:#b91c1c; }
        .pr-status-skipped { background:#f4f4f5; color:#71717a; }
        .pr-pill { display:inline-flex; align-items:center; padding:4px 9px; border-radius:999px; background:#eef4ff; color:#3355aa; font-size:11px; font-weight:800; }
        .pr-empty { padding:28px; text-align:center; color:#9e9e9e; font-size:13px; }
    </style>
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Permission Requests</div>
            <div class="eob-breadcrumb">HRMS > Permission Requests</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('hrms.dashboard') }}" class="eob-btn eob-btn-ghost">Back</a>
            <a href="{{ route('permission-requests.create') }}" class="eob-btn eob-btn-primary">New Permission Request</a>
        </div>
    </div>

    <div class="eob-body">
        @if(session('success'))
            <div class="eob-alert eob-alert-success">{!! session('success') !!}</div>
        @endif
        @if(session('error'))
            <div class="eob-alert eob-alert-error">{{ session('error') }}</div>
        @endif

        <div class="pr-grid">
            <div class="eob-table-card">
                <div class="eob-card-head">
                    <div>
                        <div class="eob-card-title">Waiting For My Approval</div>
                        <div class="eob-card-sub">Permission requests currently assigned to your hierarchy stage.</div>
                    </div>
                    <div class="eob-results">{{ $pendingApprovals->count() }} pending</div>
                </div>

                @if($pendingApprovals->isEmpty())
                    <div class="pr-empty">No permission approvals are waiting for you.</div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="eob-list-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date / Time</th>
                                    <th>Stage</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingApprovals as $approval)
                                    <tr>
                                        <td>
                                            <div class="eob-cell-title">{{ $approval->permissionRequest->employee?->name ?: $approval->permissionRequest->user?->name }}</div>
                                            <div class="eob-cell-sub">{{ $approval->permissionRequest->employee?->employee_id ?: $approval->permissionRequest->user?->email }}</div>
                                        </td>
                                        <td>
                                            <div class="eob-cell-title">{{ $approval->permissionRequest->permission_date->format('d M Y') }}</div>
                                            <div class="eob-cell-sub">{{ \Carbon\Carbon::parse($approval->permissionRequest->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($approval->permissionRequest->to_time)->format('h:i A') }}</div>
                                        </td>
                                        <td><span class="pr-pill">{{ $approval->step_name }}</span></td>
                                        <td>{{ optional($approval->permissionRequest->submitted_at)->format('d M Y') }}</td>
                                        <td><a href="{{ route('permission-requests.show', $approval->permissionRequest) }}" class="eob-btn eob-btn-primary eob-btn-sm">Review</a></td>
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
                        <div class="eob-card-title">My Permission Requests</div>
                        <div class="eob-card-sub">Track your permission request hierarchy approval status.</div>
                    </div>
                    <div class="eob-results">{{ $permissionRequests->total() }} request(s)</div>
                </div>

                @if($permissionRequests->isEmpty())
                    <div class="pr-empty">No permission requests submitted yet.</div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="eob-list-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Current Stage</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissionRequests as $permissionRequest)
                                    @php $currentApproval = $permissionRequest->approvals->firstWhere('step_key', $permissionRequest->current_step); @endphp
                                    <tr>
                                        <td>{{ $permissionRequest->permission_date->format('d M Y') }}</td>
                                        <td>
                                            <div class="eob-cell-title">{{ \Carbon\Carbon::parse($permissionRequest->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($permissionRequest->to_time)->format('h:i A') }}</div>
                                            <div class="eob-cell-sub">{{ $permissionRequest->total_minutes }} minutes</div>
                                        </td>
                                        <td><span class="pr-status pr-status-{{ $permissionRequest->status }}">{{ $permissionRequest->status }}</span></td>
                                        <td>
                                            @if($permissionRequest->status === 'pending')
                                                {{ $currentApproval?->step_name ?: 'Waiting' }}
                                            @elseif($permissionRequest->status === 'approved')
                                                Completed
                                            @else
                                                Rejected
                                            @endif
                                        </td>
                                        <td>{{ optional($permissionRequest->submitted_at)->format('d M Y') }}</td>
                                        <td><a href="{{ route('permission-requests.show', $permissionRequest) }}" class="eob-icon-btn" title="View">V</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($permissionRequests->hasPages())
                        @include('partials.table-pagination', ['paginator' => $permissionRequests])
                    @endif
                @endif
            </div>

            <div class="eob-table-card">
                <div class="eob-card-head">
                    <div>
                        <div class="eob-card-title">My Recent Decisions</div>
                        <div class="eob-card-sub">Permission approvals or rejections you completed recently.</div>
                    </div>
                    <div class="eob-results">{{ $handledApprovals->count() }} item(s)</div>
                </div>

                @if($handledApprovals->isEmpty())
                    <div class="pr-empty">No permission decisions recorded yet.</div>
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
                                            <div class="eob-cell-title">{{ $approval->permissionRequest->employee?->name ?: $approval->permissionRequest->user?->name }}</div>
                                            <div class="eob-cell-sub">{{ $approval->permissionRequest->permission_date->format('d M Y') }}</div>
                                        </td>
                                        <td>{{ $approval->step_name }}</td>
                                        <td><span class="pr-status pr-status-{{ $approval->status }}">{{ $approval->status }}</span></td>
                                        <td>{{ optional($approval->actioned_at)->format('d M Y h:i A') }}</td>
                                        <td><a href="{{ route('permission-requests.show', $approval->permissionRequest) }}" class="eob-icon-btn" title="View">V</a></td>
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
