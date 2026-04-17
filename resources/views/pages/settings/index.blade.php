@extends('layouts.app')

@section('title', 'Settings — myAgenci.ai')

@push('styles')
<style>
/* ── Settings page ── */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
    padding: 32px;
}
.settings-card {
    background: #fff;
    border: 1px solid #e1dee3;
    border-radius: 14px;
    padding: 24px 20px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    cursor: pointer;
    transition: box-shadow .2s, transform .2s;
    text-decoration: none;
    color: inherit;
}
.settings-card:hover {
    box-shadow: 0 4px 20px rgba(96,48,140,.12);
    transform: translateY(-2px);
}
.settings-card-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.ic-status   { background: #f0eadb; }
.ic-source   { background: #f3e6da; }
.ic-outcome  { background: #f5e0e6; }
.ic-subcat   { background: #eaf0fb; }
.settings-card-body h4 { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
.settings-card-body p  { font-size: 12px; color: #9e9e9e; margin: 0; }
.page-heading { padding: 32px 32px 0; font-size: 20px; font-weight: 700; }
.page-sub     { padding: 4px 32px 0;  font-size: 14px; color:#9e9e9e; }
</style>
@endpush

@section('content')
<main class="main-content">

    @include('layouts.header')

    <h2 class="page-heading">Settings</h2>
    <p  class="page-sub">Manage your CRM configuration</p>

    <div class="settings-grid">

        <a href="{{ route('settings.lead-statuses.index') }}" class="settings-card">
            <div class="settings-card-icon ic-status">🏷️</div>
            <div class="settings-card-body">
                <h4>Lead Status</h4>
                <p>Manage pipeline stages for your leads</p>
            </div>
        </a>

        <a href="{{ route('settings.lead-sources.index') }}" class="settings-card">
            <div class="settings-card-icon ic-source">📡</div>
            <div class="settings-card-body">
                <h4>Lead Source</h4>
                <p>Track where your leads are coming from</p>
            </div>
        </a>

        <a href="{{ route('settings.outcome-categories.index') }}" class="settings-card">
            <div class="settings-card-icon ic-outcome">📂</div>
            <div class="settings-card-body">
                <h4>Outcome Category</h4>
                <p>Group call outcomes into categories</p>
            </div>
        </a>

        <a href="{{ route('settings.outcome-sub-categories.index') }}" class="settings-card">
            <div class="settings-card-icon ic-subcat">🗂️</div>
            <div class="settings-card-body">
                <h4>Outcome Sub Category</h4>
                <p>Define detailed outcomes per category</p>
            </div>
        </a>

    </div>
</main>
@endsection
