@extends('layouts.app')

@section('title', 'Product — ' . $product->product_name)

@push('styles')
@include('pages.products.product_style')
@endpush

@section('content')
@php
    $gstColors = [
        0=>'#7c7c7c', 3=>'#16a34a', 5=>'#0f766e',
        12=>'#2563eb', 18=>'#ea580c', 28=>'#dc2626'
    ];
    $gstBgs = [
        0=>'#f5f4f6', 3=>'#f0fdf4', 5=>'#f0fdfa',
        12=>'#eff6ff', 18=>'#fff7ed', 28=>'#fef2f2'
    ];
    $gstBorders = [
        0=>'#e1dee3', 3=>'#bbf7d0', 5=>'#99f6e4',
        12=>'#bfdbfe', 18=>'#fed7aa', 28=>'#fecaca'
    ];
    $gstPct      = (int) $product->gst_percent;
    $gstColor    = $gstColors[$gstPct] ?? '#ea580c';
    $gstBg       = $gstBgs[$gstPct] ?? '#fff7ed';
    $gstBorder   = $gstBorders[$gstPct] ?? '#fed7aa';
    $avatarColors = ['#fe5f04','#7c3aed','#2563eb','#16a34a','#be123c','#0284c7'];
    $heroColor   = $avatarColors[$product->id % count($avatarColors)];
@endphp

<div class="pshow-page">

    <div class="pshow-topbar">
        <div>
            <div class="pshow-title">Product Detail</div>
            <div class="pshow-crumb">
                <a href="{{ route('products.index') }}">Products</a> › {{ $product->product_name }}
            </div>
        </div>
        <div class="pshow-topbar-right">
            <a href="{{ route('products.edit', $product) }}" class="pshow-btn pshow-btn-primary">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Product
            </a>
            <a href="{{ route('products.index') }}" class="pshow-btn pshow-btn-outline">← Back</a>
        </div>
    </div>

    <div class="pshow-body">

        @if(session('success'))
        <div class="pshow-alert pshow-alert-success">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {!! session('success') !!}
        </div>
        @endif

        {{-- Hero --}}
        <div class="pshow-hero">
            <div class="pshow-hero-avatar" style="background:{{ $heroColor }}">
                {{ strtoupper(substr($product->product_name, 0, 1)) }}
            </div>
            <div>
                <div class="pshow-hero-name">{{ $product->product_name }}</div>
                <div class="pshow-hero-code">{{ $product->product_code }}</div>
            </div>
            <div class="pshow-hero-right">
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:700;background:{{ $gstBg }};color:{{ $gstColor }};border:1px solid {{ $gstBorder }}">
                    {{ $product->gst_label }}
                </span>
                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:700;background:{{ $product->is_active?'#f0fdf4':'#fef2f2' }};color:{{ $product->is_active?'#16a34a':'#dc2626' }};border:1px solid {{ $product->is_active?'#bbf7d0':'#fecaca' }}">
                    {{ $product->is_active ? '● Active' : '● Inactive' }}
                </span>
            </div>
        </div>

        <div class="pshow-grid">

            {{-- Left --}}
            <div class="pshow-left">
                {{-- Core Info --}}
                <div class="pshow-card">
                    <div class="pshow-card-head">
                        <div class="pshow-card-title">📋 Product Information</div>
                    </div>
                    <div class="pshow-card-body">
                        <div class="pshow-info-grid">
                            <div class="pshow-info-item"><div class="pil">Product Name</div><div class="piv">{{ $product->product_name }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Product Code</div><div class="piv" style="font-family:monospace">{{ $product->product_code }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Category</div><div class="piv {{ !$product->category?'muted':'' }}">{{ $product->category ?? '—' }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Unit</div><div class="piv">{{ $product->unit }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Status</div><div class="piv" style="color:{{ $product->is_active?'#16a34a':'#dc2626' }}">{{ $product->is_active?'Active':'Inactive' }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Created By</div><div class="piv">{{ $product->createdBy?->name ?? 'System' }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Created</div><div class="piv">{{ $product->created_at->format('d M Y') }}</div></div>
                            <div class="pshow-info-item"><div class="pil">Last Updated</div><div class="piv">{{ $product->updated_at->diffForHumans() }}</div></div>
                        </div>
                        @if($product->description)
                        <div style="margin-top:14px;padding:12px;background:#fafafa;border-radius:9px;border:1px solid #f0eef2;font-size:13px;color:#2e2e2e;line-height:1.7">
                            {{ $product->description }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Danger zone --}}
                <div class="pshow-card" style="border-color:#fecaca">
                    <div class="pshow-card-head" style="background:#fffafa;border-bottom-color:#fecaca">
                        <div class="pshow-card-title" style="color:#dc2626">⚠️ Danger Zone</div>
                    </div>
                    <div class="pshow-card-body" style="display:flex;gap:10px">
                        <form method="POST" action="{{ route('products.toggle', $product) }}" style="flex:1">
                            @csrf @method('PATCH')
                            <button type="submit" class="pshow-btn" style="width:100%;justify-content:center;background:{{ $product->is_active?'#fef2f2':'#f0fdf4' }};color:{{ $product->is_active?'#dc2626':'#16a34a' }};border:1px solid {{ $product->is_active?'#fecaca':'#bbf7d0' }}">
                                {{ $product->is_active ? '🔒 Deactivate' : '✅ Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" style="flex:1"
                              onsubmit="return confirm('Permanently delete {{ addslashes($product->product_name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="pshow-btn pshow-btn-danger" style="width:100%;justify-content:center">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                Delete Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right --}}
            <div class="pshow-right">

                {{-- Price breakdown --}}
                <div class="pshow-price-card">
                    <div class="pshow-price-head">💰 Price Breakdown</div>
                    <div class="pshow-price-body">
                        <div style="text-align:center;padding:16px;background:{{ $gstBg }};border-radius:12px;border:1px solid {{ $gstBorder }};margin-bottom:6px">
                            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:{{ $gstColor }};margin-bottom:6px">{{ $product->gst_label }}</div>
                            <div style="font-size:28px;font-weight:900;color:{{ $gstColor }}">{{ $product->gst_percent }}%</div>
                        </div>
                        <div class="pshow-price-row">
                            <span class="pshow-price-lbl">Base Rate (excl. GST)</span>
                            <span class="pshow-price-val">{{ $product->formatted_rate }}</span>
                        </div>
                        @if($product->gst_amount > 0)
                        <div class="pshow-price-row">
                            <span class="pshow-price-lbl">GST ({{ $product->gst_percent }}%)</span>
                            <span class="pshow-price-val" style="color:{{ $gstColor }}">₹{{ number_format($product->gst_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="pshow-price-row grand">
                            <span class="pshow-price-lbl" style="font-weight:700;color:#2e2e2e">Final Price (incl. GST)</span>
                            <span class="pshow-price-val">{{ $product->formatted_rate_with_gst }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick summary --}}
                <div class="pshow-card">
                    <div class="pshow-card-head">
                        <div class="pshow-card-title">📊 Summary</div>
                    </div>
                    <div class="pshow-card-body">
                        @php
                            $summaryItems = [
                                ['Unit',             $product->unit],
                                ['GST Rate',         $product->gst_percent.'%'],
                                ['GST Amount',       '₹'.number_format($product->gst_amount,2)],
                                ['Rate (excl. GST)', $product->formatted_rate],
                                ['Rate (incl. GST)', $product->formatted_rate_with_gst],
                            ];
                        @endphp
                        @foreach($summaryItems as $item)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f7f6f9">
                            <span style="font-size:12px;color:#9e9e9e">{{ $item[0] }}</span>
                            <span style="font-size:13px;font-weight:700;color:#121212">{{ $item[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
