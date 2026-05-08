@extends('layouts.app')

@section('title', 'Asset Entry - ' . $asset->asset_name)

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Asset Entry Profile</div>
            <div class="eob-breadcrumb">HRMS > Assets > {{ $asset->asset_name }}</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('assets.edit', $asset) }}" class="eob-btn eob-btn-primary">Edit</a>
            <a href="{{ route('assets.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        @if(session('success'))
            <div class="eob-alert eob-alert-success">{!! session('success') !!}</div>
        @endif

        <div class="eob-show-layout">
            <div class="eob-profile-sticky">
                <div class="eob-profile">
                    <div class="eob-profile-banner"></div>
                    <div class="eob-profile-body">
                        <div class="eob-avatar">{{ strtoupper(substr($asset->asset_name, 0, 2)) }}</div>
                        <div class="eob-profile-name">{{ $asset->asset_name }}</div>
                        <div class="eob-profile-mail">{{ $asset->asset_code }}</div>
                        <div style="margin-top:14px;">
                            <span class="eob-chip eob-chip-{{ $asset->asset_status === 'available' ? 'verified' : ($asset->asset_status === 'damaged' ? 'rejected' : 'pending') }}">
                                {{ ucwords(str_replace('_', ' ', $asset->asset_status)) }}
                            </span>
                        </div>

                        <div class="eob-empid-card">
                            <div class="eob-empid-label">Asset Code</div>
                            <div class="eob-empid-value">{{ $asset->asset_code }}</div>
                            <div class="eob-empid-sub">Quick reference code for inventory and handover tracking.</div>
                        </div>

                        <div class="eob-side-list">
                            <div class="eob-side-item">
                                <div class="eob-side-label">Category</div>
                                <div class="eob-side-value">{{ $asset->asset_category ?: 'N/A' }}</div>
                            </div>
                            <div class="eob-side-item">
                                <div class="eob-side-label">Assigned To</div>
                                <div class="eob-side-value">{{ $asset->assignedEmployee?->name ?: 'Not assigned' }}</div>
                            </div>
                            <div class="eob-side-item">
                                <div class="eob-side-label">Location</div>
                                <div class="eob-side-value">{{ $asset->location ?: 'N/A' }}</div>
                            </div>
                            <div class="eob-side-item">
                                <div class="eob-side-label">Created By</div>
                                <div class="eob-side-value">{{ $asset->creator?->name ?? 'System' }}</div>
                            </div>
                            <div class="eob-side-item">
                                <div class="eob-side-label">Updated By</div>
                                <div class="eob-side-value">{{ $asset->updater?->name ?? 'System' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eob-section-stack">
                <div class="eob-show-card">
                    <div class="eob-card-head">
                        <div>
                            <div class="eob-card-title">Asset Details</div>
                            <div class="eob-card-sub">Core asset identity, purchase, and lifecycle information.</div>
                        </div>
                    </div>
                    <div class="eob-card-body">
                        <div class="eob-show-grid">
                            <div class="eob-show-item"><div class="eob-show-label">Asset Code</div><div class="eob-show-value">{{ $asset->asset_code }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Asset Name</div><div class="eob-show-value">{{ $asset->asset_name }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Category</div><div class="eob-show-value">{{ $asset->asset_category ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Status</div><div class="eob-show-value">{{ ucwords(str_replace('_', ' ', $asset->asset_status)) }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Brand</div><div class="eob-show-value">{{ $asset->brand ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Model</div><div class="eob-show-value">{{ $asset->model_name ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Serial Number</div><div class="eob-show-value">{{ $asset->serial_number ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Location</div><div class="eob-show-value">{{ $asset->location ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Purchase Date</div><div class="eob-show-value">{{ optional($asset->purchase_date)->format('d M Y') ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Purchase Cost</div><div class="eob-show-value">{{ $asset->purchase_cost !== null ? number_format((float) $asset->purchase_cost, 2) : 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Vendor Name</div><div class="eob-show-value">{{ $asset->vendor_name ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Invoice Number</div><div class="eob-show-value">{{ $asset->invoice_number ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Warranty Expiry</div><div class="eob-show-value">{{ optional($asset->warranty_expiry_date)->format('d M Y') ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Assigned Date</div><div class="eob-show-value">{{ optional($asset->assigned_date)->format('d M Y') ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Assigned Employee</div><div class="eob-show-value">{{ $asset->assignedEmployee ? $asset->assignedEmployee->employee_id . ' - ' . $asset->assignedEmployee->name : 'Not assigned' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Condition Notes</div><div class="eob-show-value">{{ $asset->condition_notes ?: 'N/A' }}</div></div>
                            <div class="eob-show-item"><div class="eob-show-label">Description</div><div class="eob-show-value">{{ $asset->description ?: 'N/A' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
