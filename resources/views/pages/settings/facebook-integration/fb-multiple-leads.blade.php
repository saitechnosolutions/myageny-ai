@extends('layouts.app')
@section('title', 'Facebook Campaigns — Settings')

@push('styles')
@include('pages.settings.partials.table-styles')
<style>
.fb-flow-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.fb-flow-head { padding:24px 24px 18px; border-bottom:1px solid #f1f1f1; display:flex; justify-content:space-between; gap:18px; align-items:flex-start; }
.fb-flow-title { font-size:18px; font-weight:700; color:#121212; margin:0 0 6px; }
.fb-flow-copy { font-size:13px; color:#8b8b8b; max-width:640px; margin:0; line-height:1.6; }
.fb-flow-chip { display:inline-flex; align-items:center; gap:8px; padding:9px 12px; border-radius:999px; background:#fff6ef; color:#b45309; font-size:12px; font-weight:700; border:1px solid #ffe1c7; white-space:nowrap; }
.fb-flow-body { padding:22px 24px 24px; }
.fb-campaign-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px; }
.fb-campaign-option { display:flex; align-items:flex-start; gap:14px; padding:16px 18px; border:1px solid #e9e5ea; border-radius:14px; background:linear-gradient(180deg,#fff 0%,#fffcfa 100%); transition:border-color .15s ease, box-shadow .15s ease, transform .15s ease; cursor:pointer; }
.fb-campaign-option:hover { border-color:#fdc9a6; box-shadow:0 8px 20px rgba(254,95,4,.08); transform:translateY(-1px); }
.fb-campaign-check { margin-top:3px; width:18px; height:18px; accent-color:#fe5f04; cursor:pointer; }
.fb-campaign-main { min-width:0; flex:1; }
.fb-campaign-name { font-size:15px; font-weight:700; color:#121212; margin:0 0 5px; line-height:1.5; }
.fb-campaign-meta { display:flex; flex-wrap:wrap; gap:8px; }
.fb-campaign-pill { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; background:#f7f4f9; color:#6b6470; border:1px solid #ece7ef; }
.fb-flow-actions { display:flex; justify-content:space-between; align-items:center; gap:14px; padding-top:18px; margin-top:18px; border-top:1px solid #f4f1f5; flex-wrap:wrap; }
.fb-flow-note { font-size:12px; color:#9a9a9a; }
.fb-empty-box { padding:48px 20px; text-align:center; color:#8f8f8f; }
.fb-empty-box h3 { margin:0 0 8px; font-size:17px; color:#333; }
.fb-empty-box p { margin:0; font-size:13px; }
@media (max-width: 900px) {
    .fb-campaign-grid { grid-template-columns:1fr; }
}
@media (max-width: 768px) {
    .crm-page-header, .fb-flow-head, .fb-flow-actions { flex-direction:column; align-items:flex-start; }
    .crm-page-body { padding:20px; }
    .fb-flow-head, .fb-flow-body { padding-left:18px; padding-right:18px; }
}
</style>
@endpush

@section('content')
<main class="main-content">
    <div class="crm-page-body">
        <div class="crm-page-header">
            <div>
                <h2 class="crm-title">Facebook Campaign Integration</h2>
                <p class="crm-subtitle">Choose the campaigns you want to integrate into your CRM.</p>
            </div>
            <div class="crm-header-actions">
                <a href="{{ route('settings.facebook-integration') }}" class="crm-btn crm-btn-ghost">Back</a>
                <span class="crm-btn crm-btn-primary" style="pointer-events:none;">Step 2 of 3</span>
            </div>
        </div>

        @include('pages.settings.partials.alert')

        <div class="man">
            @php
                $campaigns = collect($adid)
                    ->flatMap(function ($item) {
                        return App\Models\CampaignMaster::where('ad_id', $item['id'])
                            ->where('status', 1)
                            ->where('is_integrated', 0)
                            ->get();
                    });
            @endphp

            <div class="fb-flow-card">
                <div class="fb-flow-head">
                    <div>
                        <h3 class="fb-flow-title">Select Campaigns</h3>
                        <p class="fb-flow-copy">Choose one or more campaigns from the selected ad accounts. Once selected, we’ll continue to lead mapping and user assignment.</p>
                    </div>
                    <div class="fb-flow-chip">{{ $campaigns->count() }} Campaigns Available</div>
                </div>

                <div class="fb-flow-body">
                    <div id="multipleCampaignSelect">
                        @if ($campaigns->count())
                            <form>
                                <div class="fb-campaign-grid">
                                    @foreach ($campaigns as $camp)
                                        <label class="fb-campaign-option">
                                            <input
                                                class="campaign_select fb-campaign-check"
                                                type="checkbox"
                                                value="{{ $camp->id }}"
                                                name="campaign_select">
                                            <div class="fb-campaign-main">
                                                <h4 class="fb-campaign-name">{{ $camp->campaign_name }}</h4>
                                                <div class="fb-campaign-meta">
                                                    <span class="fb-campaign-pill">Campaign ID: {{ $camp->camp_id }}</span>
                                                    <span class="fb-campaign-pill">Ad Account: {{ $camp->ad_id }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="fb-flow-actions">
                                    <div class="fb-flow-note">Choose at least one campaign to continue with field mapping.</div>
                                    <button type="button" class="crm-btn crm-btn-primary" id="chooseCampaigns">Next</button>
                                </div>
                            </form>
                        @else
                            <div class="fb-empty-box">
                                <h3>No Campaigns Available</h3>
                                <p>There are no active campaigns available for the selected ad accounts right now.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
@include('pages.settings.facebook-integration.facebook-script')
@endpush
