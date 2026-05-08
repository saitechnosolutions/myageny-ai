@extends('layouts.app')

@section('title', 'Call Updates')

@push('styles')
<style>
.cu-page { display:flex; flex-direction:column; min-height:100%; background:#f4f5f7; }
.cu-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; background:#fff; border-bottom:1px solid #e1dee3; }
.cu-title { font-size:18px; font-weight:800; color:#121212; }
.cu-crumb { font-size:12px; color:#9e9e9e; margin-top:2px; }
.cu-crumb a { color:#fe5f04; text-decoration:none; font-weight:700; }
.cu-body { padding:18px 28px 28px; display:flex; flex-direction:column; gap:14px; }
.cu-filter-card, .cu-table-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; box-shadow:0 10px 24px rgba(18,18,18,.04); overflow:hidden; }
.cu-filter-head, .cu-table-head { padding:14px 18px; border-bottom:1px solid #f1eef2; background:linear-gradient(180deg,#fffaf7 0%, #fff 100%); }
.cu-head-title { font-size:14px; font-weight:800; color:#121212; }
.cu-head-sub { font-size:11px; color:#9e9e9e; margin-top:3px; }
.cu-filter-body { padding:18px; }
.cu-row { display:grid; grid-template-columns:1.2fr 1fr 1fr 1fr 1fr auto; gap:12px; align-items:end; }
.cu-group { display:flex; flex-direction:column; gap:6px; }
.cu-label { font-size:11px; font-weight:800; color:#7c7c7c; text-transform:uppercase; letter-spacing:.4px; }
.cu-input, .cu-select {
    width:100%; padding:10px 12px; border:1px solid #e1dee3; border-radius:10px; background:#faf7f4; color:#121212;
    font-size:13px; font-family:inherit; outline:none; transition:all .15s;
}
.cu-input:focus, .cu-select:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.10); }
.cu-select, .cu-input[type="date"] { background:linear-gradient(180deg,#fff7f1 0%, #fff2e8 100%); border-color:#f7c9ac; color:#c2410c; }
.cu-actions { display:flex; align-items:center; gap:8px; }
.cu-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 14px; border-radius:10px; font-size:13px; font-weight:700; text-decoration:none; border:none; cursor:pointer; font-family:inherit; transition:all .15s; white-space:nowrap; }
.cu-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 6px 16px rgba(254,95,4,.25); }
.cu-btn-primary:hover { transform:translateY(-1px); }
.cu-btn-ghost { background:#fff; color:#7c7c7c; border:1px solid #e1dee3; }
.cu-btn-ghost:hover { border-color:#fe5f04; color:#fe5f04; }
.cu-table-wrap { overflow-x:auto; }
.cu-table { width:100%; border-collapse:collapse; }
.cu-table th { padding:11px 14px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#9e9e9e; text-align:left; background:#fafafa; border-bottom:1px solid #ece7eb; }
.cu-table td { padding:14px; font-size:13px; color:#121212; border-bottom:1px solid #f4f1f3; vertical-align:top; }
.cu-table tbody tr { cursor:pointer; }
.cu-table tbody tr:hover td { background:#fffaf7; }
.cu-lead-id { font-family:monospace; color:#7c7c7c; font-size:12px; }
.cu-client { font-weight:700; color:#121212; }
.cu-company { font-size:12px; color:#7c7c7c; margin-top:2px; }
.cu-contact a { color:#2563eb; text-decoration:none; }
.cu-contact a:hover { color:#fe5f04; text-decoration:underline; }
.cu-update-note { color:#2e2e2e; line-height:1.5; }
.cu-meta { margin-top:6px; display:flex; flex-wrap:wrap; gap:6px; }
.cu-pill { display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:999px; font-size:10px; font-weight:800; }
.cu-pill-time { background:#eff6ff; color:#2563eb; }
.cu-pill-user { background:#f0fdf4; color:#15803d; }
.cu-empty { padding:50px 20px; text-align:center; color:#9e9e9e; }
.cu-empty-title { font-size:15px; font-weight:800; color:#7c7c7c; margin-bottom:6px; }
.cu-results { font-size:12px; color:#9e9e9e; }
.cu-results strong { color:#121212; }
@media (max-width: 1180px) {
    .cu-row { grid-template-columns:1fr 1fr; }
    .cu-actions { grid-column:1 / -1; }
}
@media (max-width: 720px) {
    .cu-topbar { padding:0 18px; }
    .cu-body { padding:14px 18px 22px; }
    .cu-row { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
<div class="cu-page">
    <div class="cu-topbar">
        <div>
            <div class="cu-title">Call Updates</div>
            <div class="cu-crumb"><a href="{{ route('leads.index') }}">Leads</a> › Call Updates</div>
        </div>
    </div>

    <div class="cu-body">
        <form method="GET" action="{{ route('leads.calls.index') }}" class="cu-filter-card">
            <div class="cu-filter-head">
                <div class="cu-head-title">Filter Call Updates</div>
                <div class="cu-head-sub">Showing current date by default. Change the filters below to view other dates or users.</div>
            </div>
            <div class="cu-filter-body">
                <div class="cu-row">
                    <div class="cu-group">
                        <label class="cu-label">Search</label>
                        <input type="text" name="search" class="cu-input" value="{{ request('search') }}" placeholder="Lead ID, client, company, mobile, user, notes">
                    </div>
                    <div class="cu-group">
                        <label class="cu-label">User</label>
                        <select name="user_id" class="cu-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cu-group">
                        <label class="cu-label">Branch</label>
                        <select name="branch_id" class="cu-select">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cu-group">
                        <label class="cu-label">Date From</label>
                        <input type="date" name="date_from" class="cu-input" value="{{ $dateFrom }}">
                    </div>
                    <div class="cu-group">
                        <label class="cu-label">Date To</label>
                        <input type="date" name="date_to" class="cu-input" value="{{ $dateTo }}">
                    </div>
                    <div class="cu-actions">
                        <button type="submit" class="cu-btn cu-btn-primary">Apply</button>
                        <a href="{{ route('leads.calls.index') }}" class="cu-btn cu-btn-ghost">Today</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="cu-table-card">
            <div class="cu-table-head">
                <div class="cu-head-title">Call Update List</div>
                <div class="cu-results">
                    Showing <strong>{{ $callUpdates->firstItem() ?? 0 }}–{{ $callUpdates->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $callUpdates->total() }}</strong> records
                </div>
            </div>

            @if($callUpdates->isEmpty())
            <div class="cu-empty">
                <div class="cu-empty-title">No call updates found</div>
                <div>Try changing the date or filter options.</div>
            </div>
            @else
            <div class="cu-table-wrap">
                <table class="cu-table">
                    <thead>
                        <tr>
                            <th>Lead ID</th>
                            <th>Client Name</th>
                            <th>Company Name</th>
                            <th>Mobile / Email</th>
                            <th>Call Updates</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($callUpdates as $call)
                        <tr onclick="window.location='{{ route('leads.show', $call->lead_id) }}'">
                            <td>
                                <a href="{{ route('leads.show', $call->lead_id) }}" class="cu-lead-id" onclick="event.stopPropagation()">LD-{{ str_pad($call->lead_id, 4, '0', STR_PAD_LEFT) }}</a>
                            </td>
                            <td>
                                <div class="cu-client">{{ $call->lead?->contact_name ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="cu-client">{{ $call->lead?->company_name ?? '—' }}</div>
                            </td>
                            <td class="cu-contact">
                                @if($call->lead?->mobile_number)
                                <div><a href="tel:{{ $call->lead->mobile_number }}" onclick="event.stopPropagation()">{{ $call->lead->mobile_number }}</a></div>
                                @endif
                                @if($call->lead?->email)
                                <div class="cu-company"><a href="mailto:{{ $call->lead->email }}" onclick="event.stopPropagation()">{{ $call->lead->email }}</a></div>
                                @endif
                            </td>
                            <td>
                                <div class="cu-update-note">{{ $call->notes ?: 'No notes added.' }}</div>
                                <div class="cu-meta">
                                    <span class="cu-pill cu-pill-time">{{ $call->called_at?->format('d M Y, h:i A') ?? '—' }}</span>
                                    @if($call->outCome?->name)
                                    <span class="cu-pill" style="background:#f5f3ff;color:#6d28d9">{{ $call->outCome->name }}</span>
                                    @endif
                                    @if($call->outComeSubCategory?->name)
                                    <span class="cu-pill" style="background:#eef2ff;color:#4338ca">{{ $call->outComeSubCategory->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="cu-pill cu-pill-user">{{ $call->user?->name ?? 'System' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($callUpdates->hasPages())
                @include('partials.table-pagination', ['paginator' => $callUpdates])
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
