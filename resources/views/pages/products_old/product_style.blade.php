<style>
/* ══════════════════════════════════════════════════
   PRODUCTS INDEX — Clean catalog-style table
   Theme: White + orange, structured, data-dense
══════════════════════════════════════════════════ */
.prd-page { display:flex; flex-direction:column; height:100%; overflow:hidden; background:#f4f5f7; font-family:'Inter',sans-serif; }

/* Topbar */
.prd-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; flex-shrink:0; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:0; z-index:30; }
.prd-title { font-size:18px; font-weight:800; color:#121212; }
.prd-crumb { font-size:12px; color:#9e9e9e; margin-top:2px; }
.prd-topbar-right { display:flex; align-items:center; gap:10px; }
.prd-btn { display:flex; align-items:center; gap:6px; padding:8px 18px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; text-decoration:none; transition:all .15s; border:none; }
.prd-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 4px 14px rgba(254,95,4,.25); }
.prd-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(254,95,4,.35); }
.prd-btn-outline { background:#fff; color:#121212; border:1px solid #e1dee3; }
.prd-btn-outline:hover { border-color:#fe5f04; color:#fe5f04; }

/* Filter bar */
.prd-filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding:10px 28px; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:60px; z-index:25; }
.prd-search-wrap { position:relative; flex:1; min-width:200px; max-width:300px; }
.prd-search-ico { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9e9e9e; width:13px; height:13px; pointer-events:none; }
.prd-search-inp { width:100%; padding:8px 12px 8px 32px; border:1px solid #e1dee3; border-radius:8px; font-size:12px; font-family:inherit; outline:none; background:#f8f8f8; color:#121212; transition:all .15s; }
.prd-search-inp:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.prd-fw { position:relative; }
.prd-fi { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9e9e9e; pointer-events:none; width:12px; height:12px; }
.prd-sel { appearance:none; -webkit-appearance:none; padding:7px 26px 7px 27px; background:#f8f8f8; border:1px solid #e1dee3; border-radius:8px; font-size:12px; font-weight:600; color:#2e2e2e; cursor:pointer; outline:none; transition:all .15s; font-family:inherit; min-width:130px; }
.prd-sel:focus { border-color:#fe5f04; background:#fff; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
.prd-fc { position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9e9e9e; width:11px; height:11px; }
.prd-reset { display:flex; align-items:center; gap:5px; padding:7px 12px; border-radius:8px; background:none; border:1px solid #e1dee3; font-size:12px; font-weight:600; color:#9e9e9e; cursor:pointer; font-family:inherit; transition:all .15s; text-decoration:none; }
.prd-reset:hover { border-color:#dc2626; color:#dc2626; }

/* Body */
.prd-body { flex:1; overflow-y:auto; padding:18px 28px 32px; display:flex; flex-direction:column; gap:14px; }
.prd-body::-webkit-scrollbar { width:5px; }
.prd-body::-webkit-scrollbar-thumb { background:#e1dee3; border-radius:3px; }

@keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

/* Stats */
.prd-stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; animation:fadeUp .35s ease both; }
.prd-stat { background:#fff; border:1px solid #e1dee3; border-radius:11px; padding:13px 15px; transition:box-shadow .2s; }
.prd-stat:hover { box-shadow:0 4px 16px rgba(0,0,0,.06); }
.prd-stat-icon { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; margin-bottom:8px; }
.prd-stat-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#9e9e9e; }
.prd-stat-value { font-size:22px; font-weight:800; color:#121212; line-height:1; margin-top:3px; }

/* Table card */
.prd-table-card { background:#fff; border:1px solid #e1dee3; border-radius:13px; overflow:hidden; animation:fadeUp .35s .1s ease both; }
.prd-table-top { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid #f0eef2; }
.prd-table-title { font-size:14px; font-weight:700; color:#121212; }
.prd-table-sub { font-size:11px; color:#9e9e9e; margin-top:2px; }
.prd-results { font-size:12px; color:#9e9e9e; }
.prd-results strong { color:#121212; font-weight:700; }

/* Table */
.prd-tbl { width:100%; border-collapse:collapse; }
.prd-tbl th { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#9e9e9e; padding:9px 14px; text-align:left; border-bottom:1px solid #f0eef2; background:#fafafa; white-space:nowrap; }
.prd-tbl td { padding:13px 14px; font-size:13px; color:#121212; border-bottom:1px solid #f7f6f9; vertical-align:middle; }
.prd-tbl tbody tr:last-child td { border-bottom:none; }
.prd-tbl tbody tr:hover td { background:#fdf9f6; }

/* Product name cell */
.prd-name-cell { display:flex; align-items:center; gap:10px; }
.prd-avatar { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:800; color:#fff; flex-shrink:0; }
.prd-pname { font-size:13px; font-weight:700; color:#121212; }
.prd-pcode { font-size:10px; color:#9e9e9e; font-family:monospace; margin-top:1px; }

/* Price cells */
.prd-rate { font-size:14px; font-weight:800; color:#121212; }
.prd-rate-gst { font-size:12px; color:#16a34a; font-weight:600; }

/* GST badge */
.prd-gst-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.prd-gst-0  { background:#f5f4f6; color:#7c7c7c; border:1px solid #e1dee3; }
.prd-gst-3  { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.prd-gst-5  { background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4; }
.prd-gst-12 { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
.prd-gst-18 { background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; }
.prd-gst-28 { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }

/* Unit pill */
.prd-unit { display:inline-flex; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600; background:#f5f4f6; color:#555; border:1px solid #e1dee3; }

/* Category */
.prd-cat { font-size:12px; color:#7c7c7c; }

/* Toggle */
.prd-toggle { position:relative; width:36px; height:20px; }
.prd-toggle input { display:none; }
.prd-toggle-sl { position:absolute; inset:0; border-radius:20px; background:#e1dee3; cursor:pointer; transition:background .2s; }
.prd-toggle-sl::before { content:''; position:absolute; width:14px; height:14px; border-radius:50%; background:#fff; top:3px; left:3px; transition:transform .2s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
.prd-toggle input:checked + .prd-toggle-sl { background:#16a34a; }
.prd-toggle input:checked + .prd-toggle-sl::before { transform:translateX(16px); }

/* Actions */
.prd-actions { display:flex; align-items:center; gap:5px; }
.prd-act-btn { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; border:1px solid #e1dee3; background:#fafafa; cursor:pointer; color:#9e9e9e; transition:all .15s; text-decoration:none; }
.prd-act-btn:hover { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
.prd-act-btn.edit:hover { border-color:#fe5f04; color:#fe5f04; background:#fff7ed; }
.prd-act-btn.del:hover { border-color:#dc2626; color:#dc2626; background:#fef2f2; }

/* Pagination */
.prd-pag { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-top:1px solid #f0eef2; }
.prd-pag-info { font-size:12px; color:#9e9e9e; }
.prd-pag-links { display:flex; gap:4px; }
.prd-pag-link { padding:5px 10px; border-radius:7px; font-size:12px; font-weight:600; text-decoration:none; color:#7c7c7c; border:1px solid #e1dee3; transition:all .15s; }
.prd-pag-link:hover, .prd-pag-link.active { background:#fe5f04; color:#fff; border-color:#fe5f04; }
.prd-pag-link.disabled { opacity:.4; pointer-events:none; }

/* Empty */
.prd-empty { text-align:center; padding:56px 20px; color:#9e9e9e; }
.prd-empty-ico { font-size:44px; margin-bottom:12px; }
.prd-empty-title { font-size:16px; font-weight:700; color:#7c7c7c; margin-bottom:6px; }

/* Alert */
.prd-alert { padding:12px 16px; border-radius:10px; font-size:13px; display:flex; align-items:center; gap:10px; }
.prd-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.prd-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

/* Delete modal */
.prd-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
.prd-modal-overlay.open { display:flex; }
.prd-modal { background:#fff; border-radius:16px; padding:28px; max-width:380px; width:90%; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:popIn .2s ease; }
@keyframes popIn { from { opacity:0; transform:scale(.94) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
.prd-modal-icon { width:52px; height:52px; border-radius:14px; background:#fef2f2; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.prd-modal-title { font-size:17px; font-weight:800; color:#121212; text-align:center; margin-bottom:8px; }
.prd-modal-sub { font-size:13px; color:#7c7c7c; text-align:center; line-height:1.6; margin-bottom:22px; }
.prd-modal-btns { display:flex; gap:10px; }
.prd-modal-btn { flex:1; padding:10px; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; border:none; transition:all .15s; }
.prd-modal-cancel { background:#f5f4f6; color:#7c7c7c; border:1px solid #e1dee3; }
.prd-modal-delete { background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; box-shadow:0 4px 12px rgba(220,38,38,.3); }

.pshow-page { display:flex; flex-direction:column; height:100%; overflow:hidden; background:#f4f5f7; font-family:'Inter',sans-serif; }
.pshow-topbar { display:flex; align-items:center; justify-content:space-between; padding:0 28px; height:60px; flex-shrink:0; background:#fff; border-bottom:1px solid #e1dee3; position:sticky; top:0; z-index:30; }
.pshow-title { font-size:18px; font-weight:800; color:#121212; }
.pshow-crumb { font-size:12px; color:#9e9e9e; margin-top:2px; }
.pshow-crumb a { color:#fe5f04; text-decoration:none; font-weight:600; }
.pshow-topbar-right { display:flex; align-items:center; gap:10px; }
.pshow-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; text-decoration:none; transition:all .15s; border:none; }
.pshow-btn-primary { background:linear-gradient(135deg,#fe5f04,#ff7c30); color:#fff; box-shadow:0 4px 12px rgba(254,95,4,.25); }
.pshow-btn-primary:hover { transform:translateY(-1px); }
.pshow-btn-outline { background:#fff; color:#121212; border:1px solid #e1dee3; }
.pshow-btn-outline:hover { border-color:#9e9e9e; }
.pshow-btn-danger  { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.pshow-body { flex:1; overflow-y:auto; padding:22px 28px 40px; }
.pshow-body::-webkit-scrollbar { width:5px; }
.pshow-body::-webkit-scrollbar-thumb { background:#e1dee3; border-radius:3px; }
@keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

/* Hero */
.pshow-hero { background:#fff; border:1px solid #e1dee3; border-radius:14px; padding:24px; display:flex; align-items:center; gap:20px; margin-bottom:20px; animation:fadeUp .35s ease; }
.pshow-hero-avatar { width:64px; height:64px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:900; color:#fff; flex-shrink:0; }
.pshow-hero-name { font-size:22px; font-weight:800; color:#121212; }
.pshow-hero-code { font-size:12px; font-family:monospace; color:#9e9e9e; margin-top:4px; }
.pshow-hero-right { margin-left:auto; display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }

/* Grid */
.pshow-grid { display:grid; grid-template-columns:1fr 300px; gap:18px; animation:fadeUp .35s .1s ease both; }
.pshow-left { display:flex; flex-direction:column; gap:16px; }
.pshow-right { display:flex; flex-direction:column; gap:16px; }

/* Card */
.pshow-card { background:#fff; border:1px solid #e1dee3; border-radius:13px; overflow:hidden; }
.pshow-card-head { display:flex; justify-content:space-between; align-items:center; padding:13px 18px; border-bottom:1px solid #f0eef2; }
.pshow-card-title { font-size:14px; font-weight:700; color:#121212; display:flex; align-items:center; gap:7px; }
.pshow-card-body  { padding:16px 18px; }

/* Info grid */
.pshow-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.pshow-info-item .pil { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#9e9e9e; margin-bottom:4px; }
.pshow-info-item .piv { font-size:13px; font-weight:600; color:#121212; }
.pshow-info-item .piv.muted { color:#9e9e9e; font-weight:400; }

/* Price breakdown */
.pshow-price-card { background:#fff; border:1px solid #e1dee3; border-radius:13px; overflow:hidden; }
.pshow-price-head { padding:13px 18px; border-bottom:1px solid #f0eef2; font-size:14px; font-weight:700; color:#121212; }
.pshow-price-body { padding:16px 18px; display:flex; flex-direction:column; gap:10px; }
.pshow-price-row  { display:flex; justify-content:space-between; align-items:center; }
.pshow-price-lbl  { font-size:12px; color:#9e9e9e; }
.pshow-price-val  { font-size:14px; font-weight:700; color:#121212; }
.pshow-price-row.grand { border-top:1px solid #f0eef2; padding-top:10px; margin-top:4px; }
.pshow-price-row.grand .pshow-price-val { font-size:22px; font-weight:800; color:#fe5f04; }

/* GST badge large */
.pshow-gst-big { display:flex; align-items:center; justify-content:center; padding:16px; border-radius:12px; font-size:22px; font-weight:800; margin-bottom:10px; }

/* Alert */
.pshow-alert { padding:12px 16px; border-radius:10px; font-size:13px; display:flex; align-items:center; gap:10px; margin-bottom:14px; }
.pshow-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }

</style>
