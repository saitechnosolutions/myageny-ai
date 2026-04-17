{{-- ================================================================
     FILE: resources/views/products/_form.blade.php
     Shared form body — used by create.blade.php & edit.blade.php
     $product is null on create, Product model on edit
================================================================ --}}

@php
    $isEdit  = !is_null($product);
    $old     = fn($field, $default = '') => old($field, $isEdit ? $product->{$field} : $default);
@endphp

<style>
/* ══════════════════════════════════════════════
   PRODUCT FORM STYLES
══════════════════════════════════════════════ */
.prf-page { display:flex; flex-direction:column; height:100%; overflow:hidden; background:#f4f5f7; font-family:'Inter',sans-serif; }
.prf-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; flex-shrink:0; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:0; z-index:30; }
.prf-title  { font-size:18px; font-weight:800; color:#121212; }
.prf-crumb  { font-size:12px; color:#9e9e9e; margin-top:2px; }
.prf-crumb a { color:#fe5f04; text-decoration:none; font-weight:600; }
.prf-topbar-right { display:flex; align-items:center; gap:10px; }
.prf-btn { display:flex; align-items:center; gap:6px; padding:8px 18px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; text-decoration:none; transition:all .15s; border:none; }
.prf-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 4px 14px rgba(254,95,4,.25); }
.prf-btn-primary:hover { transform:translateY(-1px); }
.prf-btn-outline { background:#fff; color:#121212; border:1px solid #e1dee3; }
.prf-btn-outline:hover { border-color:#9e9e9e; }
.prf-body { flex:1; overflow-y:auto; padding:24px 28px 40px; }
.prf-body::-webkit-scrollbar { width:5px; }
.prf-body::-webkit-scrollbar-thumb { background:#e1dee3; border-radius:3px; }
@keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

/* Layout */
.prf-grid { display:grid; grid-template-columns:1fr 320px; gap:20px; animation:fadeUp .35s ease both; }
.prf-left  { display:flex; flex-direction:column; gap:16px; }
.prf-right { display:flex; flex-direction:column; gap:16px; }

/* Card */
.prf-card { background:#fff; border:1px solid #e1dee3; border-radius:14px; overflow:hidden; }
.prf-card-head { display:flex; align-items:center; gap:10px; padding:15px 20px; border-bottom:1px solid #f0eef2; }
.prf-card-ico  { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.prf-card-title{ font-size:14px; font-weight:700; color:#121212; }
.prf-card-sub  { font-size:11px; color:#9e9e9e; margin-top:2px; }
.prf-card-body { padding:18px 20px; display:flex; flex-direction:column; gap:16px; }

/* Form elements */
.prf-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.prf-group { display:flex; flex-direction:column; gap:6px; }
.prf-group.span2 { grid-column:span 2; }
.prf-label { font-size:12px; font-weight:700; color:#2e2e2e; display:flex; align-items:center; gap:3px; }
.prf-req   { color:#dc2626; }
.prf-iw    { position:relative; }
.prf-ico   { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9e9e9e; pointer-events:none; width:13px; height:13px; }
.prf-inp, .prf-sel, .prf-ta {
    width:100%; padding:9px 12px 9px 32px;
    border:1px solid #e1dee3; border-radius:9px;
    font-size:13px; font-family:inherit; color:#121212;
    background:#fafafa; outline:none; transition:all .15s;
}
.prf-inp::placeholder, .prf-ta::placeholder { color:#b8b3aa; }
.prf-inp:focus, .prf-sel:focus, .prf-ta:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.prf-inp.err, .prf-sel.err { border-color:#dc2626; background:#fffafa; }
.prf-inp.ni, .prf-sel.ni   { padding-left:12px; }
.prf-sel { appearance:none; -webkit-appearance:none; cursor:pointer; }
.prf-sel-caret { position:absolute; right:9px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9e9e9e; width:12px; height:12px; }
.prf-ta  { resize:vertical; min-height:100px; padding:9px 12px; }
.prf-err { font-size:11px; color:#dc2626; margin-top:2px; }
.prf-hint{ font-size:11px; color:#9e9e9e; margin-top:2px; }

/* GST picker tiles */
.prf-gst-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.prf-gst-tile {
    display:flex; flex-direction:column; align-items:center; gap:3px;
    padding:10px 6px; border-radius:10px; border:1.5px solid #e1dee3;
    cursor:pointer; transition:all .15s; user-select:none;
}
.prf-gst-tile input { display:none; }
.prf-gst-tile:hover { border-color:#d4cfd8; background:#fafafa; }
.prf-gst-pct { font-size:16px; font-weight:800; color:#121212; }
.prf-gst-lbl { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#9e9e9e; }

.prf-gst-tile.sel-0  { border-color:#e1dee3; background:#f5f4f6; }
.prf-gst-tile.sel-0  .prf-gst-pct { color:#7c7c7c; }
.prf-gst-tile.sel-3  { border-color:#bbf7d0; background:#f0fdf4; }
.prf-gst-tile.sel-3  .prf-gst-pct { color:#16a34a; }
.prf-gst-tile.sel-5  { border-color:#99f6e4; background:#f0fdfa; }
.prf-gst-tile.sel-5  .prf-gst-pct { color:#0f766e; }
.prf-gst-tile.sel-12 { border-color:#bfdbfe; background:#eff6ff; }
.prf-gst-tile.sel-12 .prf-gst-pct { color:#2563eb; }
.prf-gst-tile.sel-18 { border-color:#fed7aa; background:#fff7ed; }
.prf-gst-tile.sel-18 .prf-gst-pct { color:#ea580c; }
.prf-gst-tile.sel-28 { border-color:#fecaca; background:#fef2f2; }
.prf-gst-tile.sel-28 .prf-gst-pct { color:#dc2626; }

/* Live preview box */
.prf-preview-box { border-radius:12px; overflow:hidden; border:1px solid #e1dee3; }
.prf-preview-head { padding:12px 16px; background:#f8f8f8; border-bottom:1px solid #f0eef2; font-size:12px; font-weight:700; color:#7c7c7c; text-transform:uppercase; letter-spacing:.5px; }
.prf-preview-body { padding:16px; display:flex; flex-direction:column; gap:10px; }
.prf-preview-row  { display:flex; justify-content:space-between; align-items:center; }
.prf-preview-lbl  { font-size:12px; color:#9e9e9e; }
.prf-preview-val  { font-size:13px; font-weight:700; color:#121212; }
.prf-preview-row.grand { border-top:1px solid #f0eef2; padding-top:10px; margin-top:2px; }
.prf-preview-row.grand .prf-preview-val { font-size:18px; font-weight:800; color:#fe5f04; }
.prf-preview-row.grand .prf-preview-lbl { font-weight:700; color:#2e2e2e; }

/* Unit selector */
.prf-unit-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:7px; }
.prf-unit-tile {
    display:flex; align-items:center; justify-content:center;
    padding:8px 6px; border-radius:9px; border:1.5px solid #e1dee3;
    cursor:pointer; transition:all .15s; font-size:12px; font-weight:700;
    color:#7c7c7c; user-select:none;
}
.prf-unit-tile input { display:none; }
.prf-unit-tile:hover { border-color:#d4cfd8; background:#fafafa; }
.prf-unit-tile.selected { border-color:#fe5f04; background:#fff0e6; color:#fe5f04; }

/* Active toggle */
.prf-toggle-row { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-radius:10px; background:#fafafa; border:1px solid #f0eef2; }
.prf-toggle-label { font-size:13px; font-weight:600; color:#121212; }
.prf-toggle-sub   { font-size:11px; color:#9e9e9e; margin-top:2px; }
.prf-toggle { position:relative; width:44px; height:24px; flex-shrink:0; }
.prf-toggle input { display:none; }
.prf-toggle-slider { position:absolute; inset:0; border-radius:24px; background:#e1dee3; cursor:pointer; transition:background .2s; }
.prf-toggle-slider::before { content:''; position:absolute; width:18px; height:18px; border-radius:50%; background:#fff; top:3px; left:3px; transition:transform .2s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
.prf-toggle input:checked + .prf-toggle-slider { background:#16a34a; }
.prf-toggle input:checked + .prf-toggle-slider::before { transform:translateX(20px); }

/* Submit bar */
.prf-submit { display:flex; gap:10px; padding:15px 20px; border-top:1px solid #f0eef2; background:#fafafa; }
</style>

{{-- Form fields --}}
<div class="prf-grid">

    {{-- ── LEFT ── --}}
    <div class="prf-left">

        {{-- Core Info --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <div class="prf-card-ico" style="background:#fff0e6">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#fe5f04" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg>
                </div>
                <div>
                    <div class="prf-card-title">Product Information</div>
                    <div class="prf-card-sub">Name, code, category and description</div>
                </div>
            </div>
            <div class="prf-card-body">
                <div class="prf-row">
                    <div class="prf-group">
                        <label class="prf-label">Product Name <span class="prf-req">*</span></label>
                        <div class="prf-iw">
                            <svg class="prf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg>
                            <input type="text" name="product_name" class="prf-inp {{ $errors->has('product_name')?'err':'' }}"
                                   placeholder="e.g. CRM Pro License" value="{{ $old('product_name') }}" required>
                        </div>
                        @error('product_name')<div class="prf-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="prf-group">
                        <label class="prf-label">Product Code</label>
                        <div class="prf-iw">
                            <svg class="prf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            <input type="text" name="product_code" class="prf-inp {{ $errors->has('product_code')?'err':'' }}"
                                   placeholder="Auto-generated if blank" value="{{ $old('product_code') }}">
                        </div>
                        <div class="prf-hint">Leave blank to auto-generate (PRD-0001)</div>
                        @error('product_code')<div class="prf-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="prf-group">
                    <label class="prf-label">Category</label>
                    <div class="prf-iw">
                        <svg class="prf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M3 14h7v7H3z"/><path d="M14 14h7v7h-7z"/></svg>
                        <input type="text" name="category" class="prf-inp"
                               placeholder="e.g. Software, Hardware, Service" value="{{ $old('category') }}"
                               list="prf-categories-list">
                        <datalist id="prf-categories-list">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="prf-hint">Type to create a new category or pick an existing one</div>
                </div>

                <div class="prf-group">
                    <label class="prf-label">Description</label>
                    <textarea name="description" class="prf-ta"
                              placeholder="Product details, features, inclusions…" rows="4">{{ $old('description') }}</textarea>
                    @error('description')<div class="prf-err">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <div class="prf-card-ico" style="background:#eff6ff">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div>
                    <div class="prf-card-title">Pricing</div>
                    <div class="prf-card-sub">Rate, GST and computed values</div>
                </div>
            </div>
            <div class="prf-card-body">
                <div class="prf-row">
                    <div class="prf-group">
                        <label class="prf-label">Rate (Excl. GST) ₹ <span class="prf-req">*</span></label>
                        <div class="prf-iw">
                            <svg class="prf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            <input type="number" name="rate" id="prf_rate" class="prf-inp {{ $errors->has('rate')?'err':'' }}"
                                   placeholder="0.00" step="0.01" min="0"
                                   value="{{ $old('rate') }}" required oninput="prfCalc()">
                        </div>
                        @error('rate')<div class="prf-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="prf-group">
                        <label class="prf-label">Unit <span class="prf-req">*</span></label>
                        <div class="prf-iw">
                            <svg class="prf-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            <select name="unit" id="prf_unit" class="prf-sel {{ $errors->has('unit')?'err':'' }}">
                                @foreach(\App\Models\Product::UNITS as $uk => $ul)
                                <option value="{{ $uk }}" {{ $old('unit','Nos') === $uk ? 'selected' : '' }}>{{ $ul }}</option>
                                @endforeach
                            </select>
                            <svg class="prf-sel-caret" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        @error('unit')<div class="prf-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- GST Rate picker --}}
                <div class="prf-group">
                    <label class="prf-label">GST Rate <span class="prf-req">*</span></label>
                    @error('gst_percent')<div class="prf-err" style="margin-bottom:6px">{{ $message }}</div>@enderror
                    <div class="prf-gst-grid" id="prfGstGrid">
                        @php
                            $gstInfo = [
                                0  => ['label'=>'Exempt',   'em'=>'🟢'],
                                3  => ['label'=>'3% GST',   'em'=>'🟡'],
                                5  => ['label'=>'5% GST',   'em'=>'🟡'],
                                12 => ['label'=>'12% GST',  'em'=>'🔵'],
                                18 => ['label'=>'18% GST',  'em'=>'🟠'],
                                28 => ['label'=>'28% GST',  'em'=>'🔴'],
                            ];
                            $currentGst = (float) $old('gst_percent', 18);
                        @endphp
                        @foreach($gstInfo as $rate => $info)
                        <div class="prf-gst-tile {{ (float)$currentGst === (float)$rate ? 'sel-'.$rate : '' }}"
                             data-rate="{{ $rate }}"
                             onclick="prfPickGst(this)">
                            <input type="radio" name="gst_percent" value="{{ $rate }}"
                                   {{ (float)$currentGst === (float)$rate ? 'checked' : '' }}>
                            <div class="prf-gst-pct">{{ $rate }}%</div>
                            <div class="prf-gst-lbl">{{ $info['label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="gst_percent" id="prf_gst_val" value="{{ $old('gst_percent', 18) }}">
                </div>
            </div>
            <div class="prf-submit">
                <button type="submit" class="prf-btn prf-btn-primary" style="flex:1">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $isEdit ? 'Save Changes' : 'Create Product' }}
                </button>
                <a href="{{ route('products.index') }}" class="prf-btn prf-btn-outline">Cancel</a>
            </div>
        </div>

    </div>{{-- /prf-left --}}

    {{-- ── RIGHT ── --}}
    <div class="prf-right">

        {{-- Live Price Preview --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <div class="prf-card-ico" style="background:#f0fdf4">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <div class="prf-card-title">Price Preview</div>
                    <div class="prf-card-sub">Live GST calculation</div>
                </div>
            </div>
            <div class="prf-card-body">
                <div class="prf-preview-box">
                    <div class="prf-preview-head">Price Breakdown</div>
                    <div class="prf-preview-body">
                        <div class="prf-preview-row">
                            <span class="prf-preview-lbl">Base Rate</span>
                            <span class="prf-preview-val" id="prev_rate">₹0.00</span>
                        </div>
                        <div class="prf-preview-row">
                            <span class="prf-preview-lbl">GST (<span id="prev_gst_pct">18</span>%)</span>
                            <span class="prf-preview-val" style="color:#ea580c" id="prev_gst_amt">₹0.00</span>
                        </div>
                        <div class="prf-preview-row grand">
                            <span class="prf-preview-lbl">Final Price (incl. GST)</span>
                            <span class="prf-preview-val" id="prev_total">₹0.00</span>
                        </div>
                    </div>
                </div>

                {{-- GST code indicator --}}
                <div id="prev_gst_badge" style="display:flex;align-items:center;justify-content:center;padding:10px;border-radius:9px;font-size:13px;font-weight:700;background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;">
                    18% GST Applicable
                </div>
            </div>
        </div>

        {{-- Unit picker visual --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <div>
                    <div class="prf-card-title">Unit of Measure</div>
                    <div class="prf-card-sub">Sold per</div>
                </div>
            </div>
            <div class="prf-card-body">
                <div class="prf-unit-grid" id="prfUnitGrid">
                    @foreach(\App\Models\Product::UNITS as $uk => $ul)
                    <div class="prf-unit-tile {{ $old('unit','Nos') === $uk ? 'selected' : '' }}"
                         data-val="{{ $uk }}" onclick="prfPickUnit(this)">
                        {{ $uk }}
                    </div>
                    @endforeach
                </div>
                <div class="prf-hint" style="margin-top:6px">Unit selection updates the dropdown above too</div>
            </div>
        </div>

        {{-- Active status --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <div>
                    <div class="prf-card-title">Status</div>
                    <div class="prf-card-sub">Active products appear in quotations</div>
                </div>
            </div>
            <div class="prf-card-body">
                <div class="prf-toggle-row">
                    <div>
                        <div class="prf-toggle-label">Product Active</div>
                        <div class="prf-toggle-sub">Available for use in leads & quotes</div>
                    </div>
                    <label class="prf-toggle">
                        <input type="checkbox" name="is_active" value="1"
                               {{ $old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <div class="prf-toggle-slider"></div>
                    </label>
                </div>
            </div>
        </div>

        @if($isEdit)
        {{-- Edit summary card --}}
        <div class="prf-card" style="background:linear-gradient(135deg,#0f172a,#1e293b);border-color:#334155">
            <div class="prf-card-head" style="border-bottom-color:#334155">
                <div class="prf-card-title" style="color:#f1f5f9">Product Info</div>
            </div>
            <div class="prf-card-body">
                @php
                    $sumItems = [
                        'Code'     => $product->product_code,
                        'Created'  => $product->created_at->format('d M Y'),
                        'Updated'  => $product->updated_at->diffForHumans(),
                        'Created by' => $product->createdBy?->name ?? 'System',
                    ];
                @endphp
                @foreach($sumItems as $lbl => $val)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #334155">
                    <span style="font-size:11px;color:#64748b">{{ $lbl }}</span>
                    <span style="font-size:12px;font-weight:600;color:#f1f5f9">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- /prf-right --}}

</div>{{-- /prf-grid --}}

<script>
(function () {

    /* ── Price preview ─────────────────────────────────────────── */
    var gstColors = {
        0:  ['#7c7c7c','#f5f4f6','Exempt from GST','#e1dee3'],
        3:  ['#16a34a','#f0fdf4','3% GST Applicable','#bbf7d0'],
        5:  ['#0f766e','#f0fdfa','5% GST Applicable','#99f6e4'],
        12: ['#2563eb','#eff6ff','12% GST Applicable','#bfdbfe'],
        18: ['#ea580c','#fff7ed','18% GST Applicable','#fed7aa'],
        28: ['#dc2626','#fef2f2','28% GST Applicable','#fecaca']
    };

    function prfCalcInternal() {
        var rate    = parseFloat(document.getElementById('prf_rate')?.value)    || 0;
        var gstPct  = parseFloat(document.getElementById('prf_gst_val')?.value) || 0;
        var gstAmt  = rate * (gstPct / 100);
        var total   = rate + gstAmt;

        var el = function(id){ return document.getElementById(id); };
        el('prev_rate')    && (el('prev_rate').textContent    = '₹' + rate.toFixed(2));
        el('prev_gst_pct') && (el('prev_gst_pct').textContent = gstPct);
        el('prev_gst_amt') && (el('prev_gst_amt').textContent = '₹' + gstAmt.toFixed(2));
        el('prev_total')   && (el('prev_total').textContent   = '₹' + total.toFixed(2));

        var badge = el('prev_gst_badge');
        var c = gstColors[gstPct] || gstColors[18];
        if (badge) {
            badge.textContent    = c[2];
            badge.style.color    = c[0];
            badge.style.background = c[1];
            badge.style.borderColor = c[3];
        }
    }

    window.prfCalc = prfCalcInternal;

    /* ── GST tile picker ────────────────────────────────────────── */
    window.prfPickGst = function (tile) {
        var rate = parseInt(tile.dataset.rate);

        document.querySelectorAll('#prfGstGrid .prf-gst-tile').forEach(function (t) {
            t.className = 'prf-gst-tile';
        });
        tile.classList.add('sel-' + rate);
        tile.querySelector('input').checked = true;

        var hidden = document.getElementById('prf_gst_val');
        if (hidden) hidden.value = rate;

        prfCalcInternal();
    };

    /* ── Unit tile picker ───────────────────────────────────────── */
    window.prfPickUnit = function (tile) {
        document.querySelectorAll('#prfUnitGrid .prf-unit-tile').forEach(function (t) {
            t.classList.remove('selected');
        });
        tile.classList.add('selected');

        var sel = document.getElementById('prf_unit');
        if (sel) sel.value = tile.dataset.val;
    };

    /* Sync unit dropdown → tiles */
    var unitSel = document.getElementById('prf_unit');
    if (unitSel) {
        unitSel.addEventListener('change', function () {
            document.querySelectorAll('#prfUnitGrid .prf-unit-tile').forEach(function (t) {
                t.classList.toggle('selected', t.dataset.val === unitSel.value);
            });
        });
    }

    /* Run calc on load to populate preview with edit values */
    prfCalcInternal();

}());
</script>
