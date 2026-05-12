@extends('layouts.app')

@section('title', 'Visitor QR Code')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
    <style>
        .vm-qr-layout { display:grid; grid-template-columns:360px minmax(0, 1fr); gap:18px; align-items:start; }
        .vm-qr-box { display:flex; align-items:center; justify-content:center; padding:22px; background:#fff; border:1px solid #e1dee3; border-radius:18px; }
        .vm-qr-box img { width:100%; max-width:320px; height:auto; }
        .vm-link-box { padding:14px; border:1px solid #f0eef2; border-radius:12px; background:#fafafa; word-break:break-all; font-size:13px; color:#121212; }
        @media (max-width: 900px) { .vm-qr-layout { grid-template-columns:1fr; } }
    </style>
@endpush

@section('content')
<div class="eob-page">
    <div class="eob-topbar">
        <div>
            <div class="eob-title">Visitor QR Code</div>
            <div class="eob-breadcrumb">HRMS > Visitor Management > QR Code</div>
        </div>
        <div class="eob-actions">
            <a href="{{ route('visitor-management.index') }}" class="eob-btn eob-btn-ghost">Back</a>
        </div>
    </div>

    <div class="eob-body">
        <div class="vm-qr-layout">
            <div class="vm-qr-box">
                <img src="{{ $qrCodeUrl }}" alt="Visitor entry QR code">
            </div>
            <div class="eob-card">
                <div class="eob-card-head">
                    <div>
                        <div class="eob-card-title">Common Visitor Entry QR</div>
                        <div class="eob-card-sub">Print or display this QR at reception. Visitors can scan and submit the entry form.</div>
                    </div>
                </div>
                <div class="eob-card-body">
                    <div class="eob-show-item">
                        <div class="eob-show-label">Visitor Form Link</div>
                        <div class="vm-link-box">{{ $visitorFormUrl }}</div>
                    </div>
                    <div class="eob-actions" style="margin-top:16px;">
                        <a href="{{ $visitorFormUrl }}" target="_blank" rel="noopener" class="eob-btn eob-btn-primary">Open Form</a>
                        <button type="button" class="eob-btn eob-btn-ghost" onclick="window.print()">Print QR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
