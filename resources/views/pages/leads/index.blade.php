@extends('layouts.app')

@section('title', 'Leads')

@push('styles')
<style>
/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   LEAD INDEX â€” Full filter + table
   Theme: Clean white, orange accent, data-rich
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.ld-page { display:flex; flex-direction:column; height:100%; overflow:hidden; background:#f4f5f7; font-family:'Inter',sans-serif; }

/* â”€â”€ Topbar â”€â”€ */
.ld-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; flex-shrink:0; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:0; z-index:30; }
.ld-page-title { font-size:18px; font-weight:800; color:#121212; }
.ld-breadcrumb { font-size:12px; color:#9e9e9e; margin-top:2px; }
.ld-topbar-right { display:flex; align-items:center; gap:10px; }
.ld-btn { display:flex; align-items:center; gap:6px; padding:8px 18px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; text-decoration:none; transition:all .15s; border:none; }
.ld-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 4px 14px rgba(254,95,4,.25); }
.ld-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(254,95,4,.35); }
.ld-btn-outline { background:#fff; color:#121212; border:1px solid #e1dee3; }
.ld-btn-outline:hover { border-color:#fe5f04; color:#fe5f04; }

/* â”€â”€ Filter Bar â”€â”€ */
.ld-filter-wrap-outer { background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:60px; z-index:25; }
.ld-filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding:10px 28px; }
.ld-filter-label { font-size:11px; font-weight:800; color:#9e9e9e; text-transform:uppercase; letter-spacing:.8px; white-space:nowrap; display:flex; align-items:center; gap:5px; margin-right:4px; }
.ld-filter-count { display:inline-flex; align-items:center; justify-content:center; width:17px; height:17px; border-radius:50%; background:#fe5f04; color:#fff; font-size:9px; font-weight:800; }
.ld-filter-group { display:flex; align-items:center; gap:7px; flex-wrap:wrap; flex:1; }
.ld-fw { position:relative; }
.ld-fi { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9e9e9e; pointer-events:none; width:12px; height:12px; }
.ld-fs, .ld-fi-input {
    appearance:none; -webkit-appearance:none;
    padding:7px 26px 7px 27px;
    background:#f8f8f8; border:1px solid #e1dee3;
    border-radius:8px; font-size:12px; font-weight:600;
    color:#2e2e2e; cursor:pointer; outline:none;
    transition:all .15s; font-family:inherit; min-width:120px;
}
.ld-fs:focus, .ld-fi-input:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.ld-fc { position:absolute; right:7px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9e9e9e; width:11px; height:11px; }
.ld-fi-input { padding:7px 10px 7px 27px; min-width:110px; }
.ld-date-range { display:flex; align-items:center; gap:5px; }
.ld-date-sep { font-size:11px; color:#9e9e9e; font-weight:600; }
.ld-qb { padding:5px 10px; border-radius:7px; background:#f8f8f8; border:1px solid #e1dee3; font-size:11px; font-weight:600; color:#7c7c7c; cursor:pointer; font-family:inherit; transition:all .15s; }
.ld-qb:hover, .ld-qb.active { background:#fff0e6; border-color:#fe5f04; color:#fe5f04; }
.ld-reset-btn { display:flex; align-items:center; gap:5px; padding:7px 12px; border-radius:8px; background:none; border:1px solid #e1dee3; font-size:12px; font-weight:600; color:#9e9e9e; cursor:pointer; font-family:inherit; transition:all .15s; }
.ld-reset-btn:hover { border-color:#dc2626; color:#dc2626; }
.ld-chips-bar { display:none; align-items:center; gap:6px; flex-wrap:wrap; padding:7px 28px; border-top:1px solid #f0eef2; }
.ld-chip { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; background:rgba(254,95,4,.09); border:1px solid rgba(254,95,4,.22); font-size:11px; font-weight:700; color:#fe5f04; cursor:pointer; }
.ld-chip:hover { background:rgba(254,95,4,.15); }
.ld-search-box { position:relative; flex:0 0 auto; }
.ld-search-ico { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9e9e9e; width:13px; height:13px; pointer-events:none; }
.ld-search-input { padding:7px 12px 7px 32px; border:1px solid #e1dee3; border-radius:8px; font-size:12px; font-family:inherit; outline:none; background:#f8f8f8; color:#121212; transition:all .15s; min-width:200px; }
.ld-search-input:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }

/* â”€â”€ Body â”€â”€ */
.ld-body { flex:1; overflow-y:auto; padding:18px 28px 32px; display:flex; flex-direction:column; gap:14px; }
.ld-body::-webkit-scrollbar { width:5px; }
.ld-body::-webkit-scrollbar-thumb { background:#e1dee3; border-radius:3px; }

@keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

/* â”€â”€ Stats â”€â”€ */
.ld-stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; animation:fadeUp .35s ease both; }
.ld-stat {
    position:relative;
    overflow:hidden;
    background:linear-gradient(180deg,#ffffff 0%, #fffdfa 100%);
    border:1px solid #e7e1de;
    border-radius:18px;
    padding:12px 14px 11px;
    transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    box-shadow:0 10px 24px rgba(18,18,18,.04);
}
.ld-stat::before {
    content:'';
    position:absolute;
    inset:0 0 auto 0;
    height:4px;
    background:var(--stat-color, #fe5f04);
}
.ld-stat:hover {
    transform:translateY(-2px);
    box-shadow:0 16px 32px rgba(18,18,18,.08);
    border-color:#ddd2cb;
}
.ld-stat-top {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom:10px;
}
.ld-stat-icon {
    width:36px;
    height:36px;
    border-radius:11px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.45);
}
.ld-stat-trend {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 9px;
    border-radius:999px;
    background:#fff7f1;
    border:1px solid #fde2cf;
    color:var(--stat-color, #fe5f04);
    font-size:10px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.04em;
    white-space:nowrap;
}
.ld-stat-label { font-size:10px; font-weight:800; color:#9e9e9e; text-transform:uppercase; letter-spacing:.5px; }
.ld-stat-value { font-size:24px; font-weight:900; color:#121212; line-height:1; margin-top:4px; letter-spacing:-.03em; }
.ld-stat-sub {
    font-size:11px;
    color:#7c7c7c;
    margin-top:8px;
    padding-top:8px;
    border-top:1px solid #f2ece8;
    line-height:1.4;
}

/* â”€â”€ Table Card â”€â”€ */
.ld-table-card { background:#fff; border:1px solid #e1dee3; border-radius:13px; overflow:hidden; animation:fadeUp .35s .1s ease both; }
.ld-table-top { display:flex; justify-content:space-between; align-items:center; padding:13px 18px; border-bottom:1px solid #f0eef2; }
.ld-table-title { font-size:14px; font-weight:700; color:#121212; }
.ld-table-sub { font-size:11px; color:#9e9e9e; margin-top:2px; }
.ld-results { font-size:12px; color:#9e9e9e; }
.ld-results strong { color:#121212; font-weight:700; }

/* Table */
.ld-tbl { width:100%; border-collapse:collapse; }
.ld-tbl th { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#9e9e9e; padding:9px 13px; text-align:left; border-bottom:1px solid #f0eef2; background:#fafafa; white-space:nowrap; }
.ld-tbl td { padding:12px 13px; font-size:13px; color:#121212; border-bottom:1px solid #f7f6f9; vertical-align:middle; }
.ld-tbl tbody tr:last-child td { border-bottom:none; }
.ld-tbl tbody tr:hover td { background:#fdf9f6; cursor:pointer; }

/* Cells */
.ld-company-cell { display:flex; align-items:center; gap:9px; }
.ld-co-logo { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; flex-shrink:0; }
.ld-co-name { font-size:13px; font-weight:700; color:#121212; }
.ld-co-contact { font-size:11px; color:#9e9e9e; margin-top:1px; }
.ld-id { font-family:monospace; font-size:11px; color:#9e9e9e; }
.ld-mobile { font-size:12px; color:#7c7c7c; font-family:monospace; }
.ld-mobile a,
.ld-email-text a { color:inherit; text-decoration:none; }
.ld-mobile a:hover,
.ld-email-text a:hover { color:#fe5f04; text-decoration:underline; }
.ld-email-text { font-size:11px; color:#9e9e9e; }
.ld-deal { font-size:13px; font-weight:700; color:#121212; }
.ld-date { font-size:12px; color:#7c7c7c; }

/* Badges */
.ld-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; border:1px solid transparent; }
.ld-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }

/* Priority badges */
.ld-pri-low    { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
.ld-pri-medium { background:#fffbeb; color:#b45309; border-color:#fde68a; }
.ld-pri-high   { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

/* Owner */
.ld-owner { display:flex; align-items:center; gap:6px; }
.ld-owner-av { width:22px; height:22px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:800; color:#fff; flex-shrink:0; }

/* Branch */
.ld-branch { font-size:11px; color:#7c7c7c; }

/* Actions */
.ld-actions { display:flex; align-items:center; gap:5px; }
.ld-act-btn { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; border:1px solid #e1dee3; background:#fafafa; cursor:pointer; color:#9e9e9e; transition:all .15s; text-decoration:none; }
.ld-act-btn:hover { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
.ld-act-btn.edit:hover { border-color:#fe5f04; color:#fe5f04; background:#fff7ed; }
.ld-act-btn.del:hover  { border-color:#dc2626; color:#dc2626; background:#fef2f2; }

/* Empty */
.ld-empty { text-align:center; padding:60px 20px; color:#9e9e9e; }
.ld-empty-icon { font-size:44px; margin-bottom:12px; }
.ld-empty-title { font-size:16px; font-weight:700; color:#7c7c7c; margin-bottom:6px; }

/* Pagination */
.ld-pag { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-top:1px solid #f0eef2; }
.ld-pag-info { font-size:12px; color:#9e9e9e; }
.ld-pag-links { display:flex; gap:4px; }
.ld-pag-link { padding:5px 10px; border-radius:7px; font-size:12px; font-weight:600; text-decoration:none; color:#7c7c7c; border:1px solid #e1dee3; transition:all .15s; }
.ld-pag-link:hover, .ld-pag-link.active { background:#fe5f04; color:#fff; border-color:#fe5f04; }
.ld-pag-link.disabled { opacity:.4; pointer-events:none; }

/* Alert */
.ld-alert { padding:12px 16px; border-radius:10px; font-size:13px; display:flex; align-items:center; gap:10px; }
.ld-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.ld-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

/* Delete Modal */
.ld-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
.ld-modal { background:#fff; border-radius:16px; padding:28px; max-width:380px; width:90%; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:popIn .2s ease; }
@keyframes popIn { from { opacity:0; transform:scale(.94) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
.ld-modal-icon { width:52px; height:52px; border-radius:14px; background:#fef2f2; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.ld-modal-title { font-size:17px; font-weight:800; color:#121212; text-align:center; margin-bottom:8px; }
.ld-modal-sub { font-size:13px; color:#7c7c7c; text-align:center; line-height:1.6; margin-bottom:22px; }
.ld-modal-btns { display:flex; gap:10px; }
.ld-modal-btn { flex:1; padding:10px; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; border:none; transition:all .15s; }
.ld-modal-cancel { background:#f5f4f6; color:#7c7c7c; border:1px solid #e1dee3; }
.ld-modal-delete { background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; box-shadow:0 4px 12px rgba(220,38,38,.3); }

@media (max-width: 1200px) {
    .ld-stats-row { grid-template-columns:repeat(2,1fr); }
}

@media (max-width: 700px) {
    .ld-stats-row { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
<div class="ld-page">

    {{-- Topbar --}}
    <div class="ld-topbar">
        <div>
            <div class="ld-page-title">Leads</div>
            <div class="ld-breadcrumb">Sales â€º Leads</div>
        </div>
        <div class="ld-topbar-right">
            <a href="{{ route('leads.create') }}" class="ld-btn ld-btn-primary">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Lead
            </a>
        </div>
    </div>

    {{-- â•â• FILTER BAR â•â• --}}
    <div class="ld-filter-wrap-outer">
    <form method="GET" action="{{ route('leads.index') }}" id="filterForm">
        <div class="ld-filter-bar">

            {{-- Search --}}
            <div class="ld-search-box">
                <svg class="ld-search-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="ld-search-input" placeholder="Company, contact, emailâ€¦"
                       value="{{ request('search') }}" oninput="delaySubmit()">
            </div>

            <span class="ld-filter-label">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>

                <span class="ld-filter-count" id="fCount" style="display:none">0</span>
            </span>

            <div class="ld-filter-group">

                {{-- Branch --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    <select name="branch_id" class="ld-fs" id="f_branch" onchange="autoSubmit()">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected':'' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    <svg class="ld-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- Mobile --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.6"/></svg>
                    <input type="text" name="mobile_number" class="ld-fi-input" id="f_mobile"
                           placeholder="Mobile no." value="{{ request('mobile_number') }}" oninput="delaySubmit()" style="min-width:130px;">
                </div>

                {{-- Lead Source --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    <select name="lead_source" class="ld-fs" id="f_source" onchange="autoSubmit()">
                        <option value="">All Sources</option>
                        @foreach(\App\Models\Lead::sourceOptions() as $key => $label)
                        <option value="{{ $key }}" {{ request('lead_source') == $key ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <svg class="ld-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- Lead Status --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                    <select name="lead_status" class="ld-fs" id="f_status" onchange="autoSubmit()">
                        <option value="">All Status</option>
                        @foreach(\App\Models\Lead::statusOptions() as $key => $label)
                        <option value="{{ $key }}" {{ request('lead_status') == $key ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <svg class="ld-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- Priority --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 16l4-4 4 4 4-6 4 4"/></svg>
                    <select name="priority" class="ld-fs" id="f_priority" onchange="autoSubmit()">
                        <option value="">All Priority</option>
                        @foreach(\App\Models\Lead::PRIORITIES as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <svg class="ld-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- User (Assigned To) --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <select name="assigned_to" class="ld-fs" id="f_user" onchange="autoSubmit()">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <svg class="ld-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- Product --}}
                <div class="ld-fw">
                    <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    <select name="product_name" class="ld-fs" id="f_product" onchange="autoSubmit()" style="min-width:130px;">
                        <option value="">All Products</option>
                        @foreach($products as $p)
                        <option value="{{ $p }}" {{ request('product_name') == $p ? 'selected':'' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    <svg class="ld-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- Date Range --}}
                <div class="ld-date-range">
                    <div class="ld-fw">
                        <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="date_from" class="ld-fi-input" id="f_date_from"
                               value="{{ request('date_from') }}" onchange="updateFilters()" style="min-width:130px;">
                    </div>
                    <span class="ld-date-sep">â†’</span>
                    <div class="ld-fw">
                        <svg class="ld-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="date_to" class="ld-fi-input" id="f_date_to"
                               value="{{ request('date_to') }}" onchange="updateFilters()" style="min-width:130px;">
                    </div>
                </div>

                {{-- Quick date buttons --}}
                <div style="display:flex;gap:4px;">
                    <button type="button" class="ld-qb" onclick="setQ('today')">Today</button>
                    <button type="button" class="ld-qb" onclick="setQ('week')">Week</button>
                    <button type="button" class="ld-qb" onclick="setQ('month')">Month</button>
                </div>
            </div>

            {{-- Reset --}}
            @if(request()->hasAny(['search','branch_id','mobile_number','lead_source','lead_status','priority','assigned_to','product_name','date_from','date_to']))
            <a href="{{ route('leads.index') }}" class="ld-reset-btn">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.36"/></svg>
                Reset
            </a>
            @endif
        </div>

        {{-- Active filter chips --}}
        <div id="chipsBar" class="ld-chips-bar"></div>
    </form>
    </div>

    {{-- Body --}}
    <div class="ld-body">

        {{-- Flash --}}
        @if(session('success'))
        <div class="ld-alert ld-alert-success">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {!! session('success') !!}
        </div>
        @endif
        @if(session('error'))
        <div class="ld-alert ld-alert-error">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            {!! session('error') !!}
        </div>
        @endif

        {{-- Stats Row --}}
        <div class="ld-stats-row">

            @php
                $statItems = [
                    ['label'=>'Total Leads',         'value'=> $stats['total'],          'sub'=>'Matching current filters', 'color'=>'#fe5f04','bg'=>'#fff0e6','icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'],
                    ['label'=>'Total Product Count', 'value'=> $stats['total_products'], 'sub'=>'Products linked to leads', 'color'=>'#2563eb','bg'=>'#eff6ff','icon'=>'<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
                    ['label'=>'Pipeline Value',      'value'=> '₹'.number_format($stats['pipeline'], 2), 'sub'=>'Value across selected leads', 'color'=>'#7c3aed','bg'=>'#faf5ff','icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                    ['label'=>'Untouched Leads Count',     'value'=> $stats['new'],            'sub'=>'No call updates yet',      'color'=>'#16a34a','bg'=>'#f0fdf4','icon'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
                ];
            @endphp
            @foreach($statItems as $s)
            <div class="ld-stat" style="--stat-color: {{ $s['color'] }}">
                <div class="ld-stat-top">
                    <div class="ld-stat-icon" style="background:{{ $s['bg'] }}">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="{{ $s['color'] }}" stroke-width="2">{!! $s['icon'] !!}</svg>
                    </div>
                    <div class="ld-stat-trend">Summary</div>
                </div>
                <div class="ld-stat-label">{{ $s['label'] }}</div>
                <div class="ld-stat-value">{{ $s['value'] }}</div>
                <div class="ld-stat-sub">{{ $s['sub'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="ld-table-card">
            <div class="ld-table-top">
                <div>
                    <div class="ld-table-title">Lead Sheet</div>
                    <div class="ld-table-sub">Recent automation runs across workflows</div>
                </div>
                <div class="ld-results">
                    Showing <strong>{{ $leads->firstItem() ?? 0 }}â€“{{ $leads->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $leads->total() }}</strong> leads
                </div>
            </div>

            @if($leads->isEmpty())
            <div class="ld-empty">
                <div class="ld-empty-icon">ðŸ“‹</div>
                <div class="ld-empty-title">No leads found</div>
                <p style="font-size:13px">Try adjusting your filters or <a href="{{ route('leads.create') }}" style="color:#fe5f04;font-weight:700">add a new lead</a>.</p>
            </div>
            @else
            <div style="overflow-x:auto;">
                <div class="table-responsive">


                <table class="ld-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company / Contact</th>
                            <th>Mobile</th>
                            <th>Source</th>
                            <th>Priority</th>
                            <th>Deal Value</th>
                            <th>Assigned To</th>
                            <th>Branch</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $colors = ['#fe5f04','#7c3aed','#2563eb','#16a34a','#be123c','#0284c7','#b45309','#0f766e']; @endphp
                        @foreach($leads as $lead)
                        @php
                            $sc = $lead->status_color;
                            $pc = $lead->priority_color;
                            $priClass = ['low'=>'ld-pri-low','medium'=>'ld-pri-medium','high'=>'ld-pri-high'][$lead->priority] ?? 'ld-pri-medium';
                            $avatarColor = $colors[$lead->id % count($colors)];
                        @endphp
                        <tr onclick="window.location='{{ route('leads.show', $lead) }}'">
                            <td><span class="ld-id">LD-{{ str_pad($lead->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div class="ld-company-cell">
                                    <div class="ld-co-logo" style="background:{{ $avatarColor }}">
                                        {{ strtoupper(substr($lead->company_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="ld-co-name">{{ $lead->company_name }}</div>
                                        <div class="ld-co-contact">{{ $lead->contact_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ld-mobile">
                                    <a href="tel:{{ $lead->mobile_number }}" onclick="event.stopPropagation()">{{ $lead->mobile_number }}</a>
                                </div>
                                @if($lead->email)
                                <div class="ld-email-text">
                                    <a href="mailto:{{ $lead->email }}" onclick="event.stopPropagation()">{{ $lead->email }}</a>
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="ld-badge" style="background:#f5f4f6;color:#555;border-color:#e1dee3;">
                                    {{ $lead->source_label }}
                                </span>
                            </td>


                            <td>
                                <span class="ld-badge {{ $priClass }}">
                                    {{ $lead->priority_label }}
                                </span>
                            </td>
                            <td><span class="ld-deal">{{ number_format($lead->products->sum('total_price'), 2) }}</span></td>
                            <td>
                                @if($lead->assignedTo)
                                <div class="ld-owner">
                                    <div class="ld-owner-av" style="background:{{ $colors[$lead->assigned_to % count($colors)] }}">
                                        {{ strtoupper(substr($lead->assignedTo->name, 0, 1)) }}
                                    </div>
                                    <span style="font-size:12px">{{ $lead->assignedTo->name }}</span>
                                </div>
                                @else
                                <span style="color:#9e9e9e;font-size:12px">Unassigned</span>
                                @endif
                            </td>
                            <td><span class="ld-branch">{{ $lead->branch?->name ?? 'â€”' }}</span></td>
                            <td><span class="ld-date">{{ $lead->lead_date->format('d M Y') }}</span></td>
                            <td onclick="event.stopPropagation()">
                                <div class="ld-actions">
                                    @can('leads.view')
                                    <a href="{{ route('leads.show', $lead) }}" class="ld-act-btn" title="View">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    @endcan
                                    @can('leads.edit')
                                    <a href="{{ route('leads.edit', $lead) }}" class="ld-act-btn edit" title="Edit">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    @endcan
                                    @can('leads.delete')
                                    <button class="ld-act-btn del" title="Delete"
                                        onclick="confirmDelete({{ $lead->id }}, '{{ addslashes($lead->company_name) }}')">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($leads->hasPages())
                @include('partials.table-pagination', ['paginator' => $leads])
            @endif
            @if(false && $leads->hasPages())
            <div class="ld-pag">
                <div class="ld-pag-info">Page {{ $leads->currentPage() }} of {{ $leads->lastPage() }}</div>
                <div class="ld-pag-links">
                    <a href="{{ $leads->previousPageUrl() ?? '#' }}" class="ld-pag-link {{ !$leads->onFirstPage() ? '' : 'disabled' }}">â€¹</a>
                    @foreach($leads->getUrlRange(max(1,$leads->currentPage()-2), min($leads->lastPage(),$leads->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="ld-pag-link {{ $page == $leads->currentPage() ? 'active':'' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $leads->nextPageUrl() ?? '#' }}" class="ld-pag-link {{ $leads->hasMorePages() ? '':'disabled' }}">â€º</a>
                </div>
            </div>
            @endif
            @endif
        </div>

    </div>
</div>

{{-- Delete Modal --}}
<div class="ld-modal-overlay" id="deleteModal">
    <div class="ld-modal">
        <div class="ld-modal-icon">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <div class="ld-modal-title">Delete Lead?</div>
        <div class="ld-modal-sub" id="deleteSub">This action cannot be undone.</div>
        <div class="ld-modal-btns">
            <button class="ld-modal-btn ld-modal-cancel" onclick="closeModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="flex:1">
                @csrf @method('DELETE')
                <button type="submit" class="ld-modal-btn ld-modal-delete" style="width:100%">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter logic
const ff = {
    f_branch:    { label:'Branch',   sel:'#f_branch' },
    f_mobile:    { label:'Mobile',   sel:'#f_mobile' },
    f_source:    { label:'Source',   sel:'#f_source' },
    f_status:    { label:'Status',   sel:'#f_status' },
    f_priority:  { label:'Priority', sel:'#f_priority' },
    f_user:      { label:'User',     sel:'#f_user' },
    f_product:   { label:'Product',  sel:'#f_product' },
    f_date_from: { label:'From',     sel:'#f_date_from' },
    f_date_to:   { label:'To',       sel:'#f_date_to' },
};
function updateFilters() {
    let count = 0, chips = [];
    Object.entries(ff).forEach(([id, cfg]) => {
        const el = document.querySelector(cfg.sel);
        if (el && el.value) {
            count++;
            const display = el.tagName === 'SELECT' ? el.options[el.selectedIndex].text : el.value;
            chips.push(`<span class="ld-chip" onclick="clearF('${id}')">${cfg.label}: ${display} Ã—</span>`);
        }
    });
    const cnt = document.getElementById('fCount');
    cnt.textContent = count; cnt.style.display = count > 0 ? 'inline-flex' : 'none';
    const bar = document.getElementById('chipsBar');
    bar.style.display = count > 0 ? 'flex' : 'none';
    bar.innerHTML = chips.join('');
}
function clearF(id) { const el = document.querySelector(ff[id].sel); if(el) el.value=''; updateFilters(); document.getElementById('filterForm').submit(); }
function autoSubmit() { updateFilters(); document.getElementById('filterForm').submit(); }
let st;
function delaySubmit() { clearTimeout(st); updateFilters(); st = setTimeout(() => document.getElementById('filterForm').submit(), 600); }
function setQ(p) {
    const today = new Date(), fmt = d => d.toISOString().split('T')[0];
    const f = document.getElementById('f_date_from'), t = document.getElementById('f_date_to');
    if (p==='today') { f.value=fmt(today); t.value=fmt(today); }
    else if (p==='week') { const mon=new Date(today); mon.setDate(today.getDate()-today.getDay()+1); const sun=new Date(mon); sun.setDate(mon.getDate()+6); f.value=fmt(mon); t.value=fmt(sun); }
    else if (p==='month') { f.value=fmt(new Date(today.getFullYear(),today.getMonth(),1)); t.value=fmt(new Date(today.getFullYear(),today.getMonth()+1,0)); }
    updateFilters(); document.getElementById('filterForm').submit();
}
document.addEventListener('DOMContentLoaded', updateFilters);

// Delete modal
function confirmDelete(id, name) {
    document.getElementById('deleteSub').textContent = `Delete lead "${name}"? This cannot be undone.`;
    document.getElementById('deleteForm').action = `/leads/${id}`;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeModal() { document.getElementById('deleteModal').style.display = 'none'; }
document.getElementById('deleteModal').addEventListener('click', e => { if(e.target===document.getElementById('deleteModal')) closeModal(); });
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
</script>
@endpush
