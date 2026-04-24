@php
    $campaigns = App\Models\CampaignMaster::where('status', 1)
        ->where('is_integrated', 1)
        ->get();
@endphp

<style>
.fb-integrate-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.fb-integrate-head { padding:24px 24px 18px; border-bottom:1px solid #f1f1f1; display:flex; justify-content:space-between; gap:18px; align-items:flex-start; }
.fb-integrate-title { font-size:18px; font-weight:700; color:#121212; margin:0 0 6px; }
.fb-integrate-copy { font-size:13px; color:#8b8b8b; margin:0; line-height:1.6; max-width:700px; }
.fb-integrate-chip { display:inline-flex; align-items:center; gap:8px; padding:9px 12px; border-radius:999px; background:#fff6ef; color:#b45309; font-size:12px; font-weight:700; border:1px solid #ffe1c7; white-space:nowrap; }
.fb-integrate-body { padding:22px 24px 24px; }
.fb-integrate-banner { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:18px; border:1px solid #ece7ef; border-radius:14px; background:linear-gradient(180deg,#fff 0%,#fffcfa 100%); margin-bottom:18px; flex-wrap:wrap; }
.fb-integrate-banner-text h4 { margin:0 0 4px; font-size:16px; font-weight:700; color:#121212; }
.fb-integrate-banner-text p { margin:0; font-size:13px; color:#8b8b8b; line-height:1.6; }
.fb-integrate-grid { display:grid; gap:14px; }
.fb-integrate-item { display:grid; grid-template-columns:minmax(0, 1.4fr) minmax(0, 1fr) auto; gap:14px; align-items:center; padding:16px 18px; border:1px solid #ece7ef; border-radius:14px; background:#fff; }
.fb-integrate-name { font-size:15px; font-weight:700; color:#121212; margin:0 0 5px; }
.fb-integrate-note { font-size:12px; color:#b45309; margin:0; line-height:1.5; }
.fb-integrate-meta { font-size:13px; color:#6b6470; font-weight:600; }
.fb-integrate-status { display:inline-flex; align-items:center; padding:5px 10px; border-radius:999px; font-size:11px; font-weight:700; border:1px solid transparent; }
.fb-integrate-status.active { background:#ecfdf3; color:#166534; border-color:#bbf7d0; }
.fb-integrate-status.inactive { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
.fb-integrate-actions { display:flex; gap:8px; align-items:center; }
.fb-integrate-icon { width:34px; height:34px; border:none; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; cursor:pointer; transition:all .15s ease; }
.fb-integrate-icon:hover { background:#f7f4f9; }
.fb-integrate-icon.delete:hover { background:#fff1f2; color:#be123c; }
.fb-integrate-empty { padding:48px 20px; text-align:center; color:#8f8f8f; }
.fb-integrate-empty h3 { margin:0 0 8px; font-size:17px; color:#333; }
.fb-integrate-empty p { margin:0; font-size:13px; }
@media (max-width: 900px) {
    .fb-integrate-item { grid-template-columns:1fr; }
    .fb-integrate-actions { justify-content:flex-start; }
}
@media (max-width: 768px) {
    .fb-integrate-head, .fb-integrate-banner { flex-direction:column; align-items:flex-start; }
    .fb-integrate-head, .fb-integrate-body { padding-left:18px; padding-right:18px; }
}
</style>

<div class="fb-integrate-card">
    <div class="fb-integrate-head">
        <div>
            <h3 class="fb-integrate-title">Facebook Campaign Integration</h3>
            <p class="fb-integrate-copy">Review your connected Facebook campaigns, remove old integrations, or remap campaigns where new lead fields were detected.</p>
        </div>
        <div class="fb-integrate-chip">{{ $campaigns->count() }} Integrated Campaigns</div>
    </div>

    <div class="fb-integrate-body">
        <div class="fbloginaccess">
            <div class="fb-integrate-banner">
                <div class="fb-integrate-banner-text">
                    <h4>Connect a New Facebook Account</h4>
                    <p>Login with Facebook to fetch ad accounts, choose campaigns, and map incoming lead assignments into your CRM.</p>
                </div>
                <a href="/settings/auth/facebook" class="crm-btn crm-btn-primary">Login With Facebook</a>
            </div>

            @if ($campaigns->count())
                <div class="fb-integrate-grid">
                    @foreach ($campaigns as $camp)
                        <div class="fb-integrate-item">
                            <div>
                                <h4 class="fb-integrate-name">{{ $camp->campaign_name }}</h4>
                                @if ($camp->new_fields == 1)
                                    <p class="fb-integrate-note">New fields were detected for this campaign. Please remap the fields to keep lead syncing correct.</p>
                                @endif
                            </div>

                            <div>
                                <div class="fb-integrate-meta">{{ $camp->camp_id }}</div>
                            </div>

                            <div class="fb-integrate-actions">
                                <span class="fb-integrate-status {{ $camp->status == 1 ? 'active' : 'inactive' }}">
                                    {{ $camp->status == 1 ? 'Active' : 'Inactive' }}
                                </span>

                                @if ($camp->new_fields == 1)
                                    <button class="fb-integrate-icon editintegratedcamp" data-camp_id="{{ $camp->id }}" title="Remap Fields">✏️</button>
                                @endif

                                <button class="fb-integrate-icon delete deleteintegratedcamp" data-camp_id="{{ $camp->id }}" title="Delete Integration">🗑️</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="fb-integrate-empty">
                    <h3>No Integrated Campaigns Yet</h3>
                    <p>Login with Facebook to connect your first ad account and campaign.</p>
                </div>
            @endif
        </div>

        <div class="man"></div>
    </div>
</div>
