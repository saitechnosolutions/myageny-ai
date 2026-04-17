@extends('layouts.app')

@section('title', 'Products Master')

@include('pages.products.product_style')

@section('content')
<div class="prd-page">

    {{-- Topbar --}}
    <div class="prd-topbar">
        <div>
            <div class="prd-title">Products Master</div>
            <div class="prd-crumb">Settings › Products</div>
        </div>
        <div class="prd-topbar-right">
            <a href="{{ route('products.create') }}" class="prd-btn prd-btn-primary">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Product
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('products.index') }}" id="filterForm">
    <div class="prd-filter-bar">
        <div class="prd-search-wrap">
            <svg class="prd-search-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="prd-search-inp" placeholder="Search name, code, category…"
                   value="{{ request('search') }}" oninput="delaySubmit()">
        </div>

        <div class="prd-fw">
            <svg class="prd-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            <select name="category" class="prd-sel" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <svg class="prd-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        <div class="prd-fw">
            <svg class="prd-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            <select name="gst" class="prd-sel" onchange="this.form.submit()">
                <option value="">All GST Rates</option>
                @foreach(\App\Models\Product::GST_RATES as $rate)
                <option value="{{ $rate }}" {{ request('gst')==$rate?'selected':'' }}>
                    {{ $rate === 0 ? 'GST Exempt (0%)' : $rate.'% GST' }}
                </option>
                @endforeach
            </select>
            <svg class="prd-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        <div class="prd-fw">
            <svg class="prd-fi" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
            <select name="status" class="prd-sel" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="1" {{ request('status')==='1'?'selected':'' }}>Active</option>
                <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>
            </select>
            <svg class="prd-fc" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        @if(request()->hasAny(['search','category','gst','status']))
        <a href="{{ route('products.index') }}" class="prd-reset">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.36"/></svg>
            Reset
        </a>
        @endif
    </div>
    </form>

    {{-- Body --}}
    <div class="prd-body">

        {{-- Flash --}}
        @if(session('success'))
        <div class="prd-alert prd-alert-success">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {!! session('success') !!}
        </div>
        @endif
        @if(session('error'))
        <div class="prd-alert prd-alert-error">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            {!! session('error') !!}
        </div>
        @endif

        {{-- Stats --}}
        <div class="prd-stats-row">
            @php
                $statItems = [
                    ['label'=>'Total Products', 'value'=>$stats['total'],      'color'=>'#fe5f04','bg'=>'#fff0e6', 'icon'=>'<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
                    ['label'=>'Active',         'value'=>$stats['active'],     'color'=>'#16a34a','bg'=>'#f0fdf4', 'icon'=>'<polyline points="20 6 9 17 4 12"/>'],
                    ['label'=>'Inactive',       'value'=>$stats['inactive'],   'color'=>'#dc2626','bg'=>'#fef2f2', 'icon'=>'<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'],
                    ['label'=>'Categories',     'value'=>$stats['categories'], 'color'=>'#7c3aed','bg'=>'#faf5ff', 'icon'=>'<path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M3 14h7v7H3z"/><path d="M14 14h7v7h-7z"/>'],
                ];
            @endphp
            @foreach($statItems as $s)
            <div class="prd-stat">
                <div class="prd-stat-icon" style="background:{{ $s['bg'] }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $s['color'] }}" stroke-width="2">{!! $s['icon'] !!}</svg>
                </div>
                <div class="prd-stat-label">{{ $s['label'] }}</div>
                <div class="prd-stat-value" style="color:{{ $s['color'] }}">{{ $s['value'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="prd-table-card">
            <div class="prd-table-top">
                <div>
                    <div class="prd-table-title">All Products</div>
                    <div class="prd-table-sub">Manage your product catalog, rates and GST</div>
                </div>
                <div class="prd-results">
                    Showing <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $products->total() }}</strong> products
                </div>
            </div>

            @if($products->isEmpty())
            <div class="prd-empty">
                <div class="prd-empty-ico">📦</div>
                <div class="prd-empty-title">No products found</div>
                <p style="font-size:13px">Try adjusting your filters or <a href="{{ route('products.create') }}" style="color:#fe5f04;font-weight:700">add a new product</a>.</p>
            </div>
            @else
            <div style="overflow-x:auto;">
                <table class="prd-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Rate (excl. GST)</th>
                            <th>GST</th>
                            <th>Rate (incl. GST)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $gstClasses = [0=>'prd-gst-0',3=>'prd-gst-3',5=>'prd-gst-5',12=>'prd-gst-12',18=>'prd-gst-18',28=>'prd-gst-28'];
                            $avatarColors = ['#fe5f04','#7c3aed','#2563eb','#16a34a','#be123c','#0284c7','#b45309','#0f766e'];
                        @endphp
                        @foreach($products as $product)
                        @php
                            $gstClass = $gstClasses[(int)$product->gst_percent] ?? 'prd-gst-0';
                            $avatarColor = $avatarColors[$product->id % count($avatarColors)];
                        @endphp
                        <tr>
                            <td style="color:#9e9e9e;font-size:11px;font-family:monospace">{{ $products->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="prd-name-cell">
                                    <div class="prd-avatar" style="background:{{ $avatarColor }}">
                                        {{ strtoupper(substr($product->product_name,0,1)) }}
                                    </div>
                                    <div>
                                        <div class="prd-pname">{{ $product->product_name }}</div>
                                        <div class="prd-pcode">{{ $product->product_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->category)
                                <span class="prd-cat">{{ $product->category }}</span>
                                @else
                                <span style="color:#9e9e9e;font-size:12px">—</span>
                                @endif
                            </td>
                            <td><span class="prd-unit">{{ $product->unit }}</span></td>
                            <td>
                                <div class="prd-rate">{{ $product->formatted_rate }}</div>
                            </td>
                            <td>
                                <span class="prd-gst-badge {{ $gstClass }}">
                                    {{ $product->gst_percent > 0 ? $product->gst_percent.'%' : 'Exempt' }}
                                </span>
                                @if($product->gst_amount > 0)
                                <div style="font-size:10px;color:#9e9e9e;margin-top:3px">₹{{ number_format($product->gst_amount,2) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="prd-rate">{{ $product->formatted_rate_with_gst }}</div>
                                @if($product->gst_percent > 0)
                                <div class="prd-rate-gst">incl. {{ $product->gst_percent }}% GST</div>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('products.toggle',$product) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <label class="prd-toggle" title="{{ $product->is_active?'Deactivate':'Activate' }}">
                                        <input type="checkbox" {{ $product->is_active?'checked':'' }} onchange="this.form.submit()">
                                        <div class="prd-toggle-sl"></div>
                                    </label>
                                </form>
                            </td>
                            <td>
                                <div class="prd-actions">
                                    <a href="{{ route('products.show',$product) }}" class="prd-act-btn" title="View">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('products.edit',$product) }}" class="prd-act-btn edit" title="Edit">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <button class="prd-act-btn del" title="Delete"
                                            onclick="prdConfirmDelete({{ $product->id }},'{{ addslashes($product->product_name) }}')">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
            <div class="prd-pag">
                <div class="prd-pag-info">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</div>
                <div class="prd-pag-links">
                    <a href="{{ $products->previousPageUrl() ?? '#' }}" class="prd-pag-link {{ !$products->onFirstPage()?'':'disabled' }}">‹</a>
                    @foreach($products->getUrlRange(max(1,$products->currentPage()-2),min($products->lastPage(),$products->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="prd-pag-link {{ $page==$products->currentPage()?'active':'' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $products->nextPageUrl() ?? '#' }}" class="prd-pag-link {{ $products->hasMorePages()?'':'disabled' }}">›</a>
                </div>
            </div>
            @endif

            @endif
        </div>

    </div>
</div>

{{-- Delete Modal --}}
<div class="prd-modal-overlay" id="deleteModal">
    <div class="prd-modal">
        <div class="prd-modal-icon">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <div class="prd-modal-title">Delete Product?</div>
        <div class="prd-modal-sub" id="deleteModalSub">This cannot be undone.</div>
        <div class="prd-modal-btns">
            <button class="prd-modal-btn prd-modal-cancel" onclick="prdCloseDelete()">Cancel</button>
            <form id="deleteForm" method="POST" style="flex:1">
                @csrf @method('DELETE')
                <button type="submit" class="prd-modal-btn prd-modal-delete" style="width:100%">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function prdConfirmDelete(id, name) {
    document.getElementById('deleteModalSub').textContent = `Delete "${name}"? This cannot be undone.`;
    document.getElementById('deleteForm').action = `/products/${id}`;
    document.getElementById('deleteModal').classList.add('open');
}
function prdCloseDelete() { document.getElementById('deleteModal').classList.remove('open'); }
document.getElementById('deleteModal').addEventListener('click', e => { if(e.target===document.getElementById('deleteModal')) prdCloseDelete(); });
document.addEventListener('keydown', e => { if(e.key==='Escape') prdCloseDelete(); });
let st;
function delaySubmit() { clearTimeout(st); st=setTimeout(()=>document.getElementById('filterForm').submit(),500); }
</script>
@endpush
