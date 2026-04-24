@php
    $fields = App\Models\Field::all();
    $fbfields = App\Models\CampaignFields::where('campaign_id', $camid)->get();
    $campaignMaster = App\Models\CampaignMaster::where('id', $camid)->first();
@endphp

<style>
.fb-fields-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.fb-fields-head { padding:24px 24px 18px; border-bottom:1px solid #f1f1f1; display:flex; justify-content:space-between; gap:18px; align-items:flex-start; }
.fb-fields-title { font-size:18px; font-weight:700; color:#121212; margin:0 0 6px; }
.fb-fields-copy { font-size:13px; color:#8b8b8b; margin:0; line-height:1.6; max-width:720px; }
.fb-fields-chip { display:inline-flex; align-items:center; padding:9px 12px; border-radius:999px; background:#fff6ef; color:#b45309; font-size:12px; font-weight:700; border:1px solid #ffe1c7; white-space:nowrap; }
.fb-fields-body { padding:22px 24px 24px; }
.fb-fields-panel { border:1px solid #ece7ef; border-radius:14px; background:linear-gradient(180deg,#fff 0%,#fffcfa 100%); padding:18px; }
.fb-fields-panel-title { font-size:15px; font-weight:700; color:#121212; margin:0 0 6px; line-height:1.5; }
.fb-fields-panel-sub { font-size:12px; color:#8b8b8b; margin:0 0 14px; }
.fb-fields-map-grid { display:grid; grid-template-columns:minmax(0, 1fr) 44px minmax(0, 1fr); gap:12px; align-items:start; }
.fb-fields-column-title { font-size:12px; font-weight:700; color:#6b6470; text-transform:uppercase; letter-spacing:.04em; margin:0 0 10px; }
.fb-fields-select-list { display:grid; gap:10px; }
.fb-fields-select { width:100%; padding:11px 12px; border:1px solid #e1dee3; border-radius:12px; font-size:13px; color:#121212; background:#fff; outline:none; }
.fb-fields-select:focus { border-color:#fe5f04; box-shadow:0 0 0 3px rgba(254,95,4,.10); }
.fb-fields-arrow-col { display:flex; flex-direction:column; gap:10px; padding-top:30px; }
.fb-fields-arrow { height:45px; display:flex; align-items:center; justify-content:center; color:#b7aebd; font-size:18px; }
.fb-fields-actions { display:flex; justify-content:space-between; align-items:center; gap:14px; padding-top:18px; margin-top:18px; border-top:1px solid #f4f1f5; flex-wrap:wrap; }
.fb-fields-note { font-size:12px; color:#9a9a9a; }
.fb-fields-empty { padding:48px 20px; text-align:center; color:#8f8f8f; }
.fb-fields-empty h3 { margin:0 0 8px; font-size:17px; color:#333; }
.fb-fields-empty p { margin:0; font-size:13px; }
@media (max-width: 900px) {
    .fb-fields-map-grid { grid-template-columns:1fr; }
    .fb-fields-arrow-col { display:none; }
}
@media (max-width: 768px) {
    .fb-fields-head, .fb-fields-actions { flex-direction:column; align-items:flex-start; }
    .fb-fields-head, .fb-fields-body { padding-left:18px; padding-right:18px; }
}
</style>

<div class="fb-fields-card">
    <div class="fb-fields-head">
        <div>
            <h3 class="fb-fields-title">Edit Field Mapping</h3>
            <p class="fb-fields-copy">Remap the Facebook campaign fields to your CRM fields so new or updated lead data continues to sync correctly.</p>
        </div>
        <div class="fb-fields-chip">1 Campaign to Remap</div>
    </div>

    <div class="fb-fields-body">
        <div class="fbmappingfields">
            @if (isset($camid))
                <form action="">
                    <div class="fb-fields-panel">
                        <h4 class="fb-fields-panel-title">Remap Fields For {{ $campaignMaster->campaign_name }}</h4>
                        <p class="fb-fields-panel-sub">Select the updated Facebook field and CRM field pairs for this campaign before saving your changes.</p>

                        <div class="fb-fields-map-grid">
                            <div>
                                <h5 class="fb-fields-column-title">Campaign Fields</h5>
                                <div class="fb-fields-select-list">
                                    @foreach ($fbfields as $field)
                                        <select class="campaign-field-select fb-fields-select">
                                            <option selected>Select the field</option>
                                            @foreach ($fbfields as $item)
                                                <option value="{{ $item->id }}">{{ $item->field_name }}</option>
                                            @endforeach
                                        </select>
                                    @endforeach
                                </div>
                            </div>

                            <div class="fb-fields-arrow-col" aria-hidden="true">
                                @foreach ($fbfields as $field)
                                    <div class="fb-fields-arrow">→</div>
                                @endforeach
                            </div>

                            <div>
                                <h5 class="fb-fields-column-title">CRM Fields</h5>
                                <div class="fb-fields-select-list">
                                    @foreach ($fbfields as $field)
                                        <select class="crm-field-select fb-fields-select">
                                            <option selected>Select the field</option>
                                            @foreach ($fields as $crmField)
                                                <option value="{{ $crmField->id }}">{{ $crmField->fieldname }}</option>
                                            @endforeach
                                        </select>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <input type="hidden" value="{{ $campaignMaster->id }}" class="map_campaigns">
                    </div>

                    <div class="fb-fields-actions">
                        <div class="fb-fields-note">Complete all field pairs before saving the updated mapping.</div>
                        <div>
                            <input type="hidden" value="{{ $campaignMaster->camp_id }}" id="map_ad_id">
                            <input type="hidden" value="{{ $campaignMaster->access_token }}" id="map_access_token">
                            <input type="hidden" value="{{ $campaignMaster->id }}" id="map_campaign_id">
                            <button type="button" class="crm-btn crm-btn-primary" id="map_multiple_fields" disabled>Save Mapping</button>
                        </div>
                    </div>
                </form>
            @else
                <div class="fb-fields-empty">
                    <h3>Campaign Integrated Successfully</h3>
                    <p>The selected campaign has already been integrated and no additional remapping is required right now.</p>
                </div>
            @endif
        </div>
    </div>
</div>
