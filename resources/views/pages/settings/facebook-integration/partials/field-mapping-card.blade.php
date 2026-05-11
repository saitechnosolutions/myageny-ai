<style>
.fb-fields-card { background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.fb-fields-head { padding:24px 24px 18px; border-bottom:1px solid #f1f1f1; display:flex; justify-content:space-between; gap:18px; align-items:flex-start; }
.fb-fields-title { font-size:18px; font-weight:700; color:#121212; margin:0 0 6px; }
.fb-fields-copy { font-size:13px; color:#8b8b8b; margin:0; line-height:1.6; max-width:720px; }
.fb-fields-chip { display:inline-flex; align-items:center; padding:9px 12px; border-radius:999px; background:#fff6ef; color:#b45309; font-size:12px; font-weight:700; border:1px solid #ffe1c7; white-space:nowrap; }
.fb-fields-body { padding:22px 24px 24px; }
.fb-fields-grid { display:grid; gap:16px; }
.fb-fields-panel { border:1px solid #ece7ef; border-radius:14px; background:linear-gradient(180deg,#fff 0%,#fffcfa 100%); padding:18px; }
.fb-fields-panel-title { font-size:15px; font-weight:700; color:#121212; margin:0 0 6px; line-height:1.5; }
.fb-fields-panel-sub { font-size:12px; color:#8b8b8b; margin:0 0 14px; }
.fb-fields-row-list { display:grid; gap:12px; }
.fb-fields-row { display:grid; grid-template-columns:minmax(0, 1fr) 44px minmax(0, 1fr); gap:12px; align-items:center; }
.fb-fields-column-title { font-size:12px; font-weight:700; color:#6b6470; text-transform:uppercase; letter-spacing:.04em; margin:0 0 10px; }
.fb-fields-badge { display:flex; align-items:center; min-height:45px; padding:11px 12px; border:1px solid #e7e1ea; border-radius:12px; font-size:13px; color:#121212; background:#fff; }
.fb-fields-select { width:100%; padding:11px 12px; border:1px solid #e1dee3; border-radius:12px; font-size:13px; color:#121212; background:#fff; outline:none; }
.fb-fields-select:focus { border-color:#fe5f04; box-shadow:0 0 0 3px rgba(254,95,4,.10); }
.fb-fields-arrow { display:flex; align-items:center; justify-content:center; color:#b7aebd; font-size:18px; }
.fb-fields-actions { display:flex; justify-content:space-between; align-items:center; gap:14px; padding-top:18px; margin-top:18px; border-top:1px solid #f4f1f5; flex-wrap:wrap; }
.fb-fields-note { font-size:12px; color:#9a9a9a; }
.fb-fields-empty { padding:48px 20px; text-align:center; color:#8f8f8f; }
.fb-fields-empty h3 { margin:0 0 8px; font-size:17px; color:#333; }
.fb-fields-empty p { margin:0; font-size:13px; }
@media (max-width: 900px) {
    .fb-fields-row { grid-template-columns:1fr; }
    .fb-fields-arrow { display:none; }
}
@media (max-width: 768px) {
    .fb-fields-head, .fb-fields-actions { flex-direction:column; align-items:flex-start; }
    .fb-fields-head, .fb-fields-body { padding-left:18px; padding-right:18px; }
}
</style>

<div class="fb-fields-card">
    <div class="fb-fields-head">
        <div>
            <h3 class="fb-fields-title">{{ $isEditMode ? 'Edit Field Mapping' : 'Field Mapping' }}</h3>
            <p class="fb-fields-copy">
                {{ $isEditMode
                    ? 'Remap Facebook campaign fields to your CRM default fields or custom fields so future syncs save values in the correct place.'
                    : 'Map Facebook campaign fields to your CRM default fields or custom fields so synced leads are stored in the right lead columns and extra field values.' }}
            </p>
        </div>
        <div class="fb-fields-chip">{{ count($campaignMappings ?? []) }} Campaigns to Map</div>
    </div>

    <div class="fb-fields-body">
        <div class="fbmappingfields">
            @if (!empty($campaignMappings))
                <form action="">
                    <div class="fb-fields-grid">
                        @foreach ($campaignMappings as $mappingPanel)
                            <div class="fb-fields-panel fb-map-panel">
                                <h4 class="fb-fields-panel-title">
                                    {{ $isEditMode ? 'Remap Fields For' : 'Map Fields For' }} {{ $mappingPanel['campaign']->campaign_name }}
                                </h4>
                                <p class="fb-fields-panel-sub">Default CRM fields update the normal lead columns. Custom fields will be stored under your extra lead form values.</p>

                                <div class="fb-fields-row-list">
                                    <div class="fb-fields-row">
                                        <h5 class="fb-fields-column-title">Facebook Field</h5>
                                        <div></div>
                                        <h5 class="fb-fields-column-title">CRM Field</h5>
                                    </div>

                                    @foreach ($mappingPanel['rows'] as $row)
                                        <div class="fb-fields-row field-mapping-row">
                                            <div class="fb-fields-badge">{{ $row['campaign_field_name'] }}</div>
                                            <div class="fb-fields-arrow" aria-hidden="true">→</div>
                                            <div>
                                                <input type="hidden" class="campaign-field-id" value="{{ $row['campaign_field_id'] }}">
                                                <select class="crm-field-select fb-fields-select">
                                                    <option value="">Select CRM field</option>
                                                    <optgroup label="Default CRM Fields">
                                                        @foreach (($crmFieldGroups['default'] ?? []) as $crmField)
                                                            <option value="core:{{ $crmField['key'] }}" @selected($row['crm_field_key'] === 'core:' . $crmField['key'])>
                                                                {{ $crmField['label'] }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    @if (!empty($crmFieldGroups['custom']))
                                                        <optgroup label="Custom Fields">
                                                            @foreach ($crmFieldGroups['custom'] as $crmField)
                                                                <option value="{{ $crmField['key'] }}" @selected($row['crm_field_key'] === $crmField['key'])>
                                                                    {{ $crmField['label'] }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <input type="hidden" value="{{ $mappingPanel['campaign']->id }}" class="map_campaigns">
                            </div>
                        @endforeach
                    </div>

                    <div class="fb-fields-actions">
                        <div class="fb-fields-note">Map each Facebook field to either a default CRM lead column or a custom field before continuing.</div>
                        <button type="button" class="crm-btn crm-btn-primary" id="map_multiple_fields"> {{ $isEditMode ? 'Save Mapping' : 'Map Fields' }}</button>
                    </div>
                </form>
            @else
                <div class="fb-fields-empty">
                    <h3>No Campaign Fields Found</h3>
                    <p>We could not find any Facebook fields to map for the selected campaign.</p>
                </div>
            @endif
        </div>
    </div>
</div>
