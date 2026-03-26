<style>
/* ════════════════════════════════════════════
   LEAD CREATE / EDIT FORM
════════════════════════════════════════════ */
.lf-page { display:flex; flex-direction:column; height:100%; overflow:hidden; background:#f4f5f7; font-family:'Inter',sans-serif; }
.lf-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; flex-shrink:0; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:0; z-index:30; }
.lf-title  { font-size:18px; font-weight:800; color:#121212; }
.lf-crumb  { font-size:12px; color:#9e9e9e; margin-top:2px; }
.lf-crumb a { color:#fe5f04; text-decoration:none; font-weight:600; }
.lf-topbar-right { display:flex; align-items:center; gap:10px; }
.lf-btn { display:flex; align-items:center; gap:6px; padding:8px 18px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; text-decoration:none; transition:all .15s; border:none; }
.lf-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 4px 14px rgba(254,95,4,.25); }
.lf-btn-primary:hover { transform:translateY(-1px); }
.lf-btn-outline { background:#fff; color:#121212; border:1px solid #e1dee3; }
.lf-btn-outline:hover { border-color:#9e9e9e; }
.lf-body { flex:1; overflow-y:auto; padding:24px 28px 40px; }
.lf-body::-webkit-scrollbar { width:5px; }
.lf-body::-webkit-scrollbar-thumb { background:#e1dee3; border-radius:3px; }
@keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

/* Form grid */
.lf-grid { display:grid; grid-template-columns:1fr 300px; gap:20px; animation:fadeUp .35s ease both; }
.lf-left { display:flex; flex-direction:column; gap:16px; }
.lf-right { display:flex; flex-direction:column; gap:16px; }

/* Card */
.lf-card { background:#fff; border:1px solid #e1dee3; border-radius:14px; overflow:hidden; }
.lf-card-head { display:flex; align-items:center; gap:10px; padding:15px 20px; border-bottom:1px solid #f0eef2; }
.lf-card-ico { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.lf-card-title { font-size:14px; font-weight:700; color:#121212; }
.lf-card-sub   { font-size:11px; color:#9e9e9e; margin-top:2px; }
.lf-card-body  { padding:18px 20px; display:flex; flex-direction:column; gap:16px; }

/* Form row */
.lf-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.lf-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.lf-group { display:flex; flex-direction:column; gap:6px; }
.lf-group.span2 { grid-column:span 2; }
.lf-label { font-size:12px; font-weight:700; color:#2e2e2e; display:flex; align-items:center; gap:3px; }
.lf-req   { color:#dc2626; }
.lf-iw    { position:relative; }
.lf-ico   { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9e9e9e; pointer-events:none; width:13px; height:13px; }
.lf-inp, .lf-sel, .lf-ta {
    width:100%; padding:9px 12px 9px 32px;
    border:1px solid #e1dee3; border-radius:9px;
    font-size:13px; font-family:inherit; color:#121212;
    background:#fafafa; outline:none; transition:all .15s;
}
.lf-inp::placeholder, .lf-ta::placeholder { color:#b8b3aa; }
.lf-inp:focus, .lf-sel:focus, .lf-ta:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.lf-inp.err, .lf-sel.err { border-color:#dc2626; background:#fffafa; }
.lf-sel { appearance:none; -webkit-appearance:none; cursor:pointer; }
.lf-sel-caret { position:absolute; right:9px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9e9e9e; width:12px; height:12px; }
.lf-ta { resize:vertical; min-height:90px; padding:9px 12px; }
.lf-err { font-size:11px; color:#dc2626; margin-top:2px; }
.lf-hint { font-size:11px; color:#9e9e9e; margin-top:2px; }

/* Priority picker */
.lf-priority-row { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.lf-pri-opt { display:flex; flex-direction:column; align-items:center; gap:5px; padding:10px; border-radius:10px; border:1.5px solid #e1dee3; cursor:pointer; transition:all .15s; }
.lf-pri-opt input { display:none; }
.lf-pri-opt:hover { border-color:#d4cfd8; background:#fafafa; }
.lf-pri-opt.selected-low    { border-color:#16a34a; background:#f0fdf4; }
.lf-pri-opt.selected-medium { border-color:#b45309; background:#fffbeb; }
.lf-pri-opt.selected-high   { border-color:#dc2626; background:#fef2f2; }
.lf-pri-ico { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; }
.lf-pri-label { font-size:11px; font-weight:700; }

/* Status picker */
.lf-status-grid { display:grid; grid-template-columns:1fr 1fr; gap:7px; }
.lf-status-opt { display:flex; align-items:center; gap:7px; padding:8px 10px; border-radius:9px; border:1.5px solid #e1dee3; cursor:pointer; transition:all .15s; }
.lf-status-opt input { display:none; }
.lf-status-opt:hover { border-color:#d4cfd8; background:#fafafa; }
.lf-status-opt.sel-new   { border-color:#2563eb; background:#eff6ff; }
.lf-status-opt.sel-qual  { border-color:#0f766e; background:#f0fdfa; }
.lf-status-opt.sel-prop  { border-color:#7c3aed; background:#faf5ff; }
.lf-status-opt.sel-nego  { border-color:#b45309; background:#fffbeb; }
.lf-status-opt.sel-won   { border-color:#16a34a; background:#f0fdf4; }
.lf-status-opt.sel-lost  { border-color:#dc2626; background:#fef2f2; }
.lf-status-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.lf-status-name { font-size:12px; font-weight:700; }
.lf-radio-hidden { display:none; }

/* Submit bar */
.lf-submit { display:flex; gap:10px; padding:15px 20px; border-top:1px solid #f0eef2; background:#fafafa; }



</style>


