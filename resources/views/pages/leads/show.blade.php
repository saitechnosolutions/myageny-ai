@extends('layouts.app')

@section('title', 'Lead — ' . $lead->company_name)

@include('pages.leads.show_page_style')

@section('content')
@php
    $colors  = ['#fe5f04','#7c3aed','#2563eb','#16a34a','#be123c','#0284c7','#b45309','#0f766e'];
    $heroColor = $colors[$lead->id % count($colors)];
    $sc = $lead->status_color;
    $pc = $lead->priority_color;
    $priClass = ['low'=>'','medium'=>'','high'=>''][$lead->priority] ?? '';

    $callCount     = $lead->callUpdates->count();
    $remCount      = $lead->reminders->where('is_completed', false)->count();
    $overdueRem    = $lead->reminders->where('is_completed', false)->filter(fn($r) => $r->remind_at->isPast())->count();
    $prodCount     = $lead->products->count();
    $qtCount       = $lead->quotations->count();

    $totalValue    = $lead->products->sum('total_price');
    $totalPaid     = $lead->products->sum('amount_paid');
    $totalPending  = $totalValue - $totalPaid;
@endphp

<div class="lsp">

    {{-- ── Topbar ── --}}
    <div class="lsp-topbar">
        <div>
            <div class="lsp-title">{{ $lead->company_name }}</div>
            <div class="lsp-crumb">
                <a href="{{ route('leads.index') }}">Leads</a> ›
                LD-{{ str_pad($lead->id,4,'0',STR_PAD_LEFT) }}
            </div>
        </div>
        <div class="lsp-topbar-right">
            <a href="{{ route('leads.edit', $lead) }}" class="lsp-btn lsp-btn-primary">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Lead
            </a>
            <a href="{{ route('leads.index') }}" class="lsp-btn lsp-btn-outline">← Back</a>
        </div>
    </div>

    {{-- ── Hero Strip ── --}}
    <div class="lsp-hero">
        <div class="lsp-hero-logo" style="background:{{ $heroColor }}">{{ strtoupper(substr($lead->company_name,0,2)) }}</div>
        <div>
            <div class="lsp-hero-name">{{ $lead->company_name }}</div>
            <div class="lsp-hero-sub">
                <span>{{ $lead->contact_name }}</span>
                <span style="color:#ddd">·</span>
                <span>{{ $lead->mobile_number }}</span>
                @if($lead->email)
                <span style="color:#ddd">·</span>
                <span>{{ $lead->email }}</span>
                @endif
                <span style="color:#ddd">·</span>
                <span>{{ $lead->lead_date->format('d M Y') }}</span>
            </div>
        </div>
        <div class="lsp-hero-right">
            <span class="lsp-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }}">
                <span class="lsp-dot" style="background:{{ $sc['text'] }}"></span>
                {{ $lead->status_label }}
            </span>
            <span class="lsp-badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};border-color:{{ $pc['border'] }}">
                {{ $lead->priority_label }} Priority
            </span>
            @if($lead->deal_value)
            <span style="font-size:15px;font-weight:800;color:var(--text);">{{ $lead->formatted_deal_value }}</span>
            @endif
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="lsp-tabs-wrap">
        <div class="lsp-tabs">
            <button class="lsp-tab active" onclick="switchTab('info', this)">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Lead Info
            </button>
            <button class="lsp-tab" onclick="switchTab('calls', this)">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 11.71 19a19.5 19.5 0 0 1-5.52-5.51A19.79 19.79 0 0 1 3.08 4.18 2 2 0 0 1 5.06 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L9.91 9.91a16 16 0 0 0 5.71 5.71l.63-.63a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Call Updates
                <span class="lsp-tab-count">{{ $callCount }}</span>
            </button>
            <button class="lsp-tab" onclick="switchTab('reminders', this)">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Reminders
                <span class="lsp-tab-count {{ $overdueRem ? 'urgent' : '' }}">{{ $remCount }}</span>
            </button>
            <button class="lsp-tab" onclick="switchTab('products', this)">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Products & Payments
                <span class="lsp-tab-count">{{ $prodCount }}</span>
            </button>
            <button class="lsp-tab" onclick="switchTab('quotations', this)">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Quotations
                <span class="lsp-tab-count">{{ $qtCount }}</span>
            </button>
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="lsp-body">

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
        <div style="padding:14px 28px 0;">
            @if(session('success'))
            <div class="lsp-alert lsp-alert-success">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {!! session('success') !!}
            </div>
            @endif
            @if(session('error'))
            <div class="lsp-alert lsp-alert-error">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                {!! session('error') !!}
            </div>
            @endif
        </div>
        @endif

        {{-- ════════════════════════════════════════
             TAB 1 — LEAD INFO
        ════════════════════════════════════════ --}}
        <div class="lsp-panel active" id="panel-info">
            <div class="lsp-grid-2" style="gap:18px;">

                {{-- Left --}}
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div class="lsp-card">
                        <div class="lsp-card-head"><div class="lsp-card-title">📋 Contact Information</div></div>
                        <div class="lsp-card-body">
                            <div class="lsp-info-grid">
                                <div class="lsp-info-item"><div class="lsp-il">Company</div><div class="lsp-iv">{{ $lead->company_name }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Contact Person</div><div class="lsp-iv">{{ $lead->contact_name }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Mobile</div><div class="lsp-iv"><a href="tel:{{ $lead->mobile_number }}">{{ $lead->mobile_number }}</a></div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Email</div><div class="lsp-iv">{!! $lead->email ? '<a href="mailto:'.$lead->email.'">'.$lead->email.'</a>' : '—' !!}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Lead Date</div><div class="lsp-iv">{{ $lead->lead_date->format('d M Y') }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Product</div><div class="lsp-iv">{{ $lead->product_name ?? '—' }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Source</div><div class="lsp-iv">{{ $lead->source_label }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Branch</div><div class="lsp-iv">{{ $lead->branch?->name ?? '—' }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Assigned To</div><div class="lsp-iv">{{ $lead->assignedTo?->name ?? 'Unassigned' }}</div></div>
                                <div class="lsp-info-item"><div class="lsp-il">Created By</div><div class="lsp-iv">{{ $lead->createdBy?->name ?? 'System' }}</div></div>
                            </div>
                        </div>
                    </div>

                    <div class="lsp-card">
                        <div class="lsp-card-head"><div class="lsp-card-title">💬 Remarks</div></div>
                        <div class="lsp-card-body">
                            <div class="lsp-remarks-box">{{ $lead->remarks ?? 'No remarks added.' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Right --}}
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div class="lsp-card">
                        <div class="lsp-card-head"><div class="lsp-card-title">💰 Deal & Status</div></div>
                        <div class="lsp-card-body">
                            <div class="lsp-info-grid" style="margin-bottom:14px;">
                                <div class="lsp-info-item"><div class="lsp-il">Deal Value</div><div class="lsp-deal-big">{{ $lead->formatted_deal_value }}</div></div>
                                <div class="lsp-info-item">
                                    <div class="lsp-il">Priority</div>
                                    <div><span class="lsp-badge" style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};border-color:{{ $pc['border'] }}">{{ $lead->priority_label }}</span></div>
                                </div>
                            </div>
                            <div style="margin-bottom:6px;font-size:11px;font-weight:700;color:#3d3d3d;text-transform:uppercase;letter-spacing:.4px;">Quick Status Change</div>
                            <form method="POST" action="{{ route('leads.update-status', $lead) }}">
                                @csrf @method('PATCH')
                                <div style="position:relative;">
                                    <select name="lead_status" class="lsp-status-select" onchange="this.form.submit()">
                                        @foreach(\App\Models\Lead::STATUSES as $k => $v)
                                        <option value="{{ $k }}" {{ $lead->lead_status===$k?'selected':'' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    <svg style="position:absolute;right:9px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9e9e9e;width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="lsp-card">
                        <div class="lsp-card-head"><div class="lsp-card-title">⚠️ Danger Zone</div></div>
                        <div class="lsp-card-body">
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}"
                                  onsubmit="return confirm('Permanently delete this lead?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="lsp-btn lsp-btn-danger" style="width:100%;justify-content:center;">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    Delete This Lead
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             TAB 2 — CALL UPDATES
        ════════════════════════════════════════ --}}
        <div class="lsp-panel" id="panel-calls">
            <div class="lsp-grid-2" style="gap:18px;align-items:start;">

                {{-- Left: Log list --}}
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <h3 style="font-size:15px;font-weight:700;color:var(--text);">📞 Call History</h3>
                        <span style="font-size:12px;color:var(--muted);">{{ $callCount }} records</span>
                    </div>

                    @if($lead->callUpdates->isEmpty())
                    <div class="lsp-empty">
                        <div class="lsp-empty-ico">📞</div>
                        <div class="lsp-empty-title">No calls logged yet</div>
                        <div class="lsp-empty-sub">Log your first call using the form →</div>
                    </div>
                    @else
                    <div class="lsp-call-list">
                        @foreach($lead->callUpdates->sortByDesc('called_at') as $call)
                        @php $oc = $call->outcome_color; @endphp
                        <div class="lsp-call-card type-{{ $call->call_type }}">
                            <div class="lsp-call-top">
                                <div class="lsp-call-meta">
                                    <span class="lsp-call-type ct-{{ $call->call_type }}">
                                        {{ $call->call_type_label }}
                                    </span>
                                    <span class="lsp-call-when">{{ $call->called_at->format('d M Y, h:i A') }}</span>
                                    @if($call->duration_minutes)
                                    <span class="lsp-call-dur">⏱ {{ $call->duration_minutes }} min</span>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('leads.calls.destroy', [$lead, $call]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="lsp-call-del" title="Remove"
                                            onclick="return confirm('Remove this call record?')">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </button>
                                </form>
                            </div>
                            @if($call->notes)
                            <div class="lsp-call-notes">{{ $call->notes }}</div>
                            @endif
                            <div class="lsp-call-footer">
                                <span class="lsp-call-outcome" style="background:{{ $oc['bg'] }};color:{{ $oc['text'] }}">
                                    {{ $call->outcome_label }}
                                </span>
                                @if($call->next_follow_up)
                                <span class="lsp-call-followup">
                                    📅 Follow-up: {{ $call->next_follow_up->format('d M Y') }}
                                </span>
                                @endif
                                <span class="lsp-call-by">By {{ $call->user?->name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Right: Add call form --}}
                <div>
                    <div class="lsp-card">
                        <div class="lsp-card-head">
                            <div class="lsp-card-title">➕ Log New Call</div>
                        </div>
                        <div class="lsp-card-body">
                            <form method="POST" action="{{ route('leads.calls.store', $lead) }}">
                                @csrf
                                <div style="display:flex;flex-direction:column;gap:12px;">

                                    <div class="lsp-form-row lsp-form-row-2">
                                        <div class="lsp-group">
                                            <label class="lsp-label">Call Date & Time <span class="lsp-req">*</span></label>
                                            <div class="lsp-fw">
                                                <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <input type="datetime-local" name="called_at" class="lsp-inp" value="{{ now()->format('Y-m-d\TH:i') }}" required readonly>
                                            </div>
                                        </div>
                                        <div class="lsp-group">
                                            <label class="lsp-label">Call Type <span class="lsp-req">*</span></label>
                                            <div class="lsp-fw">
                                                <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 11.71 19"/></svg>
                                                <select name="call_type" class="lsp-sel" required>
                                                    @foreach(\App\Models\LeadCallUpdate::CALL_TYPES as $k => $v)
                                                    <option value="{{ $k }}">{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                                <svg class="lsp-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lsp-form-row lsp-form-row-2">
                                        <div class="lsp-group">
                                            <label class="lsp-label">Duration (minutes)</label>
                                            <div class="lsp-fw">
                                                <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <input type="number" name="duration_minutes" class="lsp-inp" placeholder="0" min="0">
                                            </div>
                                        </div>
                                        <div class="lsp-group">
                                            <label class="lsp-label">Outcome <span class="lsp-req">*</span></label>
                                            <div class="lsp-fw">
                                                <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                <select name="outcome" class="lsp-sel" required>
                                                    <option value="">— Select outcome —</option>
                                                    @foreach(\App\Models\LeadCallUpdate::OUTCOMES as $k => $v)
                                                    <option value="{{ $k }}">{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                                <svg class="lsp-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lsp-group">
                                        <label class="lsp-label">Call Notes</label>
                                        <textarea name="notes" class="lsp-ta" placeholder="What was discussed? Any key points…" rows="3"></textarea>
                                    </div>

                                    <div class="lsp-group">
                                        <label class="lsp-label">Next Follow-up Date</label>
                                        <div class="lsp-fw">
                                            <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            <input type="date" name="next_follow_up" class="lsp-inp" min="{{ today()->toDateString() }}">
                                        </div>
                                    </div>

                                    <button type="submit" class="lsp-btn lsp-btn-primary" style="justify-content:center;">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                        Save Call Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             TAB 3 — REMINDERS
        ════════════════════════════════════════ --}}
        <div class="lsp-panel" id="panel-reminders">
            <div class="lsp-grid-2" style="gap:18px;align-items:start;">

                {{-- Left: Reminder list --}}
                <div>
                    @php
                        $pending   = $lead->reminders->where('is_completed', false)->sortBy('remind_at');
                        $completed = $lead->reminders->where('is_completed', true)->sortByDesc('completed_at');
                    @endphp

                    @if($pending->isNotEmpty())
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:10px;color:var(--text);">🔔 Pending ({{ $pending->count() }})</h3>
                    <div class="lsp-rem-list" style="margin-bottom:18px;">
                        @foreach($pending as $rem)
                        @php $isOverdue = $rem->is_overdue; @endphp
                        <div class="lsp-rem-card {{ $isOverdue ? 'overdue' : '' }}">
                            <div class="lsp-rem-ico" style="background:{{ $isOverdue ? '#fef2f2' : '#f5f4f6' }}">{{ $rem->type_icon }}</div>
                            <div class="lsp-rem-body">
                                <div class="lsp-rem-title">
                                    {{ $rem->title }}
                                    @if($isOverdue)
                                    <span class="lsp-badge" style="background:#fef2f2;color:#dc2626;border-color:#fecaca;font-size:10px;">Overdue</span>
                                    @endif
                                </div>
                                @if($rem->description)
                                <div class="lsp-rem-desc">{{ $rem->description }}</div>
                                @endif
                                <div class="lsp-rem-meta">
                                    <span class="lsp-rem-time {{ $isOverdue ? 'overdue' : '' }}">
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ $rem->remind_at->format('d M Y, h:i A') }}
                                    </span>
                                    <span class="lsp-badge" style="background:#f5f4f6;color:#555;border-color:#e1dee3;font-size:10px;">{{ $rem->type_label }}</span>
                                    <span class="lsp-badge" style="font-size:10px;background:{{ ['low'=>'#f0fdf4','medium'=>'#fffbeb','high'=>'#fef2f2'][$rem->priority] ?? '#f5f4f6' }};color:{{ ['low'=>'#16a34a','medium'=>'#b45309','high'=>'#dc2626'][$rem->priority] ?? '#7c7c7c' }};border:none;">{{ ucfirst($rem->priority) }}</span>
                                </div>
                            </div>
                            <div class="lsp-rem-actions">
                                <form method="POST" action="{{ route('leads.reminders.complete', [$lead, $rem]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="lsp-rem-done-btn">✓ Done</button>
                                </form>
                                <form method="POST" action="{{ route('leads.reminders.destroy', [$lead, $rem]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="lsp-rem-del-btn" onclick="return confirm('Remove reminder?')">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($completed->isNotEmpty())
                    <h3 style="font-size:13px;font-weight:700;margin-bottom:8px;color:var(--muted);">✅ Completed ({{ $completed->count() }})</h3>
                    <div class="lsp-rem-list">
                        @foreach($completed->take(5) as $rem)
                        <div class="lsp-rem-card done">
                            <div class="lsp-rem-ico" style="background:#f0fdf4">✅</div>
                            <div class="lsp-rem-body">
                                <div class="lsp-rem-title done">{{ $rem->title }}</div>
                                <div class="lsp-rem-meta">
                                    <span style="font-size:11px;color:var(--muted);">Completed {{ $rem->completed_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($lead->reminders->isEmpty())
                    <div class="lsp-empty">
                        <div class="lsp-empty-ico">🔔</div>
                        <div class="lsp-empty-title">No reminders set</div>
                        <div class="lsp-empty-sub">Set a reminder using the form →</div>
                    </div>
                    @endif
                </div>

                {{-- Right: Add reminder form --}}
                <div>
                    <div class="lsp-card">
                        <div class="lsp-card-head">
                            <div class="lsp-card-title">➕ Set Reminder</div>
                        </div>
                        <div class="lsp-card-body">
                            <form method="POST" action="{{ route('leads.reminders.store', $lead) }}">
                                @csrf
                                <div style="display:flex;flex-direction:column;gap:12px;">
                                    <div class="lsp-group">
                                        <label class="lsp-label">Title <span class="lsp-req">*</span></label>
                                        <div class="lsp-fw">
                                            <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
                                            <input type="text" name="title" class="lsp-inp" placeholder="e.g. Follow-up call with Raj Kumar" required>
                                        </div>
                                    </div>

                                    <div class="lsp-form-row lsp-form-row-2">
                                        <div class="lsp-group">
                                            <label class="lsp-label">Remind At <span class="lsp-req">*</span></label>
                                            <div class="lsp-fw">
                                                <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <input type="datetime-local" name="remind_at" class="lsp-inp" required>
                                            </div>
                                        </div>
                                        <div class="lsp-group">
                                            <label class="lsp-label">Type <span class="lsp-req">*</span></label>
                                            <div class="lsp-fw">
                                                <svg class="lsp-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                                <select name="type" class="lsp-sel" required>
                                                    @foreach(\App\Models\LeadReminder::TYPES as $k => $v)
                                                    <option value="{{ $k }}">{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                                <svg class="lsp-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lsp-group">
                                        <label class="lsp-label">Priority <span class="lsp-req">*</span></label>
                                        <div style="display:flex;gap:8px;">
                                            @foreach(['low'=>['🟢','Low','#16a34a','#f0fdf4'],'medium'=>['🟡','Medium','#b45309','#fffbeb'],'high'=>['🔴','High','#dc2626','#fef2f2']] as $pk => $pv)
                                            <label style="flex:1;display:flex;align-items:center;gap:5px;padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;transition:all .15s;" class="rem-pri-opt" data-val="{{ $pk }}">
                                                <input type="radio" name="priority" value="{{ $pk }}" {{ $pk==='medium'?'checked':'' }} style="display:none">
                                                <span>{{ $pv[0] }}</span>
                                                <span style="font-size:12px;font-weight:700;color:#555">{{ $pv[1] }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="lsp-group">
                                        <label class="lsp-label">Description</label>
                                        <textarea name="description" class="lsp-ta" placeholder="Optional details…" rows="2"></textarea>
                                    </div>

                                    <button type="submit" class="lsp-btn lsp-btn-primary" style="justify-content:center;">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                        Set Reminder
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             TAB 4 — PRODUCTS & PAYMENTS
        ════════════════════════════════════════ --}}
       <div class="lsp-panel" id="panel-products">
         @include('pages.leads.products_panel')
        </div>

        {{-- ════════════════════════════════════════
             TAB 5 — QUOTATIONS
        ════════════════════════════════════════ --}}
        <div class="lsp-panel" id="panel-quotations">

            {{-- Existing quotations --}}
            @if($lead->quotations->isNotEmpty())
            <div class="lsp-qt-list">
                @foreach($lead->quotations as $qt)
                @php $qsc = $qt->status_color; @endphp
                <div class="lsp-qt-card">
                    <div class="lsp-qt-head">
                        <div>
                            <div class="lsp-qt-num">{{ $qt->quotation_number }}</div>
                            <div class="lsp-qt-date">
                                {{ $qt->quotation_date->format('d M Y') }}
                                @if($qt->valid_until) · Valid until {{ $qt->valid_until->format('d M Y') }} @endif
                            </div>
                        </div>
                        <div class="lsp-qt-actions">
                            <span class="lsp-badge" style="background:{{ $qsc['bg'] }};color:{{ $qsc['text'] }};border-color:{{ $qsc['border'] }}">
                                {{ $qt->status_label }}
                            </span>
                            <span style="font-size:15px;font-weight:800;color:var(--text);">{{ $qt->formatted_grand_total }}</span>
                            {{-- Quick status change --}}
                            <form method="POST" action="{{ route('leads.quotations.update-status', [$lead, $qt]) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <div style="position:relative;display:inline-block;">
                                    <select name="status" class="lsp-status-select" style="font-size:12px;padding:5px 24px 5px 9px;min-width:100px;" onchange="this.form.submit()">
                                        @foreach(\App\Models\Quotation::STATUSES as $k => $v)
                                        <option value="{{ $k }}" {{ $qt->status===$k?'selected':'' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    <svg style="position:absolute;right:7px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9e9e9e;width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('leads.quotations.destroy', [$lead, $qt]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="lsp-call-del" onclick="return confirm('Delete quotation?')">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="lsp-qt-body">
                        <table class="lsp-qt-items-tbl">
                            <thead>
                                <tr><th>#</th><th>Product</th><th>Qty</th><th>Unit</th><th style="text-align:right">Price</th><th style="text-align:right">Disc%</th><th style="text-align:right">Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach($qt->items as $i => $item)
                                <tr>
                                    <td style="color:var(--muted);font-size:12px;">{{ $i+1 }}</td>
                                    <td><div style="font-weight:600;">{{ $item->product_name }}</div>@if($item->description)<div style="font-size:11px;color:var(--muted);">{{ $item->description }}</div>@endif</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td style="text-align:right;">₹{{ number_format($item->unit_price,2) }}</td>
                                    <td style="text-align:right;">{{ $item->discount_percent }}%</td>
                                    <td style="text-align:right;font-weight:700;">₹{{ number_format($item->total,2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="lsp-qt-totals">
                            <div class="lsp-qt-total-row"><span class="lsp-qt-total-label">Subtotal</span><span class="lsp-qt-total-value">₹{{ number_format($qt->subtotal,2) }}</span></div>
                            @if($qt->discount_amount > 0)
                            <div class="lsp-qt-total-row"><span class="lsp-qt-total-label">Discount</span><span class="lsp-qt-total-value" style="color:var(--green)">-₹{{ number_format($qt->discount_amount,2) }}</span></div>
                            @endif
                            @if($qt->tax_percent > 0)
                            <div class="lsp-qt-total-row"><span class="lsp-qt-total-label">Tax ({{ $qt->tax_percent }}%)</span><span class="lsp-qt-total-value">₹{{ number_format($qt->tax_amount,2) }}</span></div>
                            @endif
                            <div class="lsp-qt-total-row grand"><span>Grand Total</span><span>{{ $qt->formatted_grand_total }}</span></div>
                        </div>
                        @if($qt->notes)<div style="margin-top:10px;font-size:12px;color:var(--muted);background:var(--soft);padding:9px;border-radius:8px;">{{ $qt->notes }}</div>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Create new quotation form --}}
            <div class="lsp-qt-form-wrap">
                <div class="lsp-qt-form-head" onclick="toggleQtForm()">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create New Quotation
                    <svg id="qtArrow" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-left:auto;transition:transform .2s"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="lsp-qt-form-body {{ $lead->quotations->isEmpty() ? 'open' : '' }}" id="qtFormBody">
                    <form method="POST" action="{{ route('leads.quotations.store', $lead) }}" id="qtForm">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:14px;">

                            <div class="lsp-form-row lsp-form-row-3">
                                <div class="lsp-group">
                                    <label class="lsp-label">Quotation Date <span class="lsp-req">*</span></label>
                                    <input type="date" name="quotation_date" class="lsp-inp no-ico" value="{{ today()->toDateString() }}" required>
                                </div>
                                <div class="lsp-group">
                                    <label class="lsp-label">Valid Until</label>
                                    <input type="date" name="valid_until" class="lsp-inp no-ico" value="{{ today()->addDays(30)->toDateString() }}">
                                </div>
                                <div class="lsp-group">
                                    <label class="lsp-label">Tax %</label>
                                    <input type="number" name="tax_percent" class="lsp-inp no-ico" placeholder="0" min="0" max="100" value="18" oninput="recalcQt()">
                                </div>
                            </div>

                            {{-- Line items --}}
                            <div>
                                <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;">Line Items</div>
                                <div style="overflow-x:auto;">
                                <table class="lsp-items-table" id="qtItemsTable">
                                    <thead>
                                        <tr>
                                            <th style="min-width:160px;">Product Name *</th>
                                            <th style="min-width:140px;">Description</th>
                                            <th style="min-width:60px;">Qty *</th>
                                            <th style="min-width:60px;">Unit</th>
                                            <th style="min-width:100px;">Unit Price *</th>
                                            <th style="min-width:70px;">Disc %</th>
                                            <th style="min-width:90px;text-align:right;">Total</th>
                                            <th style="width:30px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="qtItemsBody">
                                        <tr class="qt-item-row">
                                            <td><input type="text" name="items[0][product_name]" class="lsp-item-inp" placeholder="Product name" required></td>
                                            <td><input type="text" name="items[0][description]" class="lsp-item-inp" placeholder="Optional"></td>
                                            <td><input type="number" name="items[0][quantity]" class="lsp-item-inp" value="1" min="1" onchange="recalcQt()" style="width:55px;"></td>
                                            <td><input type="text" name="items[0][unit]" class="lsp-item-inp" value="Nos" style="width:55px;"></td>
                                            <td><input type="number" name="items[0][unit_price]" class="lsp-item-inp" placeholder="0.00" step="0.01" min="0" onchange="recalcQt()"></td>
                                            <td><input type="number" name="items[0][discount_percent]" class="lsp-item-inp" value="0" min="0" max="100" onchange="recalcQt()" style="width:65px;"></td>
                                            <td class="lsp-item-total">₹0.00</td>
                                            <td><button type="button" class="lsp-remove-row" onclick="removeQtRow(this)" style="display:none">✕</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                                <button type="button" class="lsp-add-row-btn" onclick="addQtRow()">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Add Line Item
                                </button>

                                {{-- Totals preview --}}
                                <div class="lsp-qt-totals-preview">
                                    <div class="lsp-qt-pr"><span>Subtotal</span><span id="qtSubtotal">₹0.00</span></div>
                                    <div class="lsp-qt-pr"><span>Discount</span><span>-<input type="number" name="discount_amount" id="qtDiscInput" value="0" min="0" style="width:70px;border:1px solid var(--border);border-radius:5px;padding:3px 5px;font-size:12px;font-family:inherit;" onchange="recalcQt()"></span></div>
                                    <div class="lsp-qt-pr"><span>Tax</span><span id="qtTaxAmt">₹0.00</span></div>
                                    <div class="lsp-qt-pr grand"><span>Grand Total</span><span id="qtGrandTotal">₹0.00</span></div>
                                </div>
                            </div>

                            <div class="lsp-form-row lsp-form-row-2">
                                <div class="lsp-group">
                                    <label class="lsp-label">Terms & Conditions</label>
                                    <textarea name="terms_conditions" class="lsp-ta" rows="3" placeholder="Payment terms, delivery, etc."></textarea>
                                </div>
                                <div class="lsp-group">
                                    <label class="lsp-label">Internal Notes</label>
                                    <textarea name="notes" class="lsp-ta" rows="3" placeholder="Notes visible to team only…"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="lsp-btn lsp-btn-primary" style="justify-content:center;width:100%;">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Create Quotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- /lsp-body --}}
</div>{{-- /lsp --}}
@endsection

@push('scripts')
<script>
// ── Tab switching ─────────────────────────────────────────────────
function switchTab(name, btn) {
    document.querySelectorAll('.lsp-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.lsp-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-'+name).classList.add('active');
}

// ── Toggle quotation form ─────────────────────────────────────────
function toggleQtForm() {
    const body  = document.getElementById('qtFormBody');
    const arrow = document.getElementById('qtArrow');
    const open  = body.classList.toggle('open');
    arrow.style.transform = open ? 'rotate(180deg)' : '';
}

// ── Quotation line items ──────────────────────────────────────────
let qtRowIndex = 1;

function addQtRow() {
    const body = document.getElementById('qtItemsBody');
    const idx  = qtRowIndex++;
    const tr   = document.createElement('tr');
    tr.className = 'qt-item-row';
    tr.innerHTML = `
        <td><input type="text" name="items[${idx}][product_name]" class="lsp-item-inp" placeholder="Product name" required></td>
        <td><input type="text" name="items[${idx}][description]" class="lsp-item-inp" placeholder="Optional"></td>
        <td><input type="number" name="items[${idx}][quantity]" class="lsp-item-inp" value="1" min="1" onchange="recalcQt()" style="width:55px;"></td>
        <td><input type="text" name="items[${idx}][unit]" class="lsp-item-inp" value="Nos" style="width:55px;"></td>
        <td><input type="number" name="items[${idx}][unit_price]" class="lsp-item-inp" placeholder="0.00" step="0.01" min="0" onchange="recalcQt()"></td>
        <td><input type="number" name="items[${idx}][discount_percent]" class="lsp-item-inp" value="0" min="0" max="100" onchange="recalcQt()" style="width:65px;"></td>
        <td class="lsp-item-total">₹0.00</td>
        <td><button type="button" class="lsp-remove-row" onclick="removeQtRow(this)">✕</button></td>
    `;
    body.appendChild(tr);
    updateRemoveBtns();
}

function removeQtRow(btn) {
    btn.closest('tr').remove();
    updateRemoveBtns();
    recalcQt();
}

function updateRemoveBtns() {
    const rows = document.querySelectorAll('.qt-item-row');
    rows.forEach((r, i) => {
        const btn = r.querySelector('.lsp-remove-row');
        if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
    });
}

function recalcQt() {
    let subtotal = 0;
    document.querySelectorAll('.qt-item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('[name*="[quantity]"]')?.value) || 0;
        const price = parseFloat(row.querySelector('[name*="[unit_price]"]')?.value) || 0;
        const disc  = parseFloat(row.querySelector('[name*="[discount_percent]"]')?.value) || 0;
        const gross = qty * price;
        const total = gross - (gross * disc / 100);
        const totEl = row.querySelector('.lsp-item-total');
        if (totEl) totEl.textContent = '₹' + total.toFixed(2);
        subtotal += total;
    });

    const discAmt  = parseFloat(document.getElementById('qtDiscInput')?.value) || 0;
    const taxPct   = parseFloat(document.querySelector('[name="tax_percent"]')?.value) || 0;
    const taxable  = subtotal - discAmt;
    const taxAmt   = taxable * (taxPct / 100);
    const grand    = taxable + taxAmt;

    const fmt = v => '₹' + Math.max(0,v).toFixed(2);
    document.getElementById('qtSubtotal').textContent  = fmt(subtotal);
    document.getElementById('qtTaxAmt').textContent    = fmt(taxAmt);
    document.getElementById('qtGrandTotal').textContent= fmt(grand);
}

// ── Product total live calc ────────────────────────────────────────
function calcTotal() {
    const price = parseFloat(document.querySelector('[name="unit_price"]')?.value) || 0;
    const qty   = parseFloat(document.querySelector('[name="quantity"]')?.value) || 1;
    const disc  = parseFloat(document.querySelector('[name="discount_percent"]')?.value) || 0;
    const gross = price * qty;
    const total = gross - (gross * disc / 100);
    const el    = document.getElementById('prodTotal');
    if (el) el.textContent = '₹' + total.toFixed(2);
}

// ── Reminder priority selector ─────────────────────────────────────
document.querySelectorAll('.rem-pri-opt').forEach(el => {
    el.addEventListener('click', () => {
        const val = el.dataset.val;
        const colors = { low:['#16a34a','#f0fdf4','#bbf7d0'], medium:['#b45309','#fffbeb','#fde68a'], high:['#dc2626','#fef2f2','#fecaca'] };
        document.querySelectorAll('.rem-pri-opt').forEach(o => {
            o.style.borderColor = 'var(--border)';
            o.style.background  = '';
        });
        const c = colors[val];
        if (c) { el.style.borderColor = c[2]; el.style.background = c[1]; }
        el.querySelector('input').checked = true;
    });
});

// Init medium selected
const medOpt = document.querySelector('.rem-pri-opt[data-val="medium"]');
if (medOpt) { medOpt.style.borderColor = '#fde68a'; medOpt.style.background = '#fffbeb'; }
</script>


@endpush
