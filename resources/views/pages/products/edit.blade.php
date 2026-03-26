@extends('layouts.app')
@section('title', 'Edit Product — ' . $product->product_name)

@section('content')
<div class="prf-page">
    <div class="prf-topbar">
        <div>
            <div class="prf-title">Edit Product</div>
            <div class="prf-crumb">
                <a href="{{ route('products.index') }}">Products</a> ›
                <a href="{{ route('products.show', $product) }}">{{ $product->product_name }}</a> › Edit
            </div>
        </div>
        <div class="prf-topbar-right">
            <a href="{{ route('products.show', $product) }}" class="prf-btn prf-btn-outline">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                View
            </a>
            <a href="{{ route('products.index') }}" class="prf-btn prf-btn-outline">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </div>
    </div>
    <div class="prf-body">
        @if(session('success'))
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13px;display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {!! session('success') !!}
        </div>
        @endif
        <form method="POST" action="{{ route('products.update', $product) }}" id="productForm">
            @csrf @method('PUT')
            @include('products._form', ['product' => $product])
        </form>
    </div>
</div>
@endsection
