@extends('layouts.app')

@section('title', 'Companies')

@push('styles')
<style>
.cmp-page { display:flex; flex-direction:column; min-height:100%; background:#f4f5f7; font-family:'Inter',sans-serif; }
.cmp-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; background:#fff; border-bottom:1px solid #e1dee3; }
.cmp-page-title { font-size:18px; font-weight:800; color:#121212; }
.cmp-breadcrumb { font-size:12px; color:#9e9e9e; margin-top:2px; }
.cmp-actions { display:flex; gap:10px; align-items:center; }
.cmp-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:13px; font-weight:700; border:none; cursor:pointer; text-decoration:none; font-family:inherit; }
.cmp-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; }
.cmp-btn-ghost { background:#fff; color:#121212; border:1px solid #e1dee3; }
.cmp-body { padding:20px 28px 32px; display:flex; flex-direction:column; gap:16px; }
.cmp-alert { padding:12px 16px; border-radius:10px; font-size:13px; display:flex; align-items:center; gap:10px; }
.cmp-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.cmp-filter-card, .cmp-table-card { background:#fff; border:1px solid #e1dee3; border-radius:14px; overflow:hidden; }
.cmp-filter-form { display:flex; gap:10px; flex-wrap:wrap; padding:16px; align-items:end; }
.cmp-field { display:flex; flex-direction:column; gap:6px; min-width:200px; flex:1; }
.cmp-label { font-size:12px; font-weight:700; color:#666; }
.cmp-input, .cmp-select { width:100%; padding:10px 12px; border:1px solid #e1dee3; border-radius:10px; font-size:14px; font-family:inherit; outline:none; background:#fff; }
.cmp-input:focus, .cmp-select:focus { border-color:#fe5f04; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.cmp-table-head { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid #f0eef2; }
.cmp-table-title { font-size:15px; font-weight:700; color:#121212; }
.cmp-table-sub { font-size:11px; color:#9e9e9e; margin-top:2px; }
.cmp-results { font-size:12px; color:#9e9e9e; }
.cmp-table { width:100%; border-collapse:collapse; }
.cmp-table th { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#9e9e9e; padding:10px 16px; text-align:left; border-bottom:1px solid #f0eef2; background:#fafafa; }
.cmp-table td { padding:14px 16px; border-bottom:1px solid #f7f6f9; font-size:13px; color:#121212; vertical-align:middle; }
.cmp-table tbody tr:hover td { background:#fdf9f6; }
.cmp-badge { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; border:1px solid transparent; }
.cmp-active { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
.cmp-inactive { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
.cmp-cell-title { font-size:13px; font-weight:700; color:#121212; }
.cmp-cell-sub { font-size:11px; color:#9e9e9e; margin-top:2px; }
.cmp-actions-row { display:flex; gap:6px; align-items:center; }
.cmp-icon-btn { width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e1dee3; background:#fafafa; color:#7c7c7c; text-decoration:none; cursor:pointer; }
.cmp-icon-btn:hover { border-color:#fe5f04; color:#fe5f04; background:#fff7ed; }
.cmp-icon-btn.danger:hover { border-color:#dc2626; color:#dc2626; background:#fef2f2; }
.cmp-empty { text-align:center; padding:56px 20px; color:#9e9e9e; }
.cmp-pagination { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-top:1px solid #f0eef2; gap:10px; flex-wrap:wrap; }
.cmp-page-links { display:flex; gap:6px; }
.cmp-page-link { padding:6px 10px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; color:#666; border:1px solid #e1dee3; }
.cmp-page-link.active, .cmp-page-link:hover { background:#fe5f04; border-color:#fe5f04; color:#fff; }
.cmp-page-link.disabled { opacity:.4; pointer-events:none; }
@media (max-width: 768px) {
    .cmp-topbar { height:auto; padding:16px 20px; flex-direction:column; align-items:flex-start; gap:12px; }
    .cmp-body { padding:16px 20px 24px; }
    .cmp-filter-form { flex-direction:column; align-items:stretch; }
    .cmp-field { min-width:100%; }
}
</style>
@endpush

@section('content')
<div class="cmp-page">
    <div class="cmp-topbar">
        <div>
            <div class="cmp-page-title">Company Management</div>
            <div class="cmp-breadcrumb">Admin > Companies</div>
        </div>
        <div class="cmp-actions">
            <a href="{{ route('dashboard') }}" class="cmp-btn cmp-btn-ghost">Back</a>
            <a href="{{ route('companies.create') }}" class="cmp-btn cmp-btn-primary">Add Company</a>
        </div>
    </div>

    <div class="cmp-body">
        @if(session('success'))
            <div class="cmp-alert cmp-alert-success">{{ strip_tags(session('success')) }}</div>
        @endif

        <div class="cmp-filter-card">
            <form method="GET" action="{{ route('companies.index') }}" class="cmp-filter-form">
                <div class="cmp-field">
                    <label class="cmp-label" for="search">Search</label>
                    <input id="search" type="text" name="search" class="cmp-input" value="{{ request('search') }}" placeholder="Company name, email, mobile">
                </div>
                <div class="cmp-field" style="max-width:220px">
                    <label class="cmp-label" for="status">Status</label>
                    <select id="status" name="status" class="cmp-select">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Activate</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Deactivate</option>
                    </select>
                </div>
                <div class="cmp-actions">
                    <button type="submit" class="cmp-btn cmp-btn-primary">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('companies.index') }}" class="cmp-btn cmp-btn-ghost">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="cmp-table-card">
            <div class="cmp-table-head">
                <div>
                    <div class="cmp-table-title">Companies</div>
                    <div class="cmp-table-sub">Manage company profile and Facebook credentials</div>
                </div>
                <div class="cmp-results">{{ $companies->total() }} company(s)</div>
            </div>

            @if($companies->isEmpty())
                <div class="cmp-empty">No companies found.</div>
            @else
                <div style="overflow-x:auto">
                    <table class="cmp-table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Accounts</th>
                                <th>Status</th>
                                <th>Facebook Client ID</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td>
                                        <div class="cmp-cell-title">{{ $company->company_name }}</div>
                                        <div class="cmp-cell-sub">{{ \Illuminate\Support\Str::limit($company->address, 50) }}</div>
                                    </td>
                                    <td>
                                        <div class="cmp-cell-title">{{ $company->email }}</div>
                                        <div class="cmp-cell-sub">{{ $company->mobile_number }}</div>
                                    </td>
                                    <td>{{ $company->number_of_accounts }}</td>
                                    <td>
                                        <span class="cmp-badge {{ $company->company_status === 'active' ? 'cmp-active' : 'cmp-inactive' }}">
                                            {{ $company->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($company->facebook_client_id, 18) }}</td>
                                    <td>
                                        <div class="cmp-actions-row">
                                            <a href="{{ route('companies.show', $company) }}" class="cmp-icon-btn" title="View">View</a>
                                            <a href="{{ route('companies.edit', $company) }}" class="cmp-icon-btn" title="Edit">Edit</a>
                                            <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Delete this company?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="cmp-icon-btn danger" title="Delete">Del</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($companies->hasPages())
                    @include('partials.table-pagination', ['paginator' => $companies])
                @endif
                @if(false && $companies->hasPages())
                    <div class="cmp-pagination">
                        <div>Page {{ $companies->currentPage() }} of {{ $companies->lastPage() }}</div>
                        <div class="cmp-page-links">
                            @if ($companies->onFirstPage())
                                <span class="cmp-page-link disabled">Prev</span>
                            @else
                                <a href="{{ $companies->previousPageUrl() }}" class="cmp-page-link">Prev</a>
                            @endif

                            @foreach ($companies->getUrlRange(max(1, $companies->currentPage() - 2), min($companies->lastPage(), $companies->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}" class="cmp-page-link {{ $page === $companies->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach

                            @if ($companies->hasMorePages())
                                <a href="{{ $companies->nextPageUrl() }}" class="cmp-page-link">Next</a>
                            @else
                                <span class="cmp-page-link disabled">Next</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
