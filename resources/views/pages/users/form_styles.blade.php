<style>
/* ══════════════════════════════════════════════
   SHARED FORM STYLES — users/_form_styles.blade.php
   Include in both create.blade.php and edit.blade.php

══════════════════════════════════════════════ */
.ufrm-page { display:flex; flex-direction:column; height:100%; overflow:hidden; background:#f4f5f7; font-family:'Inter',sans-serif; }
.ufrm-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; flex-shrink:0; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:0; z-index:30; }
.ufrm-page-title { font-size:18px; font-weight:800; color:#121212; }
.ufrm-breadcrumb { font-size:12px; color:#9e9e9e; margin-top:2px; }
.ufrm-breadcrumb a { color:#fe5f04; text-decoration:none; font-weight:600; }
.ufrm-topbar-right { display:flex; align-items:center; gap:10px; }
.ufrm-btn { display:flex; align-items:center; gap:6px; padding:8px 18px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; text-decoration:none; transition:all .15s; border:none; }
.ufrm-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 4px 14px rgba(254,95,4,.25); }
.ufrm-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(254,95,4,.35); }
.ufrm-btn-outline { background:#fff; color:#121212; border:1px solid #e1dee3; }
.ufrm-btn-outline:hover { border-color:#9e9e9e; }
.ufrm-body { flex:1; overflow-y:auto; padding:24px 28px 40px; }
.ufrm-body::-webkit-scrollbar { width:5px; }
.ufrm-body::-webkit-scrollbar-thumb { background:#e1dee3; border-radius:3px; }
@keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
.ufrm-layout { display:grid; grid-template-columns:1fr 280px; gap:20px; max-width:1000px; animation:fadeUp .35s ease both; }
.ufrm-card { background:#fff; border:1px solid #e1dee3; border-radius:14px; overflow:hidden; }
.ufrm-card-head { padding:16px 22px; border-bottom:1px solid #f0eef2; display:flex; align-items:center; gap:10px; }
.ufrm-card-head-icon { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; }
.ufrm-card-title { font-size:14px; font-weight:700; color:#121212; }
.ufrm-card-sub { font-size:11px; color:#9e9e9e; margin-top:2px; }
.ufrm-card-body { padding:20px 22px; display:flex; flex-direction:column; gap:18px; }
.ufrm-form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.ufrm-form-group { display:flex; flex-direction:column; gap:6px; }
.ufrm-form-group.full { grid-column:span 2; }
.ufrm-label { font-size:12px; font-weight:700; color:#2e2e2e; display:flex; align-items:center; gap:4px; }
.ufrm-required { color:#dc2626; }
.ufrm-input-wrap { position:relative; }
.ufrm-input-ico { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9e9e9e; pointer-events:none; width:14px; height:14px; }
.ufrm-input, .ufrm-select, .ufrm-textarea { width:100%; padding:9px 12px 9px 34px; border:1px solid #e1dee3; border-radius:9px; font-size:13px; font-family:inherit; color:#121212; background:#fafafa; outline:none; transition:all .15s; }
.ufrm-input::placeholder { color:#b8b3aa; }
.ufrm-input:focus, .ufrm-select:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.ufrm-input.is-invalid, .ufrm-select.is-invalid { border-color:#dc2626; background:#fffafa; }
.ufrm-select { appearance:none; -webkit-appearance:none; cursor:pointer; }
.ufrm-select-caret { position:absolute; right:10px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9e9e9e; width:13px; height:13px; }
.ufrm-error { font-size:11px; color:#dc2626; display:flex; align-items:center; gap:4px; margin-top:3px; }
.ufrm-pw-wrap { position:relative; }
.ufrm-pw-toggle { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9e9e9e; padding:0; transition:color .15s; }
.ufrm-pw-toggle:hover { color:#fe5f04; }
.ufrm-pw-strength { margin-top:6px; }
.ufrm-pw-bars { display:flex; gap:3px; margin-bottom:4px; }
.ufrm-pw-bar { flex:1; height:3px; border-radius:2px; background:#f0eef2; transition:background .3s; }
.ufrm-pw-label { font-size:10px; font-weight:700; color:#9e9e9e; }
.ufrm-avatar-area { display:flex; flex-direction:column; align-items:center; gap:12px; padding:16px; }
.ufrm-avatar-preview { width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg,#fe5f04,#ff7c30); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#fff; position:relative; overflow:hidden; cursor:pointer; border:3px solid #fff; box-shadow:0 4px 16px rgba(254,95,4,.25); }
.ufrm-avatar-overlay { position:absolute; inset:0; background:rgba(0,0,0,.4); border-radius:50%; display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .2s; }
.ufrm-avatar-preview:hover .ufrm-avatar-overlay { opacity:1; }
.ufrm-avatar-upload-btn { padding:7px 14px; border-radius:8px; border:1px solid #e1dee3; font-size:12px; font-weight:600; color:#7c7c7c; cursor:pointer; background:#fafafa; transition:all .15s; }
.ufrm-avatar-upload-btn:hover { border-color:#fe5f04; color:#fe5f04; }
.ufrm-avatar-remove { font-size:11px; color:#dc2626; cursor:pointer; font-weight:600; background:none; border:none; font-family:inherit; }
.ufrm-toggle-row { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-radius:10px; background:#fafafa; border:1px solid #f0eef2; }
.ufrm-toggle-label { font-size:13px; font-weight:600; color:#121212; }
.ufrm-toggle-sub { font-size:11px; color:#9e9e9e; margin-top:2px; }
.ufrm-toggle { position:relative; width:44px; height:24px; flex-shrink:0; }
.ufrm-toggle input { display:none; }
.ufrm-toggle-slider { position:absolute; inset:0; border-radius:24px; background:#e1dee3; cursor:pointer; transition:background .2s; }
.ufrm-toggle-slider::before { content:''; position:absolute; width:18px; height:18px; border-radius:50%; background:#fff; top:3px; left:3px; transition:transform .2s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
.ufrm-toggle input:checked + .ufrm-toggle-slider { background:#16a34a; }
.ufrm-toggle input:checked + .ufrm-toggle-slider::before { transform:translateX(20px); }
.ufrm-role-grid { display:flex; flex-direction:column; gap:8px; }
.ufrm-role-option { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; border:1.5px solid #e1dee3; cursor:pointer; transition:all .15s; }
.ufrm-role-option:hover { border-color:#d0cdd4; background:#fafafa; }
.ufrm-role-option.selected { border-color:#fe5f04; background:#fff7ed; }
.ufrm-role-option input[type="radio"] { display:none; }
.ufrm-role-radio { width:16px; height:16px; border-radius:50%; border:2px solid #e1dee3; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .15s; }
.ufrm-role-option.selected .ufrm-role-radio { border-color:#fe5f04; background:#fe5f04; }
.ufrm-role-option.selected .ufrm-role-radio::after { content:''; width:6px; height:6px; border-radius:50%; background:#fff; display:block; }
.ufrm-role-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.ufrm-role-name { font-size:12px; font-weight:700; color:#121212; }
.ufrm-role-desc { font-size:10px; color:#9e9e9e; margin-top:1px; }
.ufrm-submit-area { display:flex; gap:10px; padding:16px 22px; border-top:1px solid #f0eef2; background:#fafafa; }
</style>
