@extends('layouts.app')
@section('title', 'Add New Product')

@section('content')
<div class="prf-page">
    <div class="prf-topbar">
        <div>
            <div class="prf-title">Add New Product</div>
            <div class="prf-crumb"><a href="{{ route('products.index') }}">Products</a> › Create</div>
        </div>
        <div class="prf-topbar-right">
            <a href="{{ route('products.index') }}" class="prf-btn prf-btn-outline">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </div>
    </div>
    <div class="prf-body">
        <form method="POST" action="{{ route('products.store') }}" id="productForm">
            @csrf
            @include('pages.products.form', ['product' => null])
        </form>
    </div>
</div>
@endsection
