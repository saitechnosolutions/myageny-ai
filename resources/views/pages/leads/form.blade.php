{{-- ================================================================
     FILE: resources/views/leads/_form.blade.php
     Shared form body — included by create.blade.php & edit.blade.php
     $lead is null on create, Lead model on edit
================================================================ --}}

@php
    $isEdit = !is_null($lead);
    $old = fn($field, $default = '') => old($field, $isEdit ? $lead->{$field} : $default);

    $statusColors = [
        'new'         => ['dot' => '#2563eb', 'cls' => 'sel-new',  'label' => 'New'],
        'qualified'   => ['dot' => '#0f766e', 'cls' => 'sel-qual', 'label' => 'Qualified'],
        'proposal'    => ['dot' => '#7c3aed', 'cls' => 'sel-prop', 'label' => 'Proposal'],
        'negotiation' => ['dot' => '#b45309', 'cls' => 'sel-nego', 'label' => 'Negotiation'],
        'won'         => ['dot' => '#16a34a', 'cls' => 'sel-won',  'label' => 'Won'],
        'lost'        => ['dot' => '#dc2626', 'cls' => 'sel-lost', 'label' => 'Lost'],
    ];
    $currentStatus = $old('lead_status', 'new');
    $currentPriority = $old('priority', 'medium');
@endphp

<div class="lf-grid">

    {{-- ── LEFT COLUMN ── --}}
    <div class="lf-left">

        {{-- Company & Contact --}}
        <div class="lf-card">
            <div class="lf-card-head">
                <div class="lf-card-ico" style="background:#fff0e6">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#fe5f04" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                    <div class="lf-card-title">Company & Contact</div>
                    <div class="lf-card-sub">Basic lead information</div>
                </div>
            </div>
            <div class="lf-card-body">
                <div class="lf-row">
                    <div class="lf-group">
                        <label class="lf-label">Company Name <span class="lf-req">*</span></label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            <input type="text" name="company_name" class="lf-inp {{ $errors->has('company_name') ? 'err' : '' }}"
                                   placeholder="Acme Corporation" value="{{ $old('company_name') }}" required>
                        </div>
                        @error('company_name')<div class="lf-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lf-group">
                        <label class="lf-label">Contact Name <span class="lf-req">*</span></label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input type="text" name="contact_name" class="lf-inp {{ $errors->has('contact_name') ? 'err' : '' }}"
                                   placeholder="Raj Kumar" value="{{ $old('contact_name') }}" required>
                        </div>
                        @error('contact_name')<div class="lf-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="lf-row">
                    <div class="lf-group">
                        <label class="lf-label">Mobile Number <span class="lf-req">*</span></label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                            <input type="text" name="mobile_number" class="lf-inp {{ $errors->has('mobile_number') ? 'err' : '' }}"
                                   placeholder="+91 98765 43210" value="{{ $old('mobile_number') }}" required>
                        </div>
                        @error('mobile_number')<div class="lf-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lf-group">
                        <label class="lf-label">Email Address</label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" name="email" class="lf-inp {{ $errors->has('email') ? 'err' : '' }}"
                                   placeholder="raj@company.com" value="{{ $old('email') }}">
                        </div>
                        @error('email')<div class="lf-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="lf-row">
                    <div class="lf-group">
                        <label class="lf-label">Lead Date <span class="lf-req">*</span></label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <input type="date" name="lead_date" class="lf-inp {{ $errors->has('lead_date') ? 'err' : '' }}"
                                   value="{{ $old('lead_date', now()->toDateString()) }}" required>
                        </div>
                        @error('lead_date')<div class="lf-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lf-group">
                        <label class="lf-label">Product Name</label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            <input type="text" name="product_name" class="lf-inp"
                                   placeholder="e.g. CRM Pro, Payroll Module" value="{{ $old('product_name') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deal & Assignment --}}
        <div class="lf-card">
            <div class="lf-card-head">
                <div class="lf-card-ico" style="background:#eff6ff">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div>
                    <div class="lf-card-title">Deal & Assignment</div>
                    <div class="lf-card-sub">Value, source, and team assignment</div>
                </div>
            </div>
            <div class="lf-card-body">
                <div class="lf-row">
                    <div class="lf-group">
                        <label class="lf-label">Lead Source <span class="lf-req">*</span></label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            <select name="lead_source" class="lf-sel {{ $errors->has('lead_source') ? 'err' : '' }}" required>
                                <option value="">— Select Source —</option>
                                @foreach(\App\Models\Lead::SOURCES as $key => $label)
                                <option value="{{ $key }}" {{ $old('lead_source') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <svg class="lf-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        @error('lead_source')<div class="lf-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="lf-group">
                        <label class="lf-label">Deal Value</label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            <input type="number" name="deal_value" class="lf-inp" step="0.01" min="0"
                                   placeholder="0.00" value="{{ $old('deal_value') }}">
                        </div>
                        <div class="lf-hint">Enter amount in ₹ (INR)</div>
                    </div>
                </div>

                <div class="lf-row">
                    <div class="lf-group">
                        <label class="lf-label">Assigned To</label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <select name="assigned_to" class="lf-sel">
                                <option value="">— Unassigned —</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            <svg class="lf-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                    <div class="lf-group">
                        <label class="lf-label">Branch</label>
                        <div class="lf-iw">
                            <svg class="lf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            <select name="branch_id" class="lf-sel">
                                <option value="">— No Branch —</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                            <svg class="lf-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Remarks --}}
        <div class="lf-card">
            <div class="lf-card-head">
                <div class="lf-card-ico" style="background:#f5f4f6">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#7c7c7c" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div>
                    <div class="lf-card-title">Remarks</div>
                    <div class="lf-card-sub">Additional notes about this lead</div>
                </div>
            </div>
            <div class="lf-card-body">
                <div class="lf-group">
                    <label class="lf-label">Remarks / Notes</label>
                    <textarea name="remarks" class="lf-ta" placeholder="Enter any additional details, context, or follow-up notes…" rows="4">{{ $old('remarks') }}</textarea>
                    @error('remarks')<div class="lf-err">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="lf-submit">
                <button type="submit" class="lf-btn lf-btn-primary" style="flex:1">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $isEdit ? 'Save Changes' : 'Create Lead' }}
                </button>
                <a href="{{ route('leads.index') }}" class="lf-btn lf-btn-outline">Cancel</a>
            </div>
        </div>

    </div>{{-- /lf-left --}}

    {{-- ── RIGHT COLUMN ── --}}
    <div class="lf-right">

        {{-- Lead Status Picker --}}
        <div class="lf-card">
            <div class="lf-card-head">
                <div class="lf-card-ico" style="background:#f0fdf4">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                </div>
                <div>
                    <div class="lf-card-title">Lead Status <span style="color:#dc2626">*</span></div>
                    <div class="lf-card-sub">Current stage in the pipeline</div>
                </div>
            </div>
            <div class="lf-card-body">
                @error('lead_status')<div class="lf-err" style="margin-bottom:6px">{{ $message }}</div>@enderror
                <div class="lf-status-grid">
                    @foreach($statusColors as $key => $cfg)
                    <label class="lf-status-opt {{ $currentStatus === $key ? $cfg['cls'] : '' }}" data-val="{{ $key }}">
                        <input type="radio" name="lead_status" value="{{ $key }}" {{ $currentStatus === $key ? 'checked' : '' }}>
                        <div class="lf-status-dot" style="background:{{ $cfg['dot'] }}"></div>
                        <span class="lf-status-name" style="color:{{ $currentStatus === $key ? $cfg['dot'] : '#2e2e2e' }}">{{ $cfg['label'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Priority Picker --}}
        <div class="lf-card">
            <div class="lf-card-head">
                <div class="lf-card-ico" style="background:#fffbeb">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#b45309" stroke-width="2"><path d="M3 16l4-4 4 4 4-6 4 4"/></svg>
                </div>
                <div>
                    <div class="lf-card-title">Priority <span style="color:#dc2626">*</span></div>
                    <div class="lf-card-sub">Lead urgency level</div>
                </div>
            </div>
            <div class="lf-card-body">
                @error('priority')<div class="lf-err" style="margin-bottom:6px">{{ $message }}</div>@enderror
                <div class="lf-priority-row">
                    @php
                        $priorities = [
                            'low'    => ['ico'=>'🟢','label'=>'Low',    'color'=>'#16a34a'],
                            'medium' => ['ico'=>'🟡','label'=>'Medium', 'color'=>'#b45309'],
                            'high'   => ['ico'=>'🔴','label'=>'High',   'color'=>'#dc2626'],
                        ];
                    @endphp
                    @foreach($priorities as $key => $p)
                    <label class="lf-pri-opt {{ $currentPriority === $key ? 'selected-'.$key : '' }}" data-val="{{ $key }}">
                        <input type="radio" name="priority" value="{{ $key }}" {{ $currentPriority === $key ? 'checked' : '' }}>
                        <div class="lf-pri-ico" style="background:{{ $currentPriority === $key ? ($key==='low'?'#f0fdf4':($key==='medium'?'#fffbeb':'#fef2f2')) : '#f5f4f6' }}">{{ $p['ico'] }}</div>
                        <div class="lf-pri-label" style="color:{{ $currentPriority === $key ? $p['color'] : '#7c7c7c' }}">{{ $p['label'] }}</div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary card (edit mode) --}}
        @if($isEdit)
        <div class="lf-card" style="background:linear-gradient(135deg,#0f172a,#1e293b);border-color:#334155;">
            <div class="lf-card-head" style="border-bottom-color:#334155">
                <div>
                    <div class="lf-card-title" style="color:#f1f5f9">Lead Summary</div>
                    <div class="lf-card-sub" style="color:#94a3b8">Created {{ $lead->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div class="lf-card-body">
                @php $items = [
                    ['label'=>'Created by',   'val'=> $lead->createdBy?->name ?? 'System'],
                    ['label'=>'Assigned to',  'val'=> $lead->assignedTo?->name ?? 'Unassigned'],
                    ['label'=>'Last updated', 'val'=> $lead->updated_at->diffForHumans()],
                    ['label'=>'Lead ID',      'val'=> 'LD-'.str_pad($lead->id,4,'0',STR_PAD_LEFT)],
                ]; @endphp
                @foreach($items as $item)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #334155;">
                    <span style="font-size:11px;color:#64748b">{{ $item['label'] }}</span>
                    <span style="font-size:12px;font-weight:600;color:#f1f5f9">{{ $item['val'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- /lf-right --}}

</div>{{-- /lf-grid --}}
