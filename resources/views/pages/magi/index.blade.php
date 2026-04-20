<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Magi — Smart Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
/* ═══════════════════════════════════════════
   myAgenci.ai BRAND TOKENS
   Primary: #FF5A00 (Orange)
   Dark:    #1A1A1A (Charcoal)
   Mid:     #2C2C2C
   Light:   #F5F5F5
═══════════════════════════════════════════ */
:root{
  /* --bg:#F4F4F2; */
  --bg:#ffffff;
  --bg2:#FFFFFF;
  --bg3:#ffffff;
  --surface:#FFFFFF;
  --surface2:#EDECEA;
  --border:rgba(26,26,26,.1);
  --border2:rgba(26,26,26,.06);
  --text:#1A1A1A;
  --text2:#444444;
  --text3:#888888;

  /* myAgenci.ai Brand Palette */
  --a1:#FF5A00;         /* orange — primary */
  --a2:#CC4700;         /* deep orange */
  --a3:#1A8A3A;         /* green */
  --a4:#D97706;         /* amber */
  --a5:#DC2626;         /* red */
  --a6:#0891B2;         /* cyan */
  --a7:#7C3AED;         /* violet */
  --a8:#15803D;         /* emerald */
  --a1-soft:rgba(255,90,0,.08);
  --a1-mid:rgba(255,90,0,.18);

  --grd1:linear-gradient(135deg,#FF5A00,#CC3300);
  --grd2:linear-gradient(135deg,#FF5A00,#FF8C40);
  --grd3:linear-gradient(135deg,#1A1A1A,#3C3C3C);

  --sh:0 1px 3px rgba(26,26,26,.05),0 4px 12px rgba(26,26,26,.06);
  --shh:0 4px 20px rgba(26,26,26,.12),0 1px 4px rgba(26,26,26,.07);
  --r:12px;--r2:8px;

  /* ── FONT STACK (updated) ── */
  --f1:'Outfit',sans-serif;          /* was Syne — headings, brand, labels */
  --f2:'Inter',sans-serif;           /* was DM Sans — body, UI prose */
  --f3:'IBM Plex Mono',monospace;    /* was JetBrains Mono — code, numbers */

  --tr:all .18s cubic-bezier(.4,0,.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--f2);background:var(--bg);color:var(--text);min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:var(--bg3)}
::-webkit-scrollbar-thumb{background:rgba(255,90,0,.25);border-radius:4px}
::-webkit-scrollbar-thumb:hover{background:rgba(255,90,0,.45)}

/* ═══════ HEADER ═══════ */
#hdr{
  position:sticky;top:0;z-index:600;height:58px;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 22px;gap:12px;
  background:rgba(255,255,255,.97);
  backdrop-filter:blur(20px);
  border-bottom:2px solid #FF5A00;
  box-shadow:0 2px 16px rgba(255,90,0,.08);
}
.hdr-brand{display:flex;align-items:center;gap:10px;flex-shrink:0}
.hdr-mark{
  width:36px;height:36px;border-radius:8px;background:var(--grd1);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--f1);font-size:20px;font-weight:800;color:#fff;
  box-shadow:0 4px 12px rgba(255,90,0,.35);letter-spacing:-1px;
  position:relative;overflow:hidden;
}
.hdr-mark::after{content:'';position:absolute;top:-8px;right:-8px;width:24px;height:24px;background:rgba(255,255,255,.15);border-radius:50%}
.hdr-brand-name{font-family:var(--f1);font-size:17px;font-weight:800;color:var(--text);letter-spacing:-.3px}
.hdr-brand-name em{color:#FF5A00;font-style:normal}
.hdr-brand-sub{font-family:var(--f3);font-size:8.5px;color:var(--text3);letter-spacing:1.8px;text-transform:uppercase;margin-top:1px}
.hdr-actions{padding:22px;top:-10px;display:flex;align-items:center;gap:6px;flex-shrink:0;justify-content:end}
.hpill{font-family:var(--f3);font-size:9px;font-weight:600;letter-spacing:.8px;padding:3px 9px;border-radius:20px;display:none;align-items:center;gap:5px}
.hpill.live{background:rgba(26,138,58,.1);color:#1A8A3A;border:1px solid rgba(26,138,58,.2)}
.hpill.live .sdot{width:5px;height:5px;border-radius:50%;background:#1A8A3A;animation:blink 2s infinite}
.hpill.show{display:inline-flex}
.hbtn{
  font-family:var(--f1);font-size:10px;font-weight:700;
  padding:6px 13px;border-radius:7px;cursor:pointer;
  border:1.5px solid var(--border);background:var(--bg3);color:var(--text2);
  transition:var(--tr);display:none;align-items:center;gap:5px;letter-spacing:.1px;
}
.hbtn.show{display:inline-flex}
.hbtn:hover{background:var(--a1-soft);color:var(--a1);border-color:var(--a1-mid)}
.hbtn.primary-btn{background:var(--grd1);color:#fff;border-color:transparent;box-shadow:0 4px 12px rgba(255,90,0,.28)}
.hbtn.primary-btn:hover{opacity:.9;transform:translateY(-1px)}
.hbtn.danger{background:rgba(220,38,38,.06);border-color:rgba(220,38,38,.18);color:var(--a5)}
.hbtn.danger:hover{background:rgba(220,38,38,.12)}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(255,90,0,.3)}50%{box-shadow:0 0 0 8px rgba(255,90,0,0)}}

/* ═══════ PDF OVERLAY ═══════ */
#pdf-overlay{
  display:none;position:fixed;inset:0;z-index:9999;
  background:rgba(26,26,26,.7);backdrop-filter:blur(12px);
  align-items:center;justify-content:center;flex-direction:column;gap:14px;
}
#pdf-overlay.show{display:flex}
.pdf-ring{width:48px;height:48px;border:3px solid rgba(255,90,0,.15);border-top-color:#FF5A00;border-radius:50%;animation:spin .8s linear infinite}
.pdf-lbl{font-family:var(--f1);font-size:14px;color:#fff;font-weight:700;letter-spacing:-.1px}

/* ═══════ SHEET MODAL ═══════ */
#sheet-modal{
  display:none;position:fixed;inset:0;z-index:800;
  background:rgba(26,26,26,.55);backdrop-filter:blur(8px);
  align-items:center;justify-content:center;padding:20px;
}
#sheet-modal.show{display:flex}
.sm-box{
  background:var(--surface);border-radius:18px;padding:28px;
  max-width:600px;width:100%;
  box-shadow:0 24px 64px rgba(26,26,26,.18);
  border:1px solid var(--border);
  border-top:3px solid #FF5A00;
}
.sm-title{font-family:var(--f1);font-size:18px;font-weight:800;color:var(--text);margin-bottom:5px}
.sm-sub{font-size:12px;color:var(--text3);margin-bottom:22px}
.sm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px;max-height:360px;overflow-y:auto;margin-bottom:18px}
.sm-card{background:var(--bg);border:1.5px solid var(--border);border-radius:10px;padding:14px;cursor:pointer;transition:var(--tr)}
.sm-card:hover{border-color:#FF5A00;background:var(--a1-soft);transform:translateY(-2px)}
.sm-card.sel{border-color:#FF5A00;background:var(--a1-soft)}
.sm-sheet{font-family:var(--f1);font-size:13px;font-weight:700;color:var(--text);margin-bottom:4px}
.sm-meta{font-size:10px;color:var(--text3);font-family:var(--f3)}
.sm-actions{display:flex;gap:8px;justify-content:flex-end}
.sm-btn{font-family:var(--f1);font-size:13px;font-weight:700;padding:9px 22px;border-radius:9px;cursor:pointer;border:none;transition:var(--tr)}
.sm-btn.primary{background:var(--grd1);color:#fff;box-shadow:0 4px 14px rgba(255,90,0,.28)}
.sm-btn.primary:hover{opacity:.9}
.sm-btn.sec{background:var(--bg3);color:var(--text2);border:1px solid var(--border)}
.sm-btn.sec:hover{background:var(--surface2)}

/* ═══════ AI PANEL ═══════ */
#ai-panel{
  display:none;position:fixed;top:58px;right:0;
  width:360px;height:calc(100vh - 58px);
  background:var(--surface);
  border-left:2px solid #FF5A00;
  box-shadow:-6px 0 28px rgba(255,90,0,.1);
  z-index:400;flex-direction:column;overflow:hidden;
  animation:slideIn .25s ease;
}
#ai-panel.open{display:flex}
.aip-hdr{
  padding:14px 18px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  background:linear-gradient(135deg,rgba(255,90,0,.06),rgba(255,90,0,.02));
}
.aip-title{font-family:var(--f1);font-size:14px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:7px}
.aip-close{background:none;border:none;color:var(--text3);cursor:pointer;font-size:22px;line-height:1;padding:2px 6px;transition:var(--tr);font-family:var(--f1)}
.aip-close:hover{color:#FF5A00}
#aip-body{flex:1;overflow-y:auto;padding:14px}
.ins-card{background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:13px;margin-bottom:9px;border-left:3px solid #FF5A00}
.ins-type{font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:5px;color:#FF5A00}
.ins-text{font-size:12px;color:var(--text2);line-height:1.65;font-family:var(--f2)}
.ins-val{font-family:var(--f3);font-size:11px;color:#FF5A00;font-weight:700;margin-top:5px}
.aip-loading{text-align:center;padding:40px 18px;color:var(--text3);font-size:13px;line-height:1.8;font-family:var(--f2)}
.aip-spin{width:28px;height:28px;border:3px solid rgba(255,90,0,.12);border-top-color:#FF5A00;border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 12px}
.ai-strip{
  background:linear-gradient(135deg,rgba(255,90,0,.05),rgba(255,90,0,.02));
  border:1px solid rgba(255,90,0,.15);
  border-radius:10px;padding:11px 14px;margin-bottom:9px;
  display:flex;align-items:flex-start;gap:10px;
}
.ai-strip-icon{font-size:15px;flex-shrink:0;margin-top:1px}
.ai-strip-text{font-size:12px;color:var(--text2);line-height:1.65;font-family:var(--f2)}
.ai-strip-text strong{color:#FF5A00;font-weight:700}

/* ═══════ UPLOAD SCREEN ═══════ */
#upload-screen{
  min-height:calc(100vh - 58px);
  display:flex;align-items:center;justify-content:center;
  flex-direction:column;padding:60px 20px;
  position:relative;overflow:hidden;
  background:var(--bg);
}
.up-bg{
  position:absolute;inset:0;pointer-events:none;
  background:
    radial-gradient(ellipse 60% 50% at 15% 20%,rgba(255,90,0,.08),transparent 65%),
    radial-gradient(ellipse 50% 60% at 85% 80%,rgba(255,90,0,.06),transparent 65%);
}
.up-grid{
  position:absolute;inset:0;pointer-events:none;
  background-image:
    linear-gradient(rgba(255,90,0,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,90,0,.04) 1px,transparent 1px);
  background-size:52px 52px;
}
.up-deco{position:absolute;bottom:-60px;right:-40px;font-family:var(--f1);font-size:400px;font-weight:800;color:rgba(255,90,0,.04);line-height:1;pointer-events:none;user-select:none;letter-spacing:-20px}
.up-badge{font-family:var(--f3);font-size:9.5px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#FF5A00;background:rgba(255,90,0,.08);border:1px solid rgba(255,90,0,.2);padding:5px 16px;border-radius:20px;margin-bottom:20px;position:relative}
.up-logo{display:flex;align-items:center;gap:12px;margin-bottom:10px;position:relative}
.up-logo-mark{width:56px;height:56px;border-radius:14px;background:var(--grd1);display:flex;align-items:center;justify-content:center;font-family:var(--f1);font-size:32px;font-weight:800;color:#fff;box-shadow:0 8px 24px rgba(255,90,0,.35);animation:pulse 3s ease-in-out infinite}
.up-title{font-family:var(--f1);font-size:clamp(32px,5.5vw,58px);font-weight:800;text-align:center;line-height:1.05;color:var(--text);margin-bottom:12px;position:relative;letter-spacing:-1.5px}
.up-title span{color:#FF5A00}
.up-title .dot{color:#FF5A00}
.up-desc{font-size:14.5px;color:var(--text2);text-align:center;line-height:1.8;margin-bottom:42px;max-width:500px;position:relative;font-family:var(--f2)}
.drop-zone{
  width:100%;max-width:580px;background:var(--surface);
  border:2px dashed rgba(255,90,0,.25);border-radius:22px;
  padding:50px 38px;text-align:center;cursor:pointer;
  box-shadow:var(--sh);transition:all .3s;position:relative;overflow:hidden;
}
.drop-zone::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(255,90,0,.04),transparent 60%);pointer-events:none}
.drop-zone:hover,.drop-zone.over{
  border-color:#FF5A00;
  background:rgba(255,90,0,.015);
  box-shadow:0 0 0 6px rgba(255,90,0,.08),var(--sh);
  transform:translateY(-4px);
}
.dz-icon{
  width:76px;height:76px;border-radius:20px;margin:0 auto 20px;
  background:var(--grd1);
  display:flex;align-items:center;justify-content:center;font-size:34px;
  box-shadow:0 8px 24px rgba(255,90,0,.3);
}
.dz-title{font-family:var(--f1);font-size:20px;font-weight:800;color:var(--text);margin-bottom:8px;letter-spacing:-.3px}
.dz-sub{font-size:13px;color:var(--text2);margin-bottom:28px;line-height:1.75;font-family:var(--f2)}
.btn-choose{
  display:inline-flex;align-items:center;gap:8px;
  background:var(--grd1);color:#fff;
  font-family:var(--f1);font-size:13px;font-weight:700;
  padding:13px 30px;border-radius:11px;border:none;cursor:pointer;
  box-shadow:0 6px 18px rgba(255,90,0,.32);transition:var(--tr);
  letter-spacing:.1px;
}
.btn-choose:hover{opacity:.9;transform:translateY(-2px);box-shadow:0 10px 28px rgba(255,90,0,.38)}
.dz-chips{display:flex;flex-wrap:wrap;gap:6px;justify-content:center;margin-top:24px}
.chip{
  font-family:var(--f3);font-size:9.5px;font-weight:600;
  color:var(--text3);background:var(--bg);
  border:1px solid var(--border);padding:4px 12px;border-radius:20px;
  transition:var(--tr);
}
.chip:hover{border-color:rgba(255,90,0,.3);color:#FF5A00;background:var(--a1-soft)}
#up-loader{display:none;text-align:center;margin-top:32px}
.ld-ring{width:52px;height:52px;border-radius:50%;border:3px solid rgba(255,90,0,.1);border-top-color:#FF5A00;animation:spin .9s linear infinite;margin:0 auto 16px}
.ld-txt{font-family:var(--f1);font-size:15px;color:var(--text);font-weight:700;margin-bottom:5px}
.ld-step{font-size:11px;color:var(--text3);font-family:var(--f3)}
#up-err{display:none;margin-top:16px;padding:14px 20px;background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.18);color:var(--a5);border-radius:10px;font-size:13px;max-width:580px;text-align:center;font-family:var(--f2)}
input[type=file]{display:none}

/* ═══════ DASHBOARD SHELL ═══════ */
#dash{display:none;flex-direction:column;min-height:calc(100vh - 58px)}
#dash.panel-open{margin-right:360px;transition:margin-right .3s}

.dash-hero{
  background:#FFFFFF;
  padding:12px 26px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:8px;
  position:relative;
  overflow:hidden;
  border-bottom:1px solid rgba(255,90,0,.12);
}

/* Left side: badge + title + sub all in one compact row */
.dash-hero > div:first-child{
  display:flex;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;
}

.dh-badge{
  font-family:var(--f3);font-size:9px;font-weight:700;
  letter-spacing:1.2px;text-transform:uppercase;
  color:#FF5A00;background:rgba(255,90,0,.08);
  border:1px solid rgba(255,90,0,.2);
  padding:3px 10px;border-radius:20px;
  display:inline-block;white-space:nowrap;
}

.dh-title{
  font-family:var(--f1);font-size:15px;font-weight:800;
  color:#1A1A1A;letter-spacing:-.3px;
  white-space:nowrap;
}

.dh-sub{
  font-size:11px;color:#888888;
  display:flex;align-items:center;gap:10px;
  flex-wrap:wrap;font-family:var(--f2);
}
.dh-sub span{display:flex;align-items:center;gap:4px}

/* Right side stat pills */
.dh-stats{
  display:flex;align-items:center;gap:6px;
  flex-wrap:wrap;position:relative;z-index:1;
}
.dh-stat{
  background:rgba(26,26,26,.04);
  border:1px solid rgba(26,26,26,.08);
  border-radius:8px;padding:6px 13px;
  text-align:center;min-width:80px;
}
.dh-stat-val{
  font-family:var(--f1);font-size:13px;font-weight:800;
  color:#1A1A1A;letter-spacing:-.3px;
}
.dh-stat-lbl{
  font-family:var(--f3);font-size:8px;color:#999999;
  text-transform:uppercase;letter-spacing:.8px;margin-top:1px;
}

/* NAV TABS */
.dash-nav{
  background:var(--bg2);border-bottom:1px solid var(--border);
  padding:0 26px;display:flex;gap:0;overflow-x:auto;flex-shrink:0;
  scrollbar-width:none;
  box-shadow:0 2px 8px rgba(26,26,26,.04);
}
.dash-nav::-webkit-scrollbar{display:none}
.dnav-tab{
  font-family:var(--f1);font-size:11px;font-weight:700;
  color:var(--text3);padding:13px 16px;border:none;background:none;
  cursor:pointer;border-bottom:2.5px solid transparent;transition:var(--tr);
  white-space:nowrap;display:flex;align-items:center;gap:5px;letter-spacing:.1px;
}
.dnav-tab:hover{color:var(--text2)}
.dnav-tab.active{color:#FF5A00;border-bottom-color:#FF5A00}

/* FILTER BAR */
.filter-bar{
  background:var(--bg2);border-bottom:1px solid var(--border);
  padding:7px 26px;display:flex;align-items:center;flex-wrap:wrap;gap:6px;
}
.fb-label{font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text3);white-space:nowrap}
#fb-dropdowns{display:flex;flex-wrap:wrap;gap:4px}
.fb-sel,.fb-num,.fb-search{
  font-family:var(--f2);font-size:11px;font-weight:500;
  color:var(--text2);background:var(--bg);border:1px solid var(--border);
  padding:4px 9px;border-radius:7px;cursor:pointer;outline:none;transition:var(--tr);
}
.fb-sel:focus,.fb-num:focus,.fb-search:focus{border-color:#FF5A00;box-shadow:0 0 0 2px rgba(255,90,0,.1)}
.fb-num{width:85px;font-family:var(--f3)}
.fb-search{width:145px}
.fb-sep{width:1px;height:18px;background:var(--border);margin:0 2px}
.fb-count{font-family:var(--f3);font-size:10px;color:var(--text3);white-space:nowrap}
.fb-clear{font-family:var(--f1);font-size:10px;font-weight:700;color:var(--a5);background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.15);padding:4px 10px;border-radius:7px;cursor:pointer;margin-left:auto;transition:var(--tr)}
.fb-clear:hover{background:rgba(220,38,38,.12)}

/* BODY */
.dash-body{padding:20px 26px 60px;display:flex;flex-direction:column;gap:0}
.tab-view{display:none}
.tab-view.active{display:block;animation:fadeUp .25s ease}

/* SECTION LABELS */
.sec{font-family:var(--f3);font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--text3);margin:24px 0 12px;display:flex;align-items:center;gap:10px}
.sec::after{content:'';flex:1;height:1px;background:var(--border)}
.sec::before{content:'';width:4px;height:14px;background:#FF5A00;border-radius:2px;flex-shrink:0}
.sec-icon{font-size:12px}

/* GRIDS */
.g{display:grid;gap:14px}
.g-kpi{grid-template-columns:repeat(auto-fit,minmax(155px,1fr))}
.g2{grid-template-columns:1fr 1fr}
.g3{grid-template-columns:repeat(3,1fr)}
.g4{grid-template-columns:repeat(4,1fr)}
.g21{grid-template-columns:2fr 1fr}
.g12{grid-template-columns:1fr 2fr}
.g31{grid-template-columns:3fr 1fr}
.g1{grid-template-columns:1fr}
.mt{margin-top:14px}

/* ═══════ KPI CARDS ═══════ */
.kpi{
  background:var(--surface);border-radius:var(--r);padding:17px 15px 14px;
  border:1px solid var(--border);position:relative;overflow:hidden;
  transition:var(--tr);cursor:default;box-shadow:var(--sh);
}
.kpi:hover{transform:translateY(-3px);box-shadow:var(--shh);border-color:rgba(255,90,0,.2)}
.kpi-accent{position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--r) var(--r) 0 0}
.kpi-glow{position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:.06;pointer-events:none}
.kpi-icon{position:absolute;top:13px;right:13px;font-size:18px;opacity:.15}
.kpi-label{font-family:var(--f3);font-size:9px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--text3);margin-bottom:8px}
.kpi-value{font-family:var(--f1);font-size:22px;font-weight:800;line-height:1;letter-spacing:-.8px;color:var(--kc,#FF5A00);margin-bottom:6px}
.kpi-sub{font-size:10.5px;color:var(--text3);line-height:1.5;font-family:var(--f2)}
.kpi-mini{display:flex;gap:8px;margin-top:9px;flex-wrap:wrap;padding-top:9px;border-top:1px solid var(--border)}
.kpi-m{font-family:var(--f3);font-size:9px;color:var(--text3)}
.kpi-m strong{color:var(--text2)}

/* ═══════ CARDS ═══════ */
.card{
  background:var(--surface);border-radius:var(--r);padding:18px;
  border:1px solid var(--border);box-shadow:var(--sh);
  transition:box-shadow .2s,border-color .2s,transform .18s;
  min-width:0;overflow:hidden;
}
.card:hover{box-shadow:var(--shh);border-color:rgba(255,90,0,.18)}
.card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px;flex-wrap:wrap}
.card-title{display:flex;align-items:center;gap:8px;font-family:var(--f1);font-size:13px;font-weight:700;color:var(--text);min-width:0;letter-spacing:-.1px}
.card-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.card-title-text{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.card-ctl{display:flex;align-items:center;gap:5px;flex-wrap:wrap;flex-shrink:0}
.card-sel{
  font-family:var(--f2);font-size:10px;font-weight:600;
  color:var(--text2);background:var(--bg);border:1px solid var(--border);
  padding:3px 8px;border-radius:6px;cursor:pointer;outline:none;transition:var(--tr);
}
.card-sel:focus{border-color:#FF5A00}

/* chart wrappers */
.cw{position:relative;width:100%}
.ch-xs{height:130px}.ch-xs canvas{max-height:130px!important}
.ch-sm{height:200px}.ch-sm canvas{max-height:200px!important}
.ch-md{height:250px}.ch-md canvas{max-height:250px!important}
.ch-lg{height:310px}.ch-lg canvas{max-height:310px!important}
.ch-xl{height:380px}.ch-xl canvas{max-height:380px!important}

/* ═══════ COMPARISON BARS ═══════ */
.cmp-row{display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid var(--border2)}
.cmp-row:last-child{border-bottom:none}
.cmp-rank{font-family:var(--f3);font-size:10px;font-weight:700;color:#FF5A00;width:22px;text-align:center;flex-shrink:0}
.cmp-lbl{font-family:var(--f2);font-size:12px;font-weight:600;color:var(--text);min-width:80px;max-width:120px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cmp-bar-wrap{flex:1;height:6px;background:rgba(26,26,26,.06);border-radius:3px;overflow:hidden}
.cmp-bar{height:100%;border-radius:3px;transition:width .8s cubic-bezier(.22,1,.36,1)}
.cmp-val{font-family:var(--f3);font-size:10.5px;font-weight:700;color:var(--text2);white-space:nowrap;min-width:68px;text-align:right}
.cmp-cnt{font-size:10px;color:var(--text3);min-width:32px;text-align:right;font-family:var(--f3)}

/* ═══════ TABLE ═══════ */
.tbl-wrap{
  overflow-x:auto;overflow-y:auto;max-height:500px;
  -webkit-overflow-scrolling:touch;
  border-radius:0 0 var(--r2) var(--r2);
}
.tbl-wrap table{width:100%;min-width:600px;border-collapse:collapse;font-size:11.5px}
.tbl-wrap thead{position:sticky;top:0;z-index:4}
.tbl-wrap thead th{
  padding:9px 12px;text-align:left;
  font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
  color:var(--text3);background:var(--bg);
  border-bottom:1px solid var(--border);white-space:nowrap;cursor:pointer;user-select:none;
}
.tbl-wrap thead th:hover{color:#FF5A00}
.tbl-wrap thead th.asc::after{content:' ↑'}
.tbl-wrap thead th.desc::after{content:' ↓'}
.tbl-wrap tbody td{padding:9px 12px;border-bottom:1px solid var(--border2);color:var(--text2);vertical-align:middle;white-space:nowrap;font-family:var(--f2)}
.tbl-wrap tbody tr:last-child td{border-bottom:none}
.tbl-wrap tbody tr:hover td{background:rgba(255,90,0,.03)}
.amt-cell{font-family:var(--f3);font-size:11px;color:#1A8A3A;font-weight:700}
.date-cell{font-family:var(--f3);font-size:10px;color:var(--text3)}
.num-cell{font-family:var(--f3);font-size:11px;color:var(--text2)}
.tbl-wrap table.sticky-col thead th:first-child,
.tbl-wrap table.sticky-col tbody td:first-child{position:sticky;left:0;z-index:3;background:var(--bg);box-shadow:2px 0 8px rgba(26,26,26,.07)}
.tbl-wrap table.sticky-col tbody td:first-child{background:var(--surface);z-index:2}
.tbl-wrap::-webkit-scrollbar,.pivot-wrap::-webkit-scrollbar{width:4px;height:4px}
.tbl-wrap::-webkit-scrollbar-track,.pivot-wrap::-webkit-scrollbar-track{background:var(--bg3);border-radius:4px}
.tbl-wrap::-webkit-scrollbar-thumb,.pivot-wrap::-webkit-scrollbar-thumb{background:rgba(255,90,0,.2);border-radius:4px}
.tbl-wrap::-webkit-scrollbar-thumb:hover,.pivot-wrap::-webkit-scrollbar-thumb:hover{background:rgba(255,90,0,.4)}

/* ═══════ BADGES ═══════ */
.badge{display:inline-block;font-family:var(--f3);font-size:9.5px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap}
.b-blue{background:rgba(255,90,0,.1);color:#FF5A00}
.b-violet{background:rgba(124,58,237,.1);color:var(--a7)}
.b-green{background:rgba(26,138,58,.1);color:#1A8A3A}
.b-yellow{background:rgba(217,119,6,.1);color:var(--a4)}
.b-red{background:rgba(220,38,38,.1);color:var(--a5)}
.b-cyan{background:rgba(8,145,178,.1);color:var(--a6)}
.b-pink{background:rgba(219,39,119,.1);color:#DB2777}
.b-lime{background:rgba(21,128,61,.1);color:#15803D}
.b-gray{background:rgba(26,26,26,.07);color:var(--text3)}
.b-orange{background:rgba(255,90,0,.1);color:#FF5A00}

/* ═══════ SUMMARY TABLE ═══════ */
.sum-tbl{width:100%;border-collapse:collapse;font-size:11.5px}
.sum-tbl thead th{padding:8px 10px;text-align:left;font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--text3);border-bottom:1px solid var(--border);background:var(--bg)}
.sum-tbl tbody td{padding:8px 10px;border-bottom:1px solid var(--border2);color:var(--text2);vertical-align:middle;font-family:var(--f2)}
.sum-tbl tbody tr:last-child td{border-bottom:none}
.sum-tbl tbody tr:hover td{background:rgba(255,90,0,.03)}

/* ═══════ HEATMAP ═══════ */
.heat-cell{border-radius:5px;display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:22px;padding:0 5px;font-weight:700;font-family:var(--f3);font-size:10px;white-space:nowrap}
.ct-th{max-width:72px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:center!important;cursor:default}
.scroll-badge{font-family:var(--f3);font-size:9px;font-weight:700;color:#FF5A00;background:rgba(255,90,0,.08);border:1px solid rgba(255,90,0,.2);padding:2px 9px;border-radius:20px;letter-spacing:.4px}

/* ═══════ MINI PROGRESS ═══════ */
.mbar{height:4px;background:rgba(26,26,26,.06);border-radius:2px;overflow:hidden;margin-top:4px}
.mbar-f{height:100%;border-radius:2px}

/* ═══════ WATERFALL ═══════ */
.wf-row{display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--border2)}
.wf-row:last-child{border-bottom:none}
.wf-label{font-family:var(--f2);font-size:11px;font-weight:600;color:var(--text2);min-width:100px;flex-shrink:0}
.wf-bar-wrap{flex:1;height:8px;background:rgba(26,26,26,.05);border-radius:4px;overflow:hidden}
.wf-bar{height:100%;border-radius:4px;transition:width .8s cubic-bezier(.22,1,.36,1)}
.wf-val{font-family:var(--f3);font-size:10px;font-weight:700;color:var(--text2);min-width:70px;text-align:right;white-space:nowrap}
.wf-delta{font-family:var(--f3);font-size:9px;min-width:44px;text-align:right;font-weight:700}
.wf-delta.pos{color:#1A8A3A}
.wf-delta.neg{color:var(--a5)}

/* ═══════ PIVOT ═══════ */
.pivot-wrap{overflow-x:auto;overflow-y:auto;max-height:440px;-webkit-overflow-scrolling:touch;border-radius:var(--r2)}
.pivot-wrap table{width:100%;min-width:500px;border-collapse:collapse;font-size:11px}
.pivot-wrap th{padding:8px 10px;background:var(--bg);border:1px solid var(--border);font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text3);white-space:nowrap;text-align:center;position:sticky;top:0;z-index:4}
.pivot-wrap td{padding:7px 10px;border:1px solid var(--border2);text-align:center;font-family:var(--f3);font-size:10.5px;font-weight:600;color:var(--text2);white-space:nowrap}
.pivot-wrap td.row-hdr{text-align:left;font-family:var(--f1);font-size:11px;font-weight:700;color:var(--text);background:var(--bg);position:sticky;left:0;z-index:3;box-shadow:2px 0 6px rgba(26,26,26,.07)}
.pivot-wrap th:first-child{position:sticky;left:0;z-index:5}
.pivot-wrap td.total-cell{font-weight:800;color:var(--text);background:rgba(255,90,0,.05)}

/* ═══════ PROFILE ═══════ */
.prof-col{background:var(--bg3);border-radius:10px;padding:13px;border:1px solid var(--border);margin-bottom:8px}
.prof-col-name{font-family:var(--f1);font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px;display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.prof-role{font-family:var(--f3);font-size:8px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:2px 8px;border-radius:20px}
.prof-stats{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px}
.prof-stat{font-family:var(--f3);font-size:10px;color:var(--text3)}
.prof-stat strong{color:var(--text2)}
.prof-freq{display:flex;flex-direction:column;gap:4px}
.prof-freq-row{display:flex;align-items:center;gap:6px;font-size:10px}
.prof-freq-lbl{min-width:80px;max-width:110px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text3);font-family:var(--f2)}
.prof-freq-bar{flex:1;height:4px;background:rgba(26,26,26,.06);border-radius:2px;overflow:hidden}
.prof-freq-fill{height:100%;border-radius:2px}
.prof-freq-val{font-family:var(--f3);font-size:9px;color:var(--text3);min-width:28px;text-align:right}

/* ═══════ PAGINATION ═══════ */
.pag{display:flex;flex-wrap:wrap;gap:4px;justify-content:center;margin-top:14px}
.pbtn{font-family:var(--f1);font-size:10px;font-weight:700;color:var(--text3);background:var(--bg);border:1px solid var(--border);width:30px;height:30px;border-radius:7px;cursor:pointer;transition:var(--tr)}
.pbtn:hover{color:#FF5A00;border-color:rgba(255,90,0,.3)}
.pbtn.on{background:#FF5A00;color:#fff;border-color:#FF5A00;box-shadow:0 4px 10px rgba(255,90,0,.3)}
.pdots{color:var(--text3);padding:0 4px;line-height:30px;font-size:11px}

/* ═══════ RESPONSIVE ═══════ */
@media(max-width:1200px){.g3{grid-template-columns:1fr 1fr}.g4{grid-template-columns:1fr 1fr}.g21,.g12,.g31{grid-template-columns:1fr}}
@media(max-width:900px){.g3{grid-template-columns:1fr 1fr}.g2{grid-template-columns:1fr}.g21,.g12,.g31{grid-template-columns:1fr}.dash-body,.filter-bar,.dash-hero{padding-left:14px;padding-right:14px}.dash-nav{padding:0 14px}.g-kpi{grid-template-columns:repeat(2,1fr)}.ch-xl{height:300px}.ch-xl canvas{max-height:300px!important}.ch-lg{height:260px}.ch-lg canvas{max-height:260px!important}#g-cross-main{grid-template-columns:1fr}}
@media(max-width:640px){.g3,.g2,.g4{grid-template-columns:1fr}.g-kpi{grid-template-columns:repeat(2,1fr)}.dh-stats{display:none}#ai-panel{width:100%;left:0}#dash.panel-open{margin-right:0}.filter-bar{padding:6px 12px}.fb-search{width:110px}.ch-xl{height:240px}.ch-xl canvas{max-height:240px!important}.ch-lg{height:210px}.ch-lg canvas{max-height:210px!important}.ch-md{height:190px}.ch-md canvas{max-height:190px!important}.card{padding:14px}.dash-body{padding:14px 12px 40px}}
@media(max-width:400px){.g-kpi{grid-template-columns:1fr}.hdr-brand-sub{display:none}}
.hidden{display:none!important}
</style>
</head>
<body>

<!-- ══ HEADER ══ -->
<!-- <div id="hdr"> -->
<div>
  <div class="hdr-actions">
    <div class="hpill live" id="upload-status"><span class="sdot"></span>LIVE</div>
    <button id="btn-sheet" class="hbtn" onclick="openSheetModal()">📋 Sheets</button>
    <button id="btn-insights" class="hbtn" onclick="toggleAIPanel()">🧠 Insights</button>
    <button id="btn-export" class="hbtn primary-btn" onclick="exportPDF()">⬇ PDF</button>
    <button id="btn-reset" class="hbtn danger" onclick="resetAll()">↩ Reset</button>
  </div>
</div>

<div id="pdf-overlay">
  <div class="pdf-ring">
  </div>
  <div class="pdf-lbl" id="pdf-lbl">Generating PDF…</div>
</div>

<!-- ══ STATIC FILE LOADER ══ -->
<div id="static-loader" style="
  display:none;position:fixed;inset:0;z-index:9000;
  background:#F4F4F2;
  flex-direction:column;align-items:center;justify-content:center;gap:0;
">
  <div style="
    position:absolute;inset:0;pointer-events:none;
    background:
      radial-gradient(ellipse 60% 50% at 15% 20%,rgba(255,90,0,.08),transparent 65%),
      radial-gradient(ellipse 50% 60% at 85% 80%,rgba(255,90,0,.06),transparent 65%);
  "></div>
  <div style="
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,90,0,.04) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,90,0,.04) 1px,transparent 1px);
    background-size:52px 52px;
  "></div>
  <div style="position:relative;display:flex;flex-direction:column;align-items:center;gap:0">
    <div style="
      background:linear-gradient(135deg,#FF5A00,#CC3300);
      border-radius:16px;
      padding:10px 22px;
      display:flex;align-items:center;justify-content:center;
      box-shadow:0 8px 28px rgba(255,90,0,.35);
      margin-bottom:10px;
      animation:pulse 3s ease-in-out infinite;
    ">
      <img src="/images/magi.png" alt="Magi" style="height:160px;width:auto;">
       
    </div>
    <div style="font-family:'IBM Plex Mono',monospace;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#FF5A00;margin-bottom:40px">
      Smart Analytics Platform
    </div>
    <div style="position:relative;width:280px;margin-bottom:16px">
      <div style="height:4px;background:rgba(255,90,0,.12);border-radius:2px;overflow:hidden">
        <div id="sl-progress-bar" style="height:100%;width:0%;background:linear-gradient(90deg,#FF5A00,#CC3300);border-radius:2px;transition:width .4s ease"></div>
      </div>
    </div>
    <div id="sl-step" style="font-family:'IBM Plex Mono',monospace;font-size:11px;color:#888888;letter-spacing:.5px;min-height:18px;text-align:center">
      Loading dashboard…
    </div>
    <div style="margin-top:32px;display:flex;gap:8px">
      <div id="sl-dot-1" style="width:7px;height:7px;border-radius:50%;background:#FF5A00;opacity:1;transition:opacity .3s"></div>
      <div id="sl-dot-2" style="width:7px;height:7px;border-radius:50%;background:#FF5A00;opacity:.3;transition:opacity .3s"></div>
      <div id="sl-dot-3" style="width:7px;height:7px;border-radius:50%;background:#FF5A00;opacity:.3;transition:opacity .3s"></div>
    </div>
  </div>
</div>

<!-- ══ SHEET MODAL ══ -->
<div id="sheet-modal">
  <div class="sm-box">
    <div class="sm-title">📋 Select Sheet</div>
    <div class="sm-sub" id="sm-sub">Choose which sheet to analyse</div>
    <div class="sm-grid" id="sm-grid"></div>
    <div class="sm-actions">
      <button class="sm-btn sec" onclick="closeSheetModal()">Cancel</button>
      <button class="sm-btn primary" onclick="confirmSheetSelect()">Analyse →</button>
    </div>
  </div>
</div>

<!-- ══ AI PANEL ══ -->
<div id="ai-panel">
  <div class="aip-hdr">
    <div class="aip-title">🧠 AI Insights</div>
    <button class="aip-close" onclick="toggleAIPanel()">×</button>
  </div>
  <div id="aip-body"><div class="aip-loading"><div class="aip-spin"></div>Analysing with AI…</div></div>
</div>

<!-- ══ UPLOAD SCREEN ══ -->
<div id="upload-screen">
  <div class="up-bg"></div>
  <div class="up-grid"></div>
  <div class="up-deco" style="display:none">db</div>
  <div class="up-badge">◈ Powered by Magi</div>
  <div class="up-logo">
    <div style="
      background:linear-gradient(135deg,#FF5A00,#CC3300);
      border-radius:16px;padding:10px 26px;
      display:flex;align-items:center;justify-content:center;
      box-shadow:0 8px 24px rgba(255,90,0,.35);
      animation:pulse 3s ease-in-out infinite;">
      <img src="/images/magi.png" alt="Magi" style="height:52px;width:auto;">
    </div>
  </div>
  <h1 class="up-title">Smart <span>Dashboard</span></h1>
  <p class="up-desc">Connected to <code id="db-name-badge" style="background:rgba(255,90,0,.08);padding:2px 7px;border-radius:5px;font-family:var(--f3);font-size:13px">—</code> — pick a table and Magi auto-joins all FK relations instantly.</p>

  <div class="drop-zone" id="db-connect-card" style="max-width:580px;cursor:default;padding:36px 32px;display:none">
    <div class="dz-icon">🗄️</div>
    <div class="dz-title" style="margin-bottom:6px">Select a Table to Analyse</div>
    <div id="db-conn-info" style="font-family:var(--f3);font-size:10px;color:var(--text3);margin-bottom:20px;letter-spacing:.5px"></div>

    <!-- Table selector -->
    <div style="margin-bottom:14px;text-align:left">
      <label style="font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text3);display:block;margin-bottom:5px">① Select Table</label>
      <select id="db-table-sel" class="fb-sel" style="width:100%;padding:9px 12px;font-size:12px;border-radius:9px" onchange="onTableChange()">
        <option value="">— loading tables… —</option>
      </select>
    </div>

    <!-- FK relations preview -->
    <div id="db-fk-preview" style="display:none;margin-bottom:16px;text-align:left">
      <div style="font-family:var(--f3);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text3);margin-bottom:6px">② Auto-Joined Relations</div>
      <div id="db-fk-list" style="display:flex;flex-wrap:wrap;gap:5px"></div>
    </div>

    <!-- Load button -->
    <button type="button" class="btn-choose" id="btn-db-connect"
            style="display:none;width:100%;justify-content:center"
            onclick="connectAndLoad()">
      ⚡ Load Dashboard
    </button>

    <div class="dz-chips" style="margin-top:20px">
      <span class="chip">Auto FK Joins</span><span class="chip">AI Insights</span>
      <span class="chip">20+ Charts</span><span class="chip">PDF Export</span>
      <span class="chip">Pivot Tables</span><span class="chip">Heat Maps</span>
    </div>
  </div>

  <div id="up-loader" style="display:none;text-align:center;margin-top:32px">
    <div class="ld-ring"></div>
    <div class="ld-txt">Fetching data…</div>
    <div class="ld-step" id="loader-step">Connecting to database…</div>
  </div>
  <div id="up-err" style="display:none"></div>
</div>

<!-- ══ DASHBOARD ══ -->
<div id="dash">
  <div id="table-switcher" style="display:none;background:#fff;border-bottom:2px solid rgba(255,90,0,.12);padding:8px 26px;overflow-x:auto;white-space:nowrap;scrollbar-width:none">
  <div id="table-tabs" style="display:inline-flex;gap:6px"></div>
</div>
  <div class="dash-hero">
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <div class="dh-badge" id="dh-badge">◈ Smart Dashboard</div>
    <div class="dh-title" id="dh-title">Dashboard</div>
    <div class="dh-sub" id="dh-sub"></div>
  </div>
  <div class="dh-stats" id="dh-stats"></div>
</div>

  <div class="dash-nav" id="dash-nav">
    <button class="dnav-tab active" data-tab="overview" onclick="switchTab(this,'overview')">📊 Overview</button>
    <button class="dnav-tab" data-tab="charts" onclick="switchTab(this,'charts')">📈 Charts</button>
    <button class="dnav-tab" data-tab="distribution" onclick="switchTab(this,'distribution')">🍩 Distribution</button>
    <button class="dnav-tab" data-tab="trend" onclick="switchTab(this,'trend')">📅 Trend</button>
    <button class="dnav-tab" data-tab="comparison" onclick="switchTab(this,'comparison')">⚖ Compare</button>
    <button class="dnav-tab" data-tab="crosstab" onclick="switchTab(this,'crosstab')">🔀 Cross-Tab</button>
    <button class="dnav-tab" data-tab="pivot" onclick="switchTab(this,'pivot')">🔄 Pivot</button>
    <button class="dnav-tab" data-tab="profile" onclick="switchTab(this,'profile')">🔬 Profile</button>
    <button class="dnav-tab" data-tab="table" onclick="switchTab(this,'table')">📋 Table</button>
  </div>

  <div class="filter-bar">
    <span class="fb-label">🔽 Filter</span>
    <div id="fb-dropdowns"></div>
    <div class="fb-sep" id="fb-sep-range" style="display:none"></div>
    <div id="fb-range-wrap" style="display:none;align-items:center;gap:5px">
      <span style="font-size:10px;color:var(--text3);font-family:var(--f3)" id="fb-range-lbl">Amount</span>
      <input class="fb-num" id="fb-min" type="number" placeholder="Min" oninput="applyFilters()">
      <span style="font-size:10px;color:var(--text3)">–</span>
      <input class="fb-num" id="fb-max" type="number" placeholder="Max" oninput="applyFilters()">
    </div>
    <div class="fb-sep"></div>
    <input class="fb-search" id="fb-search" type="text" placeholder="🔍 Search…" oninput="applyFilters()">
    <span class="fb-count" id="fb-count"></span>
    <button class="fb-clear" onclick="clearFilters()">✕ Clear</button>
  </div>

  <div class="dash-body">
    <!-- OVERVIEW -->
    <div class="tab-view active" id="tv-overview">
      <div id="ai-strips-wrap"></div>
      <div class="sec"><span class="sec-icon">📌</span>Key Metrics</div>
      <div class="g g-kpi" id="kpi-grid"></div>
      <div class="sec mt"><span class="sec-icon">📊</span>Summary Charts</div>
      <div class="g g3" id="g-ov-summary"></div>
      <div class="sec mt"><span class="sec-icon">🏆</span>Leaders &amp; Trends</div>
      <div class="g g21" id="g-ov-bottom"></div>
      <div class="sec mt"><span class="sec-icon">🔁</span>Period Comparison</div>
      <div class="g g2" id="g-ov-period"></div>
    </div>
    <!-- CHARTS -->
    <div class="tab-view" id="tv-charts">
      <div class="sec"><span class="sec-icon">📊</span>Group Analysis</div>
      <div class="g g2" id="g-ch-group"></div>
      <div class="sec mt"><span class="sec-icon">👤</span>Person / Agent Performance</div>
      <div class="g g1" id="g-ch-person"></div>
      <div class="sec mt"><span class="sec-icon">💰</span>Amount Breakdown</div>
      <div class="g g3" id="g-ch-amount"></div>
      <div class="sec mt"><span class="sec-icon">🔀</span>Multi-Dimension</div>
      <div class="g g2" id="g-ch-multi"></div>
    </div>
    <!-- DISTRIBUTION -->
    <div class="tab-view" id="tv-distribution">
      <div class="sec"><span class="sec-icon">🍩</span>Category Distributions</div>
      <div class="g g3" id="g-dist-cat"></div>
      <div class="sec mt"><span class="sec-icon">📐</span>Numeric Distributions</div>
      <div class="g g2" id="g-dist-num"></div>
      <div class="sec mt"><span class="sec-icon">🌡</span>Concentration Analysis</div>
      <div class="g g2" id="g-dist-conc"></div>
    </div>
    <!-- TREND -->
    <div class="tab-view" id="tv-trend">
      <div class="sec"><span class="sec-icon">📅</span>Time-Series Overview</div>
      <div class="g g1" id="g-trend-main"></div>
      <div class="sec mt"><span class="sec-icon">📆</span>Group × Period</div>
      <div class="g g2" id="g-trend-grp"></div>
      <div class="sec mt"><span class="sec-icon">📉</span>Growth &amp; Quarterly</div>
      <div class="g g2" id="g-trend-extra"></div>
    </div>
    <!-- COMPARISON -->
    <div class="tab-view" id="tv-comparison">
      <div class="sec"><span class="sec-icon">⚖</span>Ranking Bars</div>
      <div class="g g3" id="g-cmp-rank"></div>
      <div class="sec mt"><span class="sec-icon">🗂</span>Waterfall Analysis</div>
      <div class="g g2" id="g-cmp-waterfall"></div>
      <div class="sec mt"><span class="sec-icon">🔲</span>Stacked Comparisons</div>
      <div class="g g1" id="g-cmp-stacked"></div>
    </div>
    <!-- CROSS-TAB -->
    <div class="tab-view" id="tv-crosstab">
      <div class="sec"><span class="sec-icon">🔀</span>Cross-Tabulation Heat Maps</div>
      <div class="g g2" id="g-cross-main"></div>
      <div class="sec mt"><span class="sec-icon">💰</span>Amount Cross-Tab</div>
      <div class="g g1" id="g-cross-amt"></div>
    </div>
    <!-- PIVOT -->
    <div class="tab-view" id="tv-pivot">
      <div class="sec"><span class="sec-icon">🔄</span>Pivot Table Builder</div>
      <div class="card" id="pivot-card">
        <div class="card-hdr">
          <div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">Pivot Table</span></div>
          <div class="card-ctl">
            <span style="font-size:10px;color:var(--text3)">Rows:</span><select class="card-sel" id="pv-row" onchange="renderPivot()"></select>
            <span style="font-size:10px;color:var(--text3)">Cols:</span><select class="card-sel" id="pv-col" onchange="renderPivot()"></select>
            <span style="font-size:10px;color:var(--text3)">Value:</span><select class="card-sel" id="pv-val" onchange="renderPivot()"></select>
            <select class="card-sel" id="pv-agg" onchange="renderPivot()">
              <option value="sum">Sum</option><option value="avg">Avg</option>
              <option value="count">Count</option><option value="max">Max</option><option value="min">Min</option>
            </select>
          </div>
        </div>
        <div class="pivot-wrap" id="pivot-wrap"></div>
        <div class="g g1 mt"><div class="cw ch-lg"><canvas id="ch-pivot"></canvas></div></div>
      </div>
    </div>
    <!-- PROFILE -->
    <div class="tab-view" id="tv-profile">
      <div class="sec"><span class="sec-icon">📊</span>Dataset Overview</div>
      <div class="g g4" id="g-prof-kpi"></div>
      <div class="sec mt"><span class="sec-icon">🔬</span>Column Profiles</div>
      <div class="g g3" id="g-prof-cols"></div>
    </div>
    <!-- TABLE -->
    <div class="tab-view" id="tv-table">
      <div class="sec"><span class="sec-icon">📋</span>Master Data Table</div>
      <div class="card">
        <div class="card-hdr">
          <div id="rec-count" style="font-family:var(--f3);font-size:11px;color:var(--text3)"></div>
          <div class="card-ctl">
            <select class="card-sel" id="tbl-col-mode" onchange="renderTable()"><option value="key">Key Columns</option><option value="all">All Columns</option></select>
            <select class="card-sel" id="tbl-page-size" onchange="changePageSize()">
              <option value="25">25/page</option><option value="50">50/page</option>
              <option value="100">100/page</option><option value="all">All</option>
            </select>
            <button class="fb-clear" style="font-size:9px;padding:3px 9px" onclick="exportCSV()">⬇ CSV</button>
          </div>
        </div>
        <div class="tbl-wrap"><table class="sticky-col"><thead><tr id="tbl-head"></tr></thead><tbody id="tbl-body"></tbody></table></div>
        <div class="pag" id="pag"></div>
      </div>
    </div>
  </div>
</div>

<script>
Chart.register(ChartDataLabels);

/* ── PALETTE — myAgenci.ai brand colours ── */
const PAL=['#FF5A00','#1A1A1A','#1A8A3A','#D97706','#DC2626','#0891B2','#7C3AED','#CC3300','#EA580C','#0369A1','#15803D','#DB2777'];
const a16=(hex,a)=>{const n=Math.round(a*255).toString(16).padStart(2,'0');return hex+n};
const $=id=>document.getElementById(id);

let SCHEMA={},ALL=[],FILT=[],FILTERS={},SEARCH='',RANGE={min:null,max:null};
let PAGE=1,PG=25,SORT={col:null,dir:1};
let CI={},AI_OPEN=false,ACTIVE_TAB='overview';
let ALL_SHEETS=[],CURRENT_SHEET_IDX=0;

function detectSchema(headers, rows) {
  const S = {
    columns: [], dateCol: null, amountCols: [], categoryCols: [], nameCols: [],
    groupCols: [], personCols: [], statusCols: [], typeCols: [], idCols: [],
    textCols: [], numericCols: []
  };

  headers.forEach((h, i) => {
    if (!h && h !== 0) return;
    const hRaw = String(h).trim(); if (!hRaw) return;
    const hLow = hRaw.toLowerCase();
    const vals = rows.map(r => r[i]).filter(v => v !== null && v !== undefined && String(v).trim() !== '');
    if (!vals.length) return;

    const numVals = vals.filter(v => typeof v === 'number' || (typeof v === 'string' && !isNaN(parseFloat(v)) && isFinite(v)));
    const numRatio = vals.length ? numVals.length / vals.length : 0;
    const allNums = numVals.map(v => parseFloat(v));
    const uniq = [...new Set(vals.map(v => String(v).trim()))];
    const uniqRatio = vals.length ? uniq.length / vals.length : 0;
    const avgLen = vals.reduce((s, v) => s + String(v).length, 0) / vals.length;

    const dateKw = ['date', 'day', 'time', 'month', 'year', 'dob', 'period', 'posted', 'created', 'updated'];
    const isDate = dateKw.some(k => hLow.includes(k)) || vals.some(v => /^\d{4}-\d{2}-\d{2}/.test(String(v)));

    const amtKw = ['amount', 'amt', 'price', 'value', 'revenue', 'cost', 'salary', 'fee', 'total', 'pay',
      'sale', 'income', 'earning', 'premium', 'sum', 'gross', 'net', 'balance', 'receipt',
      'invoice', 'budget', 'target', 'actual', 'figure', 'charge', 'bill', 'payment',
      'turnover', 'profit', 'loss'];
    const idKw = ['id', 'code', 'no', 'num', 'ref', 'serial', 'sl', 'sr'];
    const isIdByKw = idKw.some(k =>
      hLow === k || hLow.endsWith('_' + k) || hLow.endsWith(' ' + k) ||
      hLow.startsWith(k + '_') || hLow === k || hLow.endsWith(k)
    );
    const nonAmtKw = ['hour', 'hr', 'minute', 'min', 'second', 'sec', 'duration', 'qty', 'quantity',
      'count', 'age', 'score', 'rank', 'rating', 'percent', 'pct', 'ratio',
      'latitude', 'longitude', 'lat', 'lng',
      'mobile', 'phone', 'tel', 'fax', 'contact', 'cell', 'whatsapp', 'number', 'no', 'num',
      'pin', 'zip', 'postal', 'aadhaar', 'pan', 'gstin', 'ifsc'];
    const isNonAmt = nonAmtKw.some(k => hLow.includes(k));
    const isPhonelike = allNums.length > 5 && allNums.filter(v => v > 1000000000).length / allNums.length > 0.5;
const isAmount = !isDate && !isIdByKw && !isNonAmt && !isPhonelike && numRatio > 0.6 &&
      (amtKw.some(k => hLow.includes(k)) || (numRatio > 0.8 && allNums.some(v => v > 500) && uniqRatio > 0.25));
    const isNumeric = !isDate && !isAmount && numRatio > 0.7 && uniq.length > 10;
    const isId = !isDate && !isAmount && numRatio > 0.8 && isIdByKw;
    const isCat = !isDate && !isAmount && !isId && !isNumeric && numRatio < 0.35 &&
      uniqRatio < 0.45 && uniq.length >= 2 && uniq.length <= 50 && avgLen < 50;
    const isName = !isDate && !isAmount && !isId && !isCat && !isNumeric && numRatio < 0.2 && uniqRatio > 0.5;

    let role = 'text';
    if (isDate) role = 'date';
    else if (isAmount) role = 'amount';
    else if (isId) role = 'id';
    else if (isNumeric) role = 'numeric';
    else if (isCat) {
      if (/status|stage|state|active|inactive|flag/.test(hLow)) role = 'status';
      else if (/team|group|dept|division|branch|region|zone|area|cluster|office/.test(hLow)) role = 'group';
      else if (/person|sales|emp|agent|staff|user|rep|exec|officer|by$|done|handle|assign|name|who/.test(hLow) && uniq.length <= 80) role = 'person';
      else if (/type|category|cat|kind|class|service|product|mode|channel|segment|sector/.test(hLow)) role = 'type';
      else role = 'category';
    } else if (isName) role = 'name';

    const sorted = [...allNums].sort((a, b) => a - b);
    const sum = sorted.reduce((s, v) => s + v, 0);
    const avg = sorted.length ? sum / sorted.length : 0;
    const mid = Math.floor(sorted.length / 2);
    const median = sorted.length ? (sorted.length % 2 === 0 ? (sorted[mid - 1] + sorted[mid]) / 2 : sorted[mid]) : 0;
    const std = sorted.length ? Math.sqrt(sorted.reduce((s, v) => s + (v - avg) ** 2, 0) / sorted.length) : 0;

    const col = {
      index: i, header: hRaw, role, uniq, numRatio, isDate, isAmount, isCat,
      uniqRatio, vals, numericVals: allNums,
      maxNum: sorted[sorted.length - 1] ?? 0, minNum: sorted[0] ?? 0,
      sumNum: sum, avgNum: avg, medianNum: median, stdNum: std,
      missingCount: rows.length - vals.length
    };

    S.columns.push(col);
    if (role === 'date' && !S.dateCol) S.dateCol = col;
    if (role === 'amount') S.amountCols.push(col);
    if (['category', 'type', 'status', 'group', 'person'].includes(role)) S.categoryCols.push(col);
    if (role === 'name') S.nameCols.push(col);
    if (role === 'group') S.groupCols.push(col);
    if (role === 'person') S.personCols.push(col);
    if (role === 'status') S.statusCols.push(col);
    if (role === 'type') S.typeCols.push(col);
    if (role === 'id') S.idCols.push(col);
    if (role === 'text') S.textCols.push(col);
    if (role === 'numeric') S.numericCols.push(col);
  });

  S.primaryAmount  = S.amountCols[0] || null;
  S.hasAmount      = S.amountCols.length > 0;
  S.primaryGroup   = S.groupCols[0] || null;
  S.primaryPerson  = S.personCols[0] || null;
  S.chartCols      = S.categoryCols.filter(c => c.uniq.length >= 2 && c.uniq.length <= 30);

  // ── Auto-detect important columns ──────────────────────────────────────────
  // Purely signal-based: no hardcoded role names.
  // Excludes noise (IDs, free-text, sparse, near-unique, single-value cols).
  S.importantCols = S.columns.filter(col => {
    // Structural noise — always exclude
    if (col.role === 'id')   return false;
    if (col.role === 'text') return false;

    // Sparse columns carry unreliable signal
    const totalRows = Math.max(rows.length, 1);
    const fillRate  = 1 - (col.missingCount / totalRows);
    if (fillRate < 0.6) return false;

    // Near-unique columns = free-text / surrogate keys / codes — not groupable
    if (col.uniqRatio > 0.85 && col.uniq.length > 20) return false;

    // Single-value columns have no analytical signal
    if (col.uniq.length < 2) return false;

    return true;
  });

  return S;
}

function normalizeRow(raw,headers,S){
  const obj={};
  S.columns.forEach(col=>{
    let val=raw[col.index];
    if(col.role==='date'){
      const str=val?String(val):'';obj[col.header]=str;
      const d=new Date(str);
      if(!isNaN(d)){
        obj['__month_'+col.header]=d.toLocaleString('en-US',{month:'short',year:'2-digit'});
        obj['__dateObj_'+col.header]=d;
        obj['__year_'+col.header]=d.getFullYear();
        obj['__qtr_'+col.header]='Q'+(Math.floor(d.getMonth()/3)+1)+' '+d.getFullYear();
      }
      if(!S.dateCol||col.header===S.dateCol.header){
        obj['__month']=obj['__month_'+col.header]||'';
        obj['__dateObj']=obj['__dateObj_'+col.header];
        obj['__year']=obj['__year_'+col.header]||'';
        obj['__qtr']=obj['__qtr_'+col.header]||'';
      }
    }else if(col.role==='amount'||col.role==='numeric'){
      obj[col.header]=parseFloat(val)||0;
    }else{
      obj[col.header]=val!==null&&val!==undefined&&String(val).trim()!==''?String(val).trim():null;
    }
  });
  return obj;
}

function isRowMeaningful(obj){
  const filledCols=SCHEMA.columns.filter(col=>{const v=obj[col.header];return v!==null&&v!==undefined&&String(v).trim()!=='';});
  const meaningfulFilled=filledCols.filter(col=>!['status','id'].includes(col.role)||col.header.toLowerCase()!=='active status');
  return filledCols.length>=2&&meaningfulFilled.length>=1;
}

const fmtN=n=>Number(n||0).toLocaleString('en-IN');
const fmtINR=n=>'₹'+fmtN(Math.round(n));
const fmtAmt=n=>SCHEMA.hasAmount?fmtINR(n):fmtN(n);

function colColor(k){
  const kl=(k||'').toLowerCase();
  if(/new|fresh|active|success|yes|true/.test(kl))return'#1A8A3A';
  if(/renew|exist|cont/.test(kl))return'#FF5A00';
  if(/balance|partial|pend|hold/.test(kl))return'#D97706';
  if(/cancel|close|lost|no|false|inactive/.test(kl))return'#DC2626';
  if(/wait|process|progress/.test(kl))return'#0891B2';
  if(/dm|direct/.test(kl))return'#7C3AED';
  const h=[...(k||'')].reduce((a,c)=>a+c.charCodeAt(0),0);
  return PAL[h%PAL.length];
}

const BC=['b-orange','b-violet','b-green','b-yellow','b-red','b-cyan','b-gray','b-pink','b-lime','b-blue'];
function badgeCls(val){
  const v=(val||'').toLowerCase();
  if(/new|active|success|yes/.test(v))return'b-green';
  if(/renew|existing/.test(v))return'b-orange';
  if(/balance|partial/.test(v))return'b-yellow';
  if(/cancel|close|lost|fail|no/.test(v))return'b-red';
  if(/pending|hold|wait/.test(v))return'b-cyan';
  if(/dm|direct/.test(v))return'b-violet';
  const h=[...(val||'')].reduce((a,c)=>a+c.charCodeAt(0),0);
  return BC[h%BC.length];
}

function groupBy(rows,keyFn,amountFns=[]){
  const map={};
  rows.forEach(r=>{
    const k=keyFn(r);if(!k||String(k).trim()==='')return;
    if(!map[k]){map[k]={count:0,amounts:{}};amountFns.forEach(([n])=>{map[k].amounts[n]=0;});}
    map[k].count++;
    amountFns.forEach(([n,fn])=>{map[k].amounts[n]+=(fn(r)||0);});
  });
  return map;
}
const MON_ORDER=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
function sortMonths(months){return[...months].sort((a,b)=>{const pa=a.split(' '),pb=b.split(' ');const ya=parseInt(pa[1]||0),yb=parseInt(pb[1]||0);if(ya!==yb)return ya-yb;return MON_ORDER.indexOf(pa[0])-MON_ORDER.indexOf(pb[0]);});}
function computeStats(arr){
  if(!arr.length)return{sum:0,avg:0,min:0,max:0,median:0,std:0};
  const s=[...arr].sort((a,b)=>a-b);
  const sum=s.reduce((t,v)=>t+v,0),avg=sum/s.length;
  const mid=Math.floor(s.length/2);
  const median=s.length%2===0?(s[mid-1]+s[mid])/2:s[mid];
  const std=Math.sqrt(s.reduce((t,v)=>t+(v-avg)**2,0)/s.length);
  return{sum,avg,min:s[0],max:s[s.length-1],median,std};
}
function pivotAgg(vals,method){
  if(!vals.length)return 0;
  if(method==='sum')return vals.reduce((s,v)=>s+v,0);
  if(method==='avg')return vals.reduce((s,v)=>s+v,0)/vals.length;
  if(method==='count')return vals.length;
  if(method==='max')return Math.max(...vals);
  if(method==='min')return Math.min(...vals);
  return vals.reduce((s,v)=>s+v,0);
}

/* ══ CHART FACTORY ══ */
const DL_OFF={display:false};
const DL_PIE={
  display:ctx=>{const tot=ctx.dataset.data.reduce((a,b)=>a+b,0);return tot&&(ctx.dataset.data[ctx.dataIndex]/tot)>=0.05;},
  color:'#fff',font:{family:"'Outfit',sans-serif",weight:'700',size:10},
  anchor:'center',align:'center',
  formatter:(val,ctx)=>{const tot=ctx.dataset.data.reduce((a,b)=>a+b,0);const pct=tot?Math.round(val/tot*100):0;return pct>=5?pct+'%':'';}
};
function makeDL_BAR(fmt='n'){return{display:ctx=>{const data=ctx.dataset.data;const v=data[ctx.dataIndex];if(!v||v<=0)return false;const max=Math.max(...data.filter(x=>x>0));return(v/max)>0.06;},color:'#444444',font:{family:"'Inter',sans-serif",weight:'600',size:9},anchor:'end',align:'top',offset:2,clamp:true,formatter:val=>val>0?(fmt==='inr'?fmtINR(val):fmtN(val)):''};}
const DL_INSIDE={display:ctx=>{const data=ctx.dataset.data;const v=data[ctx.dataIndex];if(!v||v<=0)return false;const max=Math.max(...data.filter(x=>x>0));return(v/max)>0.08;},color:'#fff',font:{family:"'Inter',sans-serif",weight:'600',size:9},anchor:'center',align:'center',formatter:val=>val>0?fmtN(val):''};
function makeDL_HBAR(fmt='n'){return{display:ctx=>{const v=ctx.dataset.data[ctx.dataIndex];if(!v||v<=0)return false;const max=Math.max(...ctx.dataset.data.filter(x=>x>0));return(v/max)>0.04;},color:'#444444',font:{family:"'IBM Plex Mono',monospace",weight:'600',size:8},anchor:'end',align:'right',offset:3,clamp:true,formatter:val=>val>0?(fmt==='inr'?fmtINR(val):fmtN(val)):''};}

function axS(extra={}){return{ticks:{color:'#888888',font:{size:10,family:"'Inter',sans-serif"},maxRotation:35,autoSkip:true,maxTicksLimit:12},grid:{color:'rgba(26,26,26,.06)'},...extra};}

const CO_BASE={
  responsive:true,maintainAspectRatio:false,
  layout:{padding:{top:20,right:10,bottom:4,left:4}},
  plugins:{
    legend:{labels:{color:'#444444',font:{family:"'Inter',sans-serif",size:11,weight:'500'},boxWidth:9,boxHeight:9,padding:14,usePointStyle:true,pointStyle:'circle'}},
    tooltip:{backgroundColor:'#fff',titleColor:'#1A1A1A',bodyColor:'#444444',borderColor:'rgba(26,26,26,.1)',borderWidth:1,padding:12,cornerRadius:10,titleFont:{family:"'Outfit',sans-serif",size:12,weight:'700'},bodyFont:{family:"'Inter',sans-serif",size:11}}
  }
};

function mkChart(id,type,labels,datasets,extra={}){
  if(CI[id])try{CI[id].destroy();}catch(e){}
  const ctx=$(id);if(!ctx)return;
  const isPolar=type==='pie'||type==='doughnut';
  const dl=extra._dl!==undefined?extra._dl:(isPolar?DL_PIE:makeDL_BAR('n'));
  const stacked=!!extra._stacked;
  delete extra._dl;delete extra._stacked;
  CI[id]=new Chart(ctx,{type,data:{labels,datasets},options:{...CO_BASE,scales:isPolar?undefined:{x:{...axS(),stacked},y:{...axS(),beginAtZero:true,stacked}},...extra,plugins:{...CO_BASE.plugins,...(extra.plugins||{}),datalabels:dl}}});
}
function destroyAll(){Object.values(CI).forEach(c=>{try{c.destroy();}catch(e){}});for(const k in CI)delete CI[k];}

async function autoLoadFromDB() {
  $('upload-screen').style.display = 'none';
  showStaticLoader();
  setLoaderStep('Connecting to database…', 10);

  try {
    const res = await fetch('/db/tables', {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.message || 'Failed to list tables');

    const tables = data.tables || [];
    if (!tables.length) throw new Error('No tables found in database.');

    setLoaderStep(`Found ${tables.length} tables — loading all…`, 20);

    let loaded = 0;
    const fetchResults = await Promise.allSettled(
      tables.map(t =>
        fetch('/db/fetch-table', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: JSON.stringify({ connection: data.connection, table: t.name })
        }).then(r => r.json()).then(json => {
          loaded++;
          setLoaderStep(`Loaded ${loaded} / ${tables.length} tables…`, 20 + Math.round((loaded / tables.length) * 60));
          return json;
        })
      )
    );

    ALL_SHEETS = [];
    fetchResults.forEach(result => {
      if (result.status === 'fulfilled' && result.value?.success) {
        const sheet = result.value.sheets?.[0];
        if (sheet && sheet.count > 0) ALL_SHEETS.push(sheet);
      }
    });

    if (!ALL_SHEETS.length) throw new Error('All tables are empty or failed to load.');
    ALL_SHEETS.sort((a, b) => (b.count || 0) - (a.count || 0));

    setLoaderStep('Generating dashboard…', 88);
    await new Promise(r => setTimeout(r, 150));

    // Build the table switcher tabs
    buildTableSwitcher();

    // Load the first (most rows) table
    renderDashboard(ALL_SHEETS[0], ALL_SHEETS[0].sheet, 0);

    setLoaderStep('Ready!', 100);
    await new Promise(r => setTimeout(r, 320));
    hideStaticLoader();

  } catch (err) {
    hideStaticLoader();
    $('upload-screen').style.display = 'flex';
    $('dash').style.display = 'none';
    const sw = $('table-switcher');
    if (sw) sw.style.display = 'none';
    const el = $('up-err');
    el.style.display = 'block';
    el.innerHTML = `
      <div style="
        max-width:520px;margin:0 auto;
        background:#fff;border-radius:16px;
        border:1.5px solid rgba(255,90,0,.18);
        box-shadow:0 8px 32px rgba(255,90,0,.10);
        padding:32px 28px;text-align:center;
      ">
        <div style="
          width:56px;height:56px;border-radius:14px;
          background:linear-gradient(135deg,#FF5A00,#CC3300);
          display:flex;align-items:center;justify-content:center;
          font-size:26px;margin:0 auto 18px;
          box-shadow:0 6px 18px rgba(255,90,0,.28);
        ">⚡</div>
        <div style="font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;color:#1A1A1A;margin-bottom:8px;letter-spacing:-.3px">
          Dashboard warming up!
        </div>
        <div style="font-size:13px;color:#666;line-height:1.75;font-family:'Inter',sans-serif;margin-bottom:22px">
          The data engine is spinning into gear.<br>
          Please refresh in a moment — your insights will be ready to shine.
        </div>
        <button onclick="location.reload()" style="
          font-family:'Outfit',sans-serif;font-size:13px;font-weight:700;
          background:linear-gradient(135deg,#FF5A00,#CC3300);color:#fff;
          border:none;border-radius:10px;padding:11px 28px;cursor:pointer;
          box-shadow:0 4px 14px rgba(255,90,0,.28);letter-spacing:.1px;
          transition:all .18s;
        " onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
          🔄 Refresh Now
        </button>
      </div>`;
  }
}

function buildTableSwitcher() {
  if (ALL_SHEETS.length <= 1) return;
  
  const switcher = $('table-switcher');
  const tabs = $('table-tabs');
  if (!switcher || !tabs) return;
  
  switcher.style.display = 'block';
  
  tabs.innerHTML = ALL_SHEETS.map((sheet, i) => `
    <button 
      id="ttab-${i}"
      onclick="switchToTable(${i})"
      style="
        font-family:var(--f1);font-size:11px;font-weight:700;
        padding:6px 14px;border-radius:8px;cursor:pointer;
        border:1.5px solid ${i === 0 ? '#FF5A00' : 'rgba(26,26,26,.1)'};
        background:${i === 0 ? 'rgba(255,90,0,.08)' : '#fff'};
        color:${i === 0 ? '#FF5A00' : '#888'};
        white-space:nowrap;transition:all .18s;flex-shrink:0;
      "
    >
      🗄️ ${sheet.sheet}
      <span style="
        font-family:var(--f3);font-size:9px;
        background:${i === 0 ? 'rgba(255,90,0,.15)' : 'rgba(26,26,26,.06)'};
        padding:1px 6px;border-radius:10px;margin-left:4px;
      ">${fmtN(sheet.count)}</span>
    </button>
  `).join('');
}

function switchToTable(idx) {
  if (idx === CURRENT_SHEET_IDX) return;
  
  // Update tab styles
  ALL_SHEETS.forEach((_, i) => {
    const tab = $(`ttab-${i}`);
    if (!tab) return;
    const active = i === idx;
    tab.style.borderColor = active ? '#FF5A00' : 'rgba(26,26,26,.1)';
    tab.style.background = active ? 'rgba(255,90,0,.08)' : '#fff';
    tab.style.color = active ? '#FF5A00' : '#888';
    const badge = tab.querySelector('span');
    if (badge) badge.style.background = active ? 'rgba(255,90,0,.15)' : 'rgba(26,26,26,.06)';
  });

  // Destroy charts and reload
  destroyAll();
  renderDashboard(ALL_SHEETS[idx], ALL_SHEETS[idx].sheet, idx);
}

// Replace initUpload so DOMContentLoaded triggers the auto-load
function initUpload() {
  autoLoadFromDB();
}
const LD_MSGS=['Parsing Excel file…','Detecting column semantics…','Building AI schema…','Computing statistics…','Generating chart data…','Preparing cross-tabs…','Building pivot tables…','Finalising dashboard…'];
let _ldI=0,_ldT=null;
function startLd(){_ldI=0;$('loader-step').textContent=LD_MSGS[0];_ldT=setInterval(()=>{_ldI=(_ldI+1)%LD_MSGS.length;$('loader-step').textContent=LD_MSGS[_ldI];},900);}
function stopLd(){clearInterval(_ldT);}

async function processFile(file){
  $('drop-zone').style.display='none';$('up-loader').style.display='block';$('up-err').style.display='none';
  startLd();
  const fd=new FormData();fd.append('excel_file',file);
  try{
    const res=await fetch('/upload-multi',{method:'POST',body:fd,headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''}});
    let json;try{json=await res.json();}catch{throw new Error(`Server error ${res.status}`);}
    stopLd();$('up-loader').style.display='none';
    if(!res.ok||!json.success)throw new Error(json.message||`HTTP ${res.status}`);
    ALL_SHEETS=json.sheets||[];
    if(ALL_SHEETS.length>1){$('drop-zone').style.display='block';openSheetModal(ALL_SHEETS,file.name);}
    else renderDashboard(ALL_SHEETS[0],file.name,0);
  }catch(err){
    stopLd();$('up-loader').style.display='none';$('drop-zone').style.display='block';
    const el=$('up-err');el.style.display='block';el.innerHTML = '📂  <strong>Almost there!</strong> Your file is unique — try re-uploading and Magi will work its magic. If it persists, check the file format (Excel .xlsx/.xls supported). 🚀';
  }
}

let _pendingSheets=null,_pendingFilename='',_selSheetIdx=0;
// In renderDashboard(), find this line and update the sheet modal sub text:
function openSheetModal(sheets, filename) {
  _pendingSheets = sheets || ALL_SHEETS;
  _pendingFilename = filename || '';
  _selSheetIdx = 0;
  
  // Update title to say "tables" when coming from DB
  const isDB = ALL_SHEETS.length > 0 && ALL_SHEETS[0]?.meta?.source === 'database';
  $('sm-title').textContent = isDB ? '🗄️ Select Table' : '📋 Select Sheet';
  $('sm-sub').textContent = isDB 
    ? `${_pendingSheets.length} tables in database — click to switch`
    : `${_pendingSheets.length} sheets in "${_pendingFilename}"`;
  
  $('sm-grid').innerHTML = _pendingSheets.map((s, i) => `
    <div class="sm-card ${i === 0 ? 'sel' : ''}" onclick="selSheet(${i})" id="smc-${i}">
      <div class="sm-sheet">${isDB ? '🗄️' : '📄'} ${s.sheet}</div>
      <div class="sm-meta">${fmtN(s.count)} rows · ${s.cols} cols</div>
    </div>`).join('');
  
  $('sheet-modal').classList.add('show');
}
function selSheet(i){_selSheetIdx=i;document.querySelectorAll('.sm-card').forEach((c,j)=>c.classList.toggle('sel',j===i));}
function confirmSheetSelect(){closeSheetModal();renderDashboard(_pendingSheets[_selSheetIdx],_pendingFilename,_selSheetIdx);}
function closeSheetModal(){$('sheet-modal').classList.remove('show');}

function resetAll() {
  $('upload-screen').style.display = 'none';
  $('dash').style.display = 'none';
  const sw = $('table-switcher');
  if (sw) sw.style.display = 'none';
  $('ai-panel').classList.remove('open');
  $('upload-status').classList.remove('show');
  ['btn-sheet','btn-insights','btn-export','btn-reset'].forEach(id => $(id).classList.remove('show'));
  SCHEMA = {}; ALL = []; FILT = []; FILTERS = {}; SEARCH = ''; RANGE = { min: null, max: null };
  PAGE = 1; SORT = { col: null, dir: 1 }; destroyAll(); AI_OPEN = false;
  autoLoadFromDB();
}

function renderDashboard(sheetData,filename,sheetIdx){
  CURRENT_SHEET_IDX=sheetIdx;
  SCHEMA=detectSchema(sheetData.headers||[],sheetData.rows||[]);
  ALL=(sheetData.rows||[]).map(r=>normalizeRow(r,sheetData.headers||[],SCHEMA)).filter(r=>isRowMeaningful(r));
  FILT=[...ALL];PAGE=1;PG=25;SORT={col:null,dir:1};FILTERS={};SEARCH='';RANGE={min:null,max:null};
  $('upload-screen').style.display='none';$('dash').style.display='flex';
  $('upload-status').classList.add('show');
  const btns=ALL_SHEETS.length>1?['btn-sheet','btn-insights','btn-export','btn-reset']:['btn-insights','btn-export','btn-reset'];
  ['btn-sheet','btn-insights','btn-export','btn-reset'].forEach(id=>$(id).classList.remove('show'));
  btns.forEach(id=>$(id).classList.add('show'));
  const totalAmt=SCHEMA.hasAmount?ALL.reduce((s,r)=>s+(r[SCHEMA.primaryAmount.header]||0),0):0;
  $('dh-badge').textContent=`◈ ${sheetData.sheet||'Sheet'} · ${SCHEMA.columns.length} fields · AI-Analysed`;
  $('dh-title').textContent=filename||sheetData.sheet||'Dashboard';
  const sub=[`<span>📄 ${fmtN(ALL.length)} records</span>`,`<span>🗂 ${SCHEMA.columns.length} columns</span>`,SCHEMA.hasAmount?`<span>💰 ${fmtINR(totalAmt)}</span>`:'',ALL_SHEETS.length>1?`<span>📊 ${ALL_SHEETS.length} sheets</span>`:''].filter(v=>v!=null&&String(v).trim()!=='');
  $('dh-sub').innerHTML=sub.join('');
  const stats=[{val:fmtN(ALL.length),lbl:'Records'}];
  if(SCHEMA.hasAmount)stats.push({val:fmtINR(totalAmt),lbl:SCHEMA.primaryAmount.header});
  [...SCHEMA.groupCols,...SCHEMA.personCols].slice(0,1).forEach(c=>{const u=[...new Set(ALL.map(r=>r[c.header]).filter(v=>v!=null&&String(v).trim()!==''))];stats.push({val:u.length,lbl:'Unique '+c.header});});
  if(SCHEMA.dateCol){const m=[...new Set(ALL.map(r=>r['__month']).filter(v=>v!=null&&String(v).trim()!==''))];if(m.length)stats.push({val:m.length,lbl:'Months'});}
  $('dh-stats').innerHTML=stats.map(s=>`<div class="dh-stat"><div class="dh-stat-val">${s.val}</div><div class="dh-stat-lbl">${s.lbl}</div></div>`).join('');
  setupPivotSelectors();buildFilters();renderAll();loadAIInsights();
  switchTab(document.querySelector('.dnav-tab[data-tab="overview"]'),'overview');
}

function switchTab(btn,name){
  document.querySelectorAll('.dnav-tab').forEach(t=>t.classList.remove('active'));
  if(btn)btn.classList.add('active');
  else{const b=document.querySelector(`.dnav-tab[data-tab="${name}"]`);if(b)b.classList.add('active');}
  document.querySelectorAll('.tab-view').forEach(v=>v.classList.remove('active'));
  const tv=$(`tv-${name}`);if(tv)tv.classList.add('active');
  ACTIVE_TAB=name;
}

function buildFilters() {
  const wrap = $('fb-dropdowns'); wrap.innerHTML = '';

  // Use importantCols — skip amounts, numerics, dates, ids, text (not groupable in a dropdown)
  SCHEMA.importantCols
    .filter(c => !['amount', 'numeric', 'date', 'id', 'text'].includes(c.role))
    .slice(0, 7)
    .forEach(col => {
      const sel = document.createElement('select');
      sel.className = 'fb-sel';
      sel.dataset.col = col.header;
      sel.innerHTML = `<option value="">All ${col.header}</option>` +
        col.uniq
          .filter(v => v != null && String(v).trim() !== '')
          .sort()
          .map(v => `<option value="${v}">${v}</option>`)
          .join('');
      sel.addEventListener('change', applyFilters);
      wrap.appendChild(sel);
    });

  if (SCHEMA.hasAmount) {
    $('fb-sep-range').style.display = '';
    $('fb-range-wrap').style.display = 'flex';
    $('fb-range-lbl').textContent = SCHEMA.primaryAmount.header;
    const vals = ALL.map(r => r[SCHEMA.primaryAmount.header] || 0);
    $('fb-min').placeholder = 'Min: ' + fmtN(Math.min(...vals));
    $('fb-max').placeholder = 'Max: ' + fmtN(Math.max(...vals));
  } else {
    $('fb-sep-range').style.display = 'none';
    $('fb-range-wrap').style.display = 'none';
  }

  updateFbCount();
}

function applyFilters(){
  FILTERS={};
  document.querySelectorAll('#fb-dropdowns .fb-sel').forEach(s=>{if(s.value)FILTERS[s.dataset.col]=s.value;});
  SEARCH=($('fb-search')||{}).value?.toLowerCase()||'';
  const mn=$('fb-min')?.value,mx=$('fb-max')?.value;
  RANGE={min:mn!==''&&mn!=null?parseFloat(mn):null,max:mx!==''&&mx!=null?parseFloat(mx):null};
  FILT=ALL.filter(r=>{
    for(const[col,val]of Object.entries(FILTERS)){if((r[col]||'')!==val)return false;}
    if(RANGE.min!==null&&SCHEMA.hasAmount&&(r[SCHEMA.primaryAmount.header]||0)<RANGE.min)return false;
    if(RANGE.max!==null&&SCHEMA.hasAmount&&(r[SCHEMA.primaryAmount.header]||0)>RANGE.max)return false;
    if(SEARCH){const s=SCHEMA.columns.map(c=>String(r[c.header]||'')).join(' ').toLowerCase();if(!s.includes(SEARCH))return false;}
    return true;
  });
  PAGE=1;updateFbCount();renderAll();
}
function clearFilters(){
  FILTERS={};SEARCH='';RANGE={min:null,max:null};
  document.querySelectorAll('#fb-dropdowns .fb-sel').forEach(s=>s.value='');
  ['fb-search','fb-min','fb-max'].forEach(id=>{if($(id))$(id).value='';});
  FILT=[...ALL];PAGE=1;updateFbCount();renderAll();
}
function updateFbCount(){$('fb-count').textContent=`${fmtN(FILT.length)} / ${fmtN(ALL.length)}`;}

function renderAll(){
  renderKPIs();renderOverviewSummary();renderOverviewBottom();renderOverviewPeriod();
  renderGroupCharts();renderPersonCharts();renderAmountCharts();renderMultiDimension();
  renderDistributionCats();renderDistributionNums();renderDistributionConc();
  renderTrendMain();renderTrendGroup();renderTrendExtra();
  renderComparisonRanks();renderComparisonWaterfall();renderComparisonStacked();
  renderCrossTabs();renderCrossTabAmount();
  renderPivot();renderProfile();renderTable();
}

/* ══ KPIs ══ */
function renderKPIs(){
  const rows=FILT,cards=[];
  cards.push({label:'Total Records',value:fmtN(rows.length),sub:'filtered entries',icon:'📁',c:'#FF5A00'});
  SCHEMA.amountCols.forEach((col,i)=>{
    const vals=rows.map(r=>r[col.header]||0),st=computeStats(vals);
    const clrs=['#1A8A3A','#CC3300','#D97706'];
    cards.push({label:'Total '+col.header,value:fmtINR(st.sum),sub:`avg ${fmtINR(st.avg)}`,icon:'💰',c:clrs[i%3],mini:[{k:'Max',v:fmtINR(st.max)},{k:'Med',v:fmtINR(st.median)}]});
  });
  [...SCHEMA.groupCols,...SCHEMA.personCols].slice(0,2).forEach(col=>{
    const u=[...new Set(rows.map(r=>r[col.header]).filter(v=>v!=null&&String(v).trim()!==''))];
    cards.push({label:'Unique '+col.header,value:fmtN(u.length),sub:'distinct values',icon:'👥',c:'#CC3300'});
  });
  if(SCHEMA.dateCol){
    const dates=rows.map(r=>r['__dateObj']).filter(v=>v!=null).sort((a,b)=>a-b);
    if(dates.length){const m=[...new Set(rows.map(r=>r['__month']).filter(v=>v!=null&&String(v).trim()!==''))];cards.push({label:'Date Span',value:`${m.length} months`,sub:`${dates[0].toLocaleDateString('en-IN')} – ${dates[dates.length-1].toLocaleDateString('en-IN')}`,icon:'📅',c:'#0891B2'});}
  }
  SCHEMA.chartCols.filter(c=>['status','type'].includes(c.role)).slice(0,1).forEach(col=>{
    const gb=groupBy(rows,r=>r[col.header]);
    const top=Object.entries(gb).sort((a,b)=>b[1].count-a[1].count)[0];
    if(top)cards.push({label:'Top '+col.header,value:top[0],sub:`${top[1].count} records (${Math.round(top[1].count/rows.length*100)}%)`,icon:'🏆',c:'#D97706'});
  });
  if(SCHEMA.numericCols.length){const col=SCHEMA.numericCols[0],vals=rows.map(r=>r[col.header]||0).filter(v=>v),st=computeStats(vals);cards.push({label:col.header,value:fmtN(Math.round(st.avg)),sub:`avg · max ${fmtN(Math.round(st.max))}`,icon:'🔢',c:'#0891B2'});}
  $('kpi-grid').innerHTML=cards.map(c=>`
    <div class="kpi" style="--kc:${c.c}">
      <div class="kpi-accent" style="background:${c.c}"></div>
      <div class="kpi-glow" style="background:${c.c}"></div>
      <div class="kpi-icon">${c.icon}</div>
      <div class="kpi-label">${c.label}</div>
      <div class="kpi-value">${c.value}</div>
      <div class="kpi-sub">${c.sub}</div>
      ${c.mini?.length?`<div class="kpi-mini">${c.mini.map(m=>`<span class="kpi-m"><strong>${m.v}</strong> ${m.k}</span>`).join('')}</div>`:''}
    </div>`).join('');
}

function renderOverviewSummary() {
  const grid = $('g-ov-summary'); grid.innerHTML = '';

  // Use importantCols — only groupable categoricals with meaningful cardinality
  const cols = SCHEMA.importantCols
    .filter(c =>
      !['amount', 'numeric', 'date', 'id', 'text'].includes(c.role) &&
      c.uniq.length >= 2 &&
      c.uniq.length <= 30
    )
    .slice(0, 3);

  if (!cols.length) return;

  const n = cols.length;
  grid.style.gridTemplateColumns = n === 1 ? '1fr' : n === 2 ? '1fr 1fr' : 'repeat(3,1fr)';

  cols.forEach((col, i) => {
    const gb = groupBy(FILT, r => r[col.header]);
    const sorted = Object.entries(gb).sort((a, b) => b[1].count - a[1].count);
    const labels = sorted.map(([k]) => k);
    const counts = sorted.map(([, v]) => v.count);
    const colors = labels.map(k => colColor(k));
    const cid = `ch-ov-s-${i}`, typeId = `ov-s-type-${i}`;

    const card = document.createElement('div'); card.className = 'card';
    card.innerHTML = `
      <div class="card-hdr">
        <div class="card-title">
          <span class="card-dot" style="background:${PAL[i % PAL.length]}"></span>
          <span class="card-title-text">${col.header}</span>
        </div>
        <div class="card-ctl">
          <select class="card-sel" id="${typeId}" onchange="rerenderOvS(${i})">
            <option value="doughnut">Donut</option>
            <option value="bar">Bar</option>
            <option value="pie">Pie</option>
            <option value="polarArea">Polar</option>
            <option value="horizontalBar">H-Bar</option>
          </select>
        </div>
      </div>
      <div class="cw ch-md"><canvas id="${cid}"></canvas></div>`;

    grid.appendChild(card);
    window[`_ovs_${i}`] = { col, labels, counts, colors, cid };
    requestAnimationFrame(() => rerenderOvS(i));
  });
}

function rerenderOvS(i){
  const d=window[`_ovs_${i}`];if(!d)return;
  let type=($(`ov-s-type-${i}`)||{}).value||'doughnut';
  const{labels,counts,colors,cid}=d;
  const isHBar=type==='horizontalBar';if(isHBar)type='bar';
  if(type==='doughnut'||type==='pie'){
    mkChart(cid,type,labels,[{data:counts,backgroundColor:colors.map(c=>a16(c,0.82)),borderColor:'#fff',borderWidth:3,hoverOffset:6}],{cutout:type==='doughnut'?'58%':0,layout:{padding:{top:10,right:10,bottom:10,left:10}},plugins:{legend:{...CO_BASE.plugins.legend,position:'bottom'}},_dl:DL_PIE});
  }else if(type==='polarArea'){
    mkChart(cid,'polarArea',labels,[{data:counts,backgroundColor:colors.map(c=>a16(c,0.65)),borderColor:colors.map(c=>a16(c,0.9)),borderWidth:2}],{layout:{padding:{top:10,right:10,bottom:10,left:10}},plugins:{legend:{...CO_BASE.plugins.legend,position:'bottom'}},_dl:DL_OFF});
  }else{
    const dl=isHBar?makeDL_HBAR('n'):makeDL_BAR('n');
    mkChart(cid,'bar',labels,[{label:'Count',data:counts,backgroundColor:colors.map(c=>a16(c,0.82)),borderRadius:isHBar?4:7,borderWidth:0}],{indexAxis:isHBar?'y':'x',plugins:{legend:{display:false}},_dl:dl});
  }
}

function renderOverviewBottom(){
  const grid=$('g-ov-bottom');grid.innerHTML='';
  const leaderCol=SCHEMA.primaryPerson||SCHEMA.primaryGroup||SCHEMA.chartCols[0];
  if(leaderCol){
    const amFns=SCHEMA.hasAmount?[[SCHEMA.primaryAmount.header,r=>r[SCHEMA.primaryAmount.header]]]:[];
    const gb=groupBy(FILT,r=>r[leaderCol.header],amFns);
    const sorted=Object.entries(gb).sort((a,b)=>SCHEMA.hasAmount?b[1].amounts[SCHEMA.primaryAmount.header]-a[1].amounts[SCHEMA.primaryAmount.header]:b[1].count-a[1].count).slice(0,12);
    const maxVal=sorted.length?(SCHEMA.hasAmount?sorted[0][1].amounts[SCHEMA.primaryAmount.header]:sorted[0][1].count):1;
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#1A8A3A"></span><span class="card-title-text">🏆 Top ${leaderCol.header}</span></div></div>`
      +sorted.map(([k,v],ri)=>{const val=SCHEMA.hasAmount?v.amounts[SCHEMA.primaryAmount.header]:v.count;const pct=Math.round((val/maxVal||0)*100);return`<div class="cmp-row"><div class="cmp-rank">#${ri+1}</div><div class="cmp-lbl" title="${k}">${k}</div><div class="cmp-bar-wrap"><div class="cmp-bar" style="width:${pct}%;background:${colColor(k)}"></div></div><div class="cmp-val">${SCHEMA.hasAmount?fmtINR(val):fmtN(val)}</div><div class="cmp-cnt">${v.count}</div></div>`;}).join('');
    grid.appendChild(card);
  }
  if(SCHEMA.dateCol){
    const monthly={};
    FILT.forEach(r=>{const m=r['__month'];if(!m)return;if(!monthly[m])monthly[m]={count:0,amount:0};monthly[m].count++;if(SCHEMA.hasAmount)monthly[m].amount+=(r[SCHEMA.primaryAmount.header]||0);});
    const months=sortMonths(Object.keys(monthly));
    if(months.length>1){
      const card=document.createElement('div');card.className='card';
      card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">📅 Monthly Summary</span></div><div class="card-ctl"><select class="card-sel" id="ov-tr-mode" onchange="rerenderOvTrend()"><option value="count">Count</option>${SCHEMA.hasAmount?`<option value="amount">${SCHEMA.primaryAmount.header}</option>`:''}</select><select class="card-sel" id="ov-tr-type" onchange="rerenderOvTrend()"><option value="bar">Bar</option><option value="line">Line</option><option value="area">Area</option></select></div></div><div class="cw ch-lg"><canvas id="ch-ov-trend"></canvas></div>`;
      grid.appendChild(card);
      window._ovTrend={monthly,months};
      requestAnimationFrame(()=>rerenderOvTrend());
    }
  }
}
function rerenderOvTrend(){
  const{monthly,months}=window._ovTrend||{};if(!months)return;
  const mode=($('ov-tr-mode')||{}).value||'count';
  const ctype=($('ov-tr-type')||{}).value||'bar';
  const data=months.map(m=>mode==='amount'?monthly[m].amount:monthly[m].count);
  const color=mode==='amount'?'#1A8A3A':'#FF5A00';
  const isLine=ctype==='line'||ctype==='area';
  mkChart('ch-ov-trend',isLine?'line':'bar',months,[{label:mode==='amount'?SCHEMA.primaryAmount.header:'Count',data,backgroundColor:isLine?a16(color,0.12):a16(color,0.82),borderColor:color,borderWidth:isLine?2.5:0,borderRadius:isLine?0:6,fill:ctype==='area',tension:.4,pointRadius:isLine?4:0,pointBackgroundColor:color,pointBorderColor:'#fff',pointBorderWidth:2}],{plugins:{legend:{display:false}},_dl:DL_OFF});
}

function renderOverviewPeriod(){
  const grid=$('g-ov-period');grid.innerHTML='';
  if(!SCHEMA.dateCol)return;
  const monthly={};
  FILT.forEach(r=>{const m=r['__month'];if(!m)return;if(!monthly[m])monthly[m]={count:0,amount:0};monthly[m].count++;if(SCHEMA.hasAmount)monthly[m].amount+=(r[SCHEMA.primaryAmount.header]||0);});
  const months=sortMonths(Object.keys(monthly));
  if(months.length>=2){
    const curr=monthly[months[months.length-1]],prev=monthly[months[months.length-2]];
    const countDelta=prev.count?Math.round((curr.count-prev.count)/prev.count*100):0;
    const amtDelta=SCHEMA.hasAmount&&prev.amount?Math.round((curr.amount-prev.amount)/prev.amount*100):0;
    const moms=SCHEMA.hasAmount?[{label:'Count',curr:curr.count,prev:prev.count,delta:countDelta,fmt:fmtN},{label:SCHEMA.primaryAmount.header,curr:curr.amount,prev:prev.amount,delta:amtDelta,fmt:fmtINR}]:[{label:'Count',curr:curr.count,prev:prev.count,delta:countDelta,fmt:fmtN}];
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#D97706"></span><span class="card-title-text">📆 ${months[months.length-1]} vs ${months[months.length-2]}</span></div></div>`
      +moms.map(m=>`<div style="padding:10px 0;border-bottom:1px solid var(--border2)"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px"><span style="font-family:var(--f1);font-size:11px;font-weight:700;color:var(--text2)">${m.label}</span><span class="badge ${m.delta>=0?'b-green':'b-red'}">${m.delta>=0?'▲':'▼'} ${Math.abs(m.delta)}% MoM</span></div><div style="display:flex;gap:16px"><div><div style="font-family:var(--f3);font-size:13px;font-weight:700;color:var(--text)">${m.fmt(m.curr)}</div><div style="font-size:9px;color:var(--text3);margin-top:2px;font-family:var(--f3)">Current</div></div><div><div style="font-family:var(--f3);font-size:13px;font-weight:600;color:var(--text3)">${m.fmt(m.prev)}</div><div style="font-size:9px;color:var(--text3);margin-top:2px;font-family:var(--f3)">Previous</div></div></div></div>`).join('');
    grid.appendChild(card);
  }
  if(months.length>1){
    const card2=document.createElement('div');card2.className='card';
    card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#0891B2"></span><span class="card-title-text">📊 Count${SCHEMA.hasAmount?' + '+SCHEMA.primaryAmount.header:''} Trend</span></div></div><div class="cw ch-lg"><canvas id="ch-period-combo"></canvas></div>`;
    grid.appendChild(card2);
    const datasets=[{label:'Count',data:months.map(m=>monthly[m].count),backgroundColor:a16('#FF5A00',0.75),borderRadius:5,borderWidth:0,type:'bar'}];
    if(SCHEMA.hasAmount)datasets.push({label:SCHEMA.primaryAmount.header,data:months.map(m=>monthly[m].amount),borderColor:'#1A8A3A',borderWidth:2.5,pointRadius:4,pointBackgroundColor:'#1A8A3A',pointBorderColor:'#fff',pointBorderWidth:2,tension:.4,type:'line',backgroundColor:'transparent',yAxisID:'y2'});
    requestAnimationFrame(()=>mkChart('ch-period-combo','bar',months,datasets,{_dl:DL_OFF,scales:{x:axS(),y:axS({beginAtZero:true}),y2:SCHEMA.hasAmount?{...axS({beginAtZero:true}),position:'right',grid:{display:false}}:undefined}}));
  }
}

function renderGroupCharts(){
  const grid=$('g-ch-group');grid.innerHTML='';
  const groupCol=SCHEMA.primaryGroup||SCHEMA.chartCols[0];if(!groupCol)return;
  const stackCol=SCHEMA.chartCols.find(c=>c.header!==groupCol.header&&c.uniq.length<=10);
  const amFns=SCHEMA.hasAmount?[[SCHEMA.primaryAmount.header,r=>r[SCHEMA.primaryAmount.header]]]:[];
  const gb=groupBy(FILT,r=>r[groupCol.header],amFns);
  const groups=Object.keys(gb).sort((a,b)=>gb[b].count-gb[a].count);
  const card1=document.createElement('div');card1.className='card';
  const amHdr=SCHEMA.hasAmount?`<th>${SCHEMA.primaryAmount.header}</th><th>Avg</th>`:'';
  card1.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#D97706"></span><span class="card-title-text">${groupCol.header} Summary</span></div></div>
    <div class="tbl-wrap" style="max-height:380px"><table><thead><tr><th>${groupCol.header}</th><th>Count</th>${amHdr}<th>Share</th></tr></thead><tbody>
    ${groups.map(g=>{const d=gb[g];const pct=Math.round(d.count/FILT.length*100);const ac=SCHEMA.hasAmount?`<td class="amt-cell">${fmtINR(d.amounts[SCHEMA.primaryAmount.header])}</td><td class="amt-cell" style="font-size:10px;color:var(--text3)">${fmtINR(d.amounts[SCHEMA.primaryAmount.header]/d.count)}</td>`:'';return`<tr><td><strong>${g}</strong></td><td><span class="badge ${badgeCls(g)}">${d.count}</span></td>${ac}<td><div style="display:flex;align-items:center;gap:5px"><div class="mbar" style="width:55px"><div class="mbar-f" style="width:${pct}%;background:${colColor(g)}"></div></div><span style="font-size:9px;color:var(--text3)">${pct}%</span></div></td></tr>`;}).join('')}
    </tbody></table></div>`;
  grid.appendChild(card1);
  const card2=document.createElement('div');card2.className='card';
  card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">${groupCol.header}${stackCol?' × '+stackCol.header:''} Chart</span></div><div class="card-ctl"><select class="card-sel" id="grp-type" onchange="rerenderGroupChart()"><option value="stacked">Stacked</option><option value="grouped">Grouped</option><option value="simple">Simple</option><option value="doughnut">Donut</option><option value="line">Line</option></select></div></div><div class="cw ch-xl"><canvas id="ch-grp"></canvas></div>`;
  grid.appendChild(card2);
  window._grpData={gb,groups,groupCol,stackCol};rerenderGroupChart();
}
function rerenderGroupChart(){
  const{gb,groups,groupCol,stackCol}=window._grpData||{};if(!groups)return;
  const type=($('grp-type')||{}).value||'stacked';
  if(type==='doughnut'){
    const counts=groups.map(g=>gb[g].count),colors=groups.map(g=>a16(colColor(g),0.82));
    mkChart('ch-grp','doughnut',groups,[{data:counts,backgroundColor:colors,borderColor:'#fff',borderWidth:3,hoverOffset:8}],{cutout:'58%',layout:{padding:{top:10,right:10,bottom:10,left:10}},plugins:{legend:{...CO_BASE.plugins.legend,position:'bottom'}},_dl:DL_PIE});
    return;
  }
  if(stackCol&&type!=='simple'){
    const allSts=[...new Set(FILT.map(r=>r[stackCol.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
    const stacked=type==='stacked';
    if(type==='line'){
      const ds=allSts.map(st=>({label:st,data:groups.map(g=>FILT.filter(r=>r[groupCol.header]===g&&r[stackCol.header]===st).length),borderColor:colColor(st),backgroundColor:'transparent',borderWidth:2,tension:.4,pointRadius:4,pointBackgroundColor:colColor(st)}));
      mkChart('ch-grp','line',groups,ds,{_dl:DL_OFF});
    }else{
      const ds=allSts.map(st=>({label:st,data:groups.map(g=>FILT.filter(r=>r[groupCol.header]===g&&r[stackCol.header]===st).length),backgroundColor:colColor(st),borderRadius:3}));
      mkChart('ch-grp','bar',groups,ds,{_dl:DL_INSIDE,_stacked:stacked});
    }
  }else{
    const counts=groups.map(g=>gb[g].count),colors=groups.map(g=>a16(colColor(g),0.82));
    const isLine=type==='line';
    mkChart('ch-grp',isLine?'line':'bar',groups,[{label:'Count',data:counts,backgroundColor:isLine?'transparent':colors,borderColor:colors,borderWidth:isLine?2:0,borderRadius:8,tension:.4,pointRadius:isLine?4:0,fill:false}],{plugins:{legend:{display:false}},_dl:isLine?DL_OFF:makeDL_BAR('n')});
  }
}

function renderPersonCharts(){
  const grid=$('g-ch-person');grid.innerHTML='';
  const personCol=SCHEMA.primaryPerson;if(!personCol)return;
  const card=document.createElement('div');card.className='card';
  const grpOpts=SCHEMA.groupCols.length?`<select class="card-sel" id="per-grp" onchange="rerenderPerson()"><option value="">All Groups</option>${[...new Set(ALL.map(r=>r[SCHEMA.groupCols[0].header]).filter(v=>v!=null&&String(v).trim()!==''))].sort().map(g=>`<option value="${g}">${g}</option>`).join('')}</select>`:'';
  const modeOpts=SCHEMA.amountCols.length?`<select class="card-sel" id="per-mode" onchange="rerenderPerson()">${SCHEMA.amountCols.map(c=>`<option value="${c.header}">${c.header}</option>`).join('')}<option value="count">Count</option></select>`:'';
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#1A8A3A"></span><span class="card-title-text">${personCol.header} Performance</span></div><div class="card-ctl">${grpOpts}${modeOpts}<select class="card-sel" id="per-top" onchange="rerenderPerson()"><option value="15">Top 15</option><option value="25">Top 25</option><option value="10">Top 10</option></select><select class="card-sel" id="per-type" onchange="rerenderPerson()"><option value="horizontalBar">H-Bar</option><option value="bar">Bar</option><option value="line">Line</option></select></div></div><div class="cw ch-xl"><canvas id="ch-person"></canvas></div>`;
  grid.appendChild(card);
  window._personData={personCol};rerenderPerson();
}
function rerenderPerson(){
  const{personCol}=window._personData||{};if(!personCol)return;
  const grpFilter=($('per-grp')||{}).value||'';
  const mode=($('per-mode')||{}).value||(SCHEMA.hasAmount?SCHEMA.primaryAmount.header:'count');
  const topN=parseInt(($('per-top')||{}).value||15);
  let ctype=($('per-type')||{}).value||'horizontalBar';
  const groupCol=SCHEMA.groupCols[0];
  const srcRows=grpFilter&&groupCol?FILT.filter(r=>r[groupCol.header]===grpFilter):FILT;
  const amFns=mode!=='count'&&SCHEMA.hasAmount?[[mode,r=>r[mode]||0]]:[];
  const gb2=groupBy(srcRows,r=>r[personCol.header],amFns);
  const persons=Object.keys(gb2).sort((a,b)=>mode!=='count'&&SCHEMA.hasAmount?gb2[b].amounts[mode]-gb2[a].amounts[mode]:gb2[b].count-gb2[a].count).slice(0,topN);
  const vals=persons.map(p=>mode!=='count'&&SCHEMA.hasAmount?gb2[p].amounts[mode]:gb2[p].count);
  const colors=persons.map((_,i)=>a16(PAL[i%PAL.length],0.82));
  const isHBar=ctype==='horizontalBar';if(isHBar)ctype='bar';
  const dl=isHBar?makeDL_HBAR(mode!=='count'&&SCHEMA.hasAmount?'inr':'n'):makeDL_BAR(mode!=='count'&&SCHEMA.hasAmount?'inr':'n');
  mkChart('ch-person',ctype,persons,[{label:mode,data:vals,backgroundColor:ctype==='line'?'transparent':colors,borderColor:ctype==='line'?'#FF5A00':colors,borderWidth:ctype==='line'?2:0,borderRadius:7,tension:.4,pointRadius:ctype==='line'?4:0,fill:false}],{indexAxis:isHBar?'y':'x',plugins:{legend:{display:false}},_dl:ctype==='line'?DL_OFF:dl});
}

function renderAmountCharts(){
  const grid=$('g-ch-amount');grid.innerHTML='';
  if(!SCHEMA.hasAmount)return;
  SCHEMA.amountCols.slice(0,1).forEach((amtCol)=>{
    const groupCol=SCHEMA.primaryGroup||SCHEMA.chartCols[0];if(!groupCol)return;
    const gb=groupBy(FILT,r=>r[groupCol.header],[[amtCol.header,r=>r[amtCol.header]]]);
    const sorted=Object.entries(gb).sort((a,b)=>b[1].amounts[amtCol.header]-a[1].amounts[amtCol.header]);
    const labels=sorted.map(([k])=>k),amts=sorted.map(([,v])=>v.amounts[amtCol.header]),colors=labels.map(k=>a16(colColor(k),0.82));
    const st=computeStats(FILT.map(r=>r[amtCol.header]||0));
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#1A8A3A"></span><span class="card-title-text">${amtCol.header} Share</span></div></div><div class="cw ch-md"><canvas id="ch-amt-d"></canvas></div><div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border2)">${[{k:'Total',v:fmtINR(st.sum)},{k:'Avg',v:fmtINR(st.avg)},{k:'Med',v:fmtINR(st.median)}].map(m=>`<span style="font-family:var(--f3);font-size:10px;color:var(--text3)"><strong style="color:var(--text2)">${m.v}</strong> ${m.k}</span>`).join('')}</div>`;
    grid.appendChild(card);
    requestAnimationFrame(()=>mkChart('ch-amt-d','doughnut',labels,[{data:amts,backgroundColor:colors,borderColor:'#fff',borderWidth:3,hoverOffset:8}],{cutout:'60%',layout:{padding:{top:10,right:10,bottom:10,left:10}},plugins:{legend:{...CO_BASE.plugins.legend,position:'bottom'}},_dl:{...DL_PIE,formatter:(v,c)=>{const tot=c.dataset.data.reduce((a,b)=>a+b,0);const pct=tot?Math.round(v/tot*100):0;return pct>=5?pct+'%':'';},color:'#fff'}}));
    const card2=document.createElement('div');card2.className='card';
    card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">${amtCol.header} by ${groupCol.header}</span></div></div><div class="cw ch-md"><canvas id="ch-amt-hb"></canvas></div>`;
    grid.appendChild(card2);
    requestAnimationFrame(()=>mkChart('ch-amt-hb','bar',labels,[{label:amtCol.header,data:amts,backgroundColor:colors,borderRadius:5,borderWidth:0}],{indexAxis:'y',plugins:{legend:{display:false}},_dl:makeDL_HBAR('inr')}));
    if(FILT.length>10){
      const vals=FILT.map(r=>r[amtCol.header]||0).sort((a,b)=>a-b);
      const min=vals[0],max=vals[vals.length-1],bins=10,step=(max-min)/bins||1;
      const bkts=Array.from({length:bins},(_,i)=>({label:fmtN(Math.round(min+i*step)),count:0}));
      vals.forEach(v=>{const bi=Math.min(Math.floor((v-min)/step),bins-1);bkts[bi].count++;});
      const card3=document.createElement('div');card3.className='card';
      card3.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#CC3300"></span><span class="card-title-text">${amtCol.header} Histogram</span></div></div><div class="cw ch-md"><canvas id="ch-amt-hist"></canvas></div>`;
      grid.appendChild(card3);
      requestAnimationFrame(()=>mkChart('ch-amt-hist','bar',bkts.map(b=>b.label),[{label:'Freq',data:bkts.map(b=>b.count),backgroundColor:a16('#CC3300',0.75),borderRadius:6,borderWidth:0}],{plugins:{legend:{display:false}},_dl:makeDL_BAR('n')}));
    }
  });
}

function renderMultiDimension(){
  const grid=$('g-ch-multi');grid.innerHTML='';
  const groupCol=SCHEMA.primaryGroup||SCHEMA.chartCols[0];
  if(SCHEMA.amountCols.length>=2&&groupCol){
    const amFns=SCHEMA.amountCols.map(c=>[c.header,r=>r[c.header]||0]);
    const gb=groupBy(FILT,r=>r[groupCol.header],amFns);
    const groups=Object.keys(gb).sort((a,b)=>gb[b].count-gb[a].count).slice(0,15);
    const datasets=SCHEMA.amountCols.map((c,i)=>({label:c.header,data:groups.map(g=>gb[g]?.amounts[c.header]||0),backgroundColor:a16(PAL[i%PAL.length],0.78),borderRadius:5,borderWidth:0}));
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">All Amounts by ${groupCol.header}</span></div><div class="card-ctl"><select class="card-sel" id="multi-type" onchange="rerenderMulti()"><option value="grouped">Grouped</option><option value="stacked">Stacked</option><option value="line">Line</option></select></div></div><div class="cw ch-xl"><canvas id="ch-multi"></canvas></div>`;
    grid.appendChild(card);
    window._multiData={groups,datasets};rerenderMulti();
  }
  if(SCHEMA.amountCols.length>=2){
    const xCol=SCHEMA.amountCols[0],yCol=SCHEMA.amountCols[1];
    const colorCol=SCHEMA.primaryGroup||SCHEMA.chartCols[0];
    let datasets2;
    if(colorCol){const grpVals=[...new Set(FILT.map(r=>r[colorCol.header]).filter(v=>v!=null&&String(v).trim()!==''))];datasets2=grpVals.map((g,i)=>({label:g,data:FILT.filter(r=>r[colorCol.header]===g).map(r=>({x:r[xCol.header]||0,y:r[yCol.header]||0})),backgroundColor:a16(PAL[i%PAL.length],0.6),pointRadius:5}));}
    else{datasets2=[{label:'Records',data:FILT.map(r=>({x:r[xCol.header]||0,y:r[yCol.header]||0})),backgroundColor:a16('#FF5A00',0.6),pointRadius:4}];}
    const card2=document.createElement('div');card2.className='card';
    card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#7C3AED"></span><span class="card-title-text">${xCol.header} vs ${yCol.header} Scatter</span></div></div><div class="cw ch-xl"><canvas id="ch-scatter"></canvas></div>`;
    grid.appendChild(card2);
    requestAnimationFrame(()=>mkChart('ch-scatter','scatter',null,datasets2,{_dl:DL_OFF,scales:{x:{...axS(),title:{display:true,text:xCol.header,color:'#888888',font:{family:"'Inter',sans-serif",size:11}}},y:{...axS({beginAtZero:true}),title:{display:true,text:yCol.header,color:'#888888',font:{family:"'Inter',sans-serif",size:11}}}}}));
  }
}
function rerenderMulti(){
  const{groups,datasets}=window._multiData||{};if(!groups)return;
  const t=($('multi-type')||{}).value||'grouped';const stacked=t==='stacked';
  if(t==='line'){const lineDs=datasets.map(d=>({...d,type:'line',borderColor:d.backgroundColor.slice(0,7),backgroundColor:'transparent',borderWidth:2,tension:.4,pointRadius:3,fill:false}));mkChart('ch-multi','line',groups,lineDs,{_dl:DL_OFF});}
  else mkChart('ch-multi','bar',groups,datasets,{_dl:DL_INSIDE,_stacked:stacked});
}

function renderDistributionCats(){
  const grid=$('g-dist-cat');grid.innerHTML='';
  const cols=SCHEMA.chartCols;if(!cols.length)return;
  const n=Math.min(cols.length,6);
  grid.style.gridTemplateColumns=n===1?'1fr':n===2?'1fr 1fr':'repeat(3,1fr)';
  cols.slice(0,6).forEach((col,i)=>{
    const gb=groupBy(FILT,r=>r[col.header]);
    const sorted=Object.entries(gb).sort((a,b)=>b[1].count-a[1].count);
    const labels=sorted.map(([k])=>k),counts=sorted.map(([,v])=>v.count),colors=labels.map(k=>a16(colColor(k),0.82));
    const cid=`ch-dc-${i}`,typeId=`dc-type-${i}`;
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:${PAL[i%PAL.length]}"></span><span class="card-title-text">${col.header}</span></div><div class="card-ctl"><select class="card-sel" id="${typeId}" onchange="rerenderDistCat(${i})"><option value="doughnut">Donut</option><option value="bar">Bar</option><option value="pie">Pie</option><option value="horizontalBar">H-Bar</option></select></div></div><div class="cw ch-sm"><canvas id="${cid}"></canvas></div>`;
    grid.appendChild(card);
    window[`_dc_${i}`]={col,labels,counts,colors,cid};
    requestAnimationFrame(()=>rerenderDistCat(i));
  });
}
function rerenderDistCat(i){
  const d=window[`_dc_${i}`];if(!d)return;
  let type=($(`dc-type-${i}`)||{}).value||'doughnut';
  const{labels,counts,colors,cid}=d;
  const isHBar=type==='horizontalBar';if(isHBar)type='bar';
  if(type==='doughnut'||type==='pie'){
    mkChart(cid,type,labels,[{data:counts,backgroundColor:colors,borderColor:'#fff',borderWidth:3,hoverOffset:6}],{cutout:type==='doughnut'?'58%':0,layout:{padding:{top:10,right:10,bottom:10,left:10}},plugins:{legend:{...CO_BASE.plugins.legend,position:'bottom'}},_dl:DL_PIE});
  }else{
    const dl=isHBar?makeDL_HBAR('n'):makeDL_BAR('n');
    mkChart(cid,'bar',labels,[{label:'Count',data:counts,backgroundColor:colors,borderRadius:isHBar?4:7,borderWidth:0}],{indexAxis:isHBar?'y':'x',plugins:{legend:{display:false}},_dl:dl});
  }
}

function renderDistributionNums(){
  const grid=$('g-dist-num');grid.innerHTML='';
  const numCols=[...SCHEMA.amountCols,...SCHEMA.numericCols].slice(0,4);
  if(!numCols.length)return;
  numCols.forEach((col,i)=>{
    const vals=FILT.map(r=>r[col.header]||0).filter(v=>v!==0).sort((a,b)=>a-b);
    if(vals.length<5)return;
    const bins=12,min=vals[0],max=vals[vals.length-1],step=(max-min)/bins||1;
    const bkts=Array.from({length:bins},(_,bi)=>({label:fmtN(Math.round(min+bi*step)),count:0}));
    vals.forEach(v=>{const bi=Math.min(Math.floor((v-min)/step),bins-1);bkts[bi].count++;});
    const st=computeStats(vals);
    const cid=`ch-dn-${i}`;
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:${PAL[(i+2)%PAL.length]}"></span><span class="card-title-text">${col.header} Distribution</span></div></div><div class="cw ch-md"><canvas id="${cid}"></canvas></div><div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border2)">${[{k:'Min',v:col.isAmount?fmtINR(st.min):fmtN(Math.round(st.min))},{k:'Avg',v:col.isAmount?fmtINR(st.avg):fmtN(Math.round(st.avg))},{k:'Max',v:col.isAmount?fmtINR(st.max):fmtN(Math.round(st.max))},{k:'Std',v:col.isAmount?fmtINR(st.std):fmtN(Math.round(st.std))}].map(m=>`<span style="font-family:var(--f3);font-size:10px;color:var(--text3)"><strong style="color:var(--text2)">${m.v}</strong> ${m.k}</span>`).join('')}</div>`;
    grid.appendChild(card);
    requestAnimationFrame(()=>mkChart(cid,'bar',bkts.map(b=>b.label),[{label:col.header,data:bkts.map(b=>b.count),backgroundColor:a16(PAL[(i+2)%PAL.length],0.72),borderRadius:4,borderWidth:0}],{plugins:{legend:{display:false}},_dl:makeDL_BAR('n')}));
  });
}

function renderDistributionConc(){
  const grid=$('g-dist-conc');grid.innerHTML='';
  if(!SCHEMA.hasAmount||!SCHEMA.primaryGroup)return;
  const amtCol=SCHEMA.primaryAmount,groupCol=SCHEMA.primaryGroup;
  const gb=groupBy(FILT,r=>r[groupCol.header],[[amtCol.header,r=>r[amtCol.header]]]);
  const sorted=Object.entries(gb).sort((a,b)=>b[1].amounts[amtCol.header]-a[1].amounts[amtCol.header]);
  const total=sorted.reduce((s,[,v])=>s+v.amounts[amtCol.header],0)||1;
  let cumPct=0;
  const paretoLabels=sorted.map(([k])=>k);
  const parAmts=sorted.map(([,v])=>v.amounts[amtCol.header]);
  const parCum=sorted.map(([,v])=>{cumPct+=v.amounts[amtCol.header]/total*100;return Math.round(cumPct);});
  const card=document.createElement('div');card.className='card';
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#D97706"></span><span class="card-title-text">Pareto — ${amtCol.header} by ${groupCol.header}</span></div></div><div class="cw ch-lg"><canvas id="ch-pareto"></canvas></div>`;
  grid.appendChild(card);
  requestAnimationFrame(()=>mkChart('ch-pareto','bar',paretoLabels,[{label:amtCol.header,data:parAmts,backgroundColor:paretoLabels.map(k=>a16(colColor(k),0.8)),borderRadius:6,borderWidth:0},{label:'Cumulative %',data:parCum,borderColor:'#FF5A00',borderWidth:2.5,pointRadius:4,pointBackgroundColor:'#FF5A00',pointBorderColor:'#fff',pointBorderWidth:2,tension:.4,type:'line',backgroundColor:'transparent',yAxisID:'y2'}],{_dl:DL_OFF,scales:{x:axS(),y:axS({beginAtZero:true}),y2:{...axS({beginAtZero:true}),position:'right',grid:{display:false},max:100,ticks:{...axS().ticks,callback:v=>v+'%'}}}}));
  const top10=sorted.slice(0,10),bot10=[...sorted].reverse().slice(0,10);
  const card2=document.createElement('div');card2.className='card';
  card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#1A8A3A"></span><span class="card-title-text">Top 10 vs Bottom 10</span></div></div><div class="cw ch-lg"><canvas id="ch-topbot"></canvas></div>`;
  grid.appendChild(card2);
  requestAnimationFrame(()=>mkChart('ch-topbot','bar',[...top10.map(([k])=>k),'---',...bot10.map(([k])=>k)],[{label:'Top 10',data:[...top10.map(([,v])=>v.amounts[amtCol.header]),...Array(1+bot10.length).fill(0)],backgroundColor:a16('#1A8A3A',0.8),borderRadius:5},{label:'Bottom 10',data:[...Array(top10.length+1).fill(0),...bot10.map(([,v])=>v.amounts[amtCol.header])],backgroundColor:a16('#DC2626',0.75),borderRadius:5}],{_dl:DL_OFF,plugins:{legend:{...CO_BASE.plugins.legend}}}));
}

function buildTrendBuckets(period){
  const b={};
  FILT.forEach(r=>{
    let key;
    if(period==='monthly')key=r['__month'];
    else if(period==='quarterly')key=r['__qtr'];
    else if(period==='yearly')key=String(r['__year']||'');
    if(!key)return;
    if(!b[key]){b[key]={count:0,amounts:{}};SCHEMA.amountCols.forEach(c=>{b[key].amounts[c.header]=0;});}
    b[key].count++;
    SCHEMA.amountCols.forEach(c=>{b[key].amounts[c.header]+=(r[c.header]||0);});
  });
  const keys=period==='monthly'?sortMonths(Object.keys(b)):Object.keys(b).sort();
  return{bucket:b,keys};
}

function renderTrendMain(){
  const grid=$('g-trend-main');grid.innerHTML='';
  if(!SCHEMA.dateCol){grid.innerHTML='<div class="card" style="text-align:center;padding:36px;color:var(--text3);font-family:var(--f2)">📅 <strong>No time travel today!</strong> Add a date column to your dataset and Magi will reveal beautiful time-series trends. ✨</div>';return;}
  const card=document.createElement('div');card.className='card';
  const amtOpts=SCHEMA.amountCols.map(c=>`<option value="${c.header}">${c.header}</option>`).join('');
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">Time-Series Trend</span></div><div class="card-ctl"><select class="card-sel" id="tr-period" onchange="rerenderTrend()"><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="yearly">Yearly</option></select><select class="card-sel" id="tr-mode" onchange="rerenderTrend()"><option value="count">Count</option>${SCHEMA.hasAmount?amtOpts:''}<option value="both">Count + Amount</option></select><select class="card-sel" id="tr-ctype" onchange="rerenderTrend()"><option value="bar">Bar</option><option value="line">Line</option><option value="area">Area</option></select></div></div><div class="cw ch-xl"><canvas id="ch-trend-main"></canvas></div>`;
  grid.appendChild(card);rerenderTrend();
}
function rerenderTrend(){
  const period=($('tr-period')||{}).value||'monthly';
  const mode=($('tr-mode')||{}).value||'count';
  const ctype=($('tr-ctype')||{}).value||'bar';
  const{bucket,keys}=buildTrendBuckets(period);if(!keys.length)return;
  const isLine=ctype==='line'||ctype==='area';
  const ds=[];
  if(mode==='count'||mode==='both'){ds.push({label:'Count',data:keys.map(k=>bucket[k]?.count||0),backgroundColor:isLine?a16('#FF5A00',0.1):a16('#FF5A00',0.82),borderColor:'#FF5A00',borderWidth:isLine?2.5:0,borderRadius:isLine?0:5,fill:ctype==='area',tension:.4,pointRadius:isLine?4:0,pointBackgroundColor:'#FF5A00',pointBorderColor:'#fff',pointBorderWidth:2});}
  if(mode!=='count'&&SCHEMA.hasAmount){const amtKey=mode==='both'?SCHEMA.primaryAmount.header:mode;ds.push({label:amtKey,data:keys.map(k=>bucket[k]?.amounts?.[amtKey]||0),backgroundColor:a16('#1A8A3A',0.1),borderColor:'#1A8A3A',borderWidth:2.5,pointRadius:4,pointBackgroundColor:'#1A8A3A',pointBorderColor:'#fff',pointBorderWidth:2,type:'line',tension:.4,fill:false,yAxisID:mode==='both'?'y2':'y'});}
  mkChart('ch-trend-main','bar',keys,ds,{_dl:DL_OFF,scales:{x:axS(),y:axS({beginAtZero:true}),y2:mode==='both'&&SCHEMA.hasAmount?{...axS({beginAtZero:true}),position:'right',grid:{display:false}}:undefined}});
}

function renderTrendGroup(){
  const grid=$('g-trend-grp');grid.innerHTML='';
  if(!SCHEMA.dateCol||!SCHEMA.primaryGroup)return;
  const groupCol=SCHEMA.primaryGroup;
  const{bucket:monthly,keys:months}=buildTrendBuckets('monthly');if(months.length<2)return;
  const allGroups=[...new Set(FILT.map(r=>r[groupCol.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const grpM={};
  FILT.forEach(r=>{const m=r['__month'],g=r[groupCol.header];if(!m||!g)return;if(!grpM[g])grpM[g]={};if(!grpM[g][m])grpM[g][m]={count:0,amount:0};grpM[g][m].count++;if(SCHEMA.hasAmount)grpM[g][m].amount+=(r[SCHEMA.primaryAmount.header]||0);});
  const card=document.createElement('div');card.className='card';
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#CC3300"></span><span class="card-title-text">${groupCol.header} Monthly Trend</span></div><div class="card-ctl">${SCHEMA.hasAmount?`<select class="card-sel" id="grp-tr-mode" onchange="rerenderGrpTrend()"><option value="count">Count</option><option value="amount">Amount</option></select>`:''}<select class="card-sel" id="grp-tr-type" onchange="rerenderGrpTrend()"><option value="line">Area Lines</option><option value="bar">Stacked Bars</option></select></div></div><div class="cw ch-lg"><canvas id="ch-grp-trend"></canvas></div>`;
  grid.appendChild(card);
  window._grpTrend={allGroups,grpM,months,groupCol};rerenderGrpTrend();
  if(months.length>1&&allGroups.length>1){
    const maxVal=Math.max(1,...allGroups.flatMap(g=>months.map(m=>grpM[g]?.[m]?.count||0)));
    const card2=document.createElement('div');card2.className='card';
    card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#D97706"></span><span class="card-title-text">Activity Heatmap: ${groupCol.header} × Month</span></div></div>
      <div class="tbl-wrap" style="max-height:320px">
        <table class="sticky-col" style="min-width:${Math.max(400,months.slice(-12).length*70+160)}px">
          <thead><tr>
            <th style="text-align:left;position:sticky;left:0;z-index:5;background:var(--bg)">${groupCol.header}</th>
            ${months.slice(-12).map(m=>`<th style="text-align:center;min-width:52px">${m}</th>`).join('')}
          </tr></thead>
          <tbody>${allGroups.map(g=>`<tr>
            <td style="font-weight:700;color:var(--text);white-space:nowrap;position:sticky;left:0;background:var(--surface);z-index:2;box-shadow:2px 0 6px rgba(26,26,26,.05)">${g}</td>
            ${months.slice(-12).map(m=>{const v=grpM[g]?.[m]?.count||0;const it=maxVal?v/maxVal:0;const bg=`rgba(255,90,0,${(it*.22+.04).toFixed(2)})`;const fg=it>.6?'#fff':'var(--text2)';return`<td style="text-align:center;padding:4px 6px"><div class="heat-cell" style="background:${bg};color:${fg};min-width:36px">${v||'–'}</div></td>`;}).join('')}
          </tr>`).join('')}</tbody>
        </table>
      </div>`;
    grid.appendChild(card2);
  }
}
function rerenderGrpTrend(){
  const{allGroups,grpM,months}=window._grpTrend||{};if(!allGroups)return;
  const mode=($('grp-tr-mode')||{}).value||'count';
  const isBar=($('grp-tr-type')||{}).value==='bar';
  const datasets=allGroups.map(g=>({label:g,data:months.map(m=>grpM[g]?.[m]?(mode==='amount'&&SCHEMA.hasAmount?grpM[g][m].amount:grpM[g][m].count):0),backgroundColor:isBar?a16(colColor(g),0.8):a16(colColor(g),0.15),borderColor:colColor(g),borderWidth:2,fill:!isBar,tension:.4,pointRadius:isBar?0:3,pointBackgroundColor:colColor(g)}));
  mkChart('ch-grp-trend',isBar?'bar':'line',months,datasets,{_dl:DL_OFF,_stacked:isBar});
}

function renderTrendExtra(){
  const grid=$('g-trend-extra');grid.innerHTML='';
  if(!SCHEMA.dateCol)return;
  const{bucket:yearly,keys:years}=buildTrendBuckets('yearly');
  const{bucket:quarterly,keys:quarters}=buildTrendBuckets('quarterly');
  if(years.length>=2&&SCHEMA.hasAmount){
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#D97706"></span><span class="card-title-text">Year-over-Year</span></div></div><div class="cw ch-lg"><canvas id="ch-yoy"></canvas></div>`;
    grid.appendChild(card);
    const datasets=[{label:'Count',data:years.map(y=>yearly[y].count),backgroundColor:a16('#FF5A00',0.78),borderRadius:6,borderWidth:0},{label:SCHEMA.primaryAmount.header,data:years.map(y=>yearly[y].amounts[SCHEMA.primaryAmount.header]||0),borderColor:'#1A8A3A',borderWidth:2.5,pointRadius:5,pointBackgroundColor:'#1A8A3A',pointBorderColor:'#fff',pointBorderWidth:2,type:'line',backgroundColor:'transparent',yAxisID:'y2',tension:.4}];
    requestAnimationFrame(()=>mkChart('ch-yoy','bar',years,datasets,{_dl:DL_OFF,scales:{x:axS(),y:axS({beginAtZero:true}),y2:{...axS({beginAtZero:true}),position:'right',grid:{display:false}}}}));
  }
  if(quarters.length>=2){
    const card2=document.createElement('div');card2.className='card';
    card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#0891B2"></span><span class="card-title-text">Quarterly Performance</span></div></div><div class="cw ch-lg"><canvas id="ch-qtr"></canvas></div>`;
    grid.appendChild(card2);
    const ds=[{label:'Count',data:quarters.map(q=>quarterly[q].count),backgroundColor:a16('#0891B2',0.72),borderRadius:5,borderWidth:0}];
    if(SCHEMA.hasAmount)ds.push({label:SCHEMA.primaryAmount.header,data:quarters.map(q=>quarterly[q].amounts[SCHEMA.primaryAmount.header]||0),borderColor:'#FF5A00',borderWidth:2.5,pointRadius:5,pointBackgroundColor:'#FF5A00',type:'line',backgroundColor:'transparent',yAxisID:'y2',tension:.4});
    requestAnimationFrame(()=>mkChart('ch-qtr','bar',quarters,ds,{_dl:DL_OFF,scales:{x:axS(),y:axS({beginAtZero:true}),y2:SCHEMA.hasAmount?{...axS({beginAtZero:true}),position:'right',grid:{display:false}}:undefined}}));
  }
}

function renderComparisonRanks(){
  const grid=$('g-cmp-rank');grid.innerHTML='';
  const cols=[SCHEMA.primaryGroup,...SCHEMA.chartCols.filter(c=>c.header!==(SCHEMA.primaryGroup||{}).header)].filter(v=>v!=null&&String(v).trim()!=='').slice(0,3);
  if(!cols.length)return;
  cols.forEach((col,i)=>{
    const amFns=SCHEMA.hasAmount?[[SCHEMA.primaryAmount.header,r=>r[SCHEMA.primaryAmount.header]]]:[];
    const gb=groupBy(FILT,r=>r[col.header],amFns);
    const sorted=Object.entries(gb).sort((a,b)=>SCHEMA.hasAmount?b[1].amounts[SCHEMA.primaryAmount.header]-a[1].amounts[SCHEMA.primaryAmount.header]:b[1].count-a[1].count);
    if(!sorted.length)return;
    const maxVal=SCHEMA.hasAmount?sorted[0][1].amounts[SCHEMA.primaryAmount.header]:sorted[0][1].count;
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:${PAL[i%PAL.length]}"></span><span class="card-title-text">${col.header} Ranking</span></div></div>`
      +sorted.map(([k,v],ri)=>{const val=SCHEMA.hasAmount?v.amounts[SCHEMA.primaryAmount.header]:v.count;const pct=Math.round((val/(maxVal||1))*100);return`<div class="cmp-row"><div class="cmp-rank">#${ri+1}</div><div class="cmp-lbl" title="${k}">${k}</div><div class="cmp-bar-wrap"><div class="cmp-bar" style="width:${pct}%;background:${colColor(k)}"></div></div><div class="cmp-val">${SCHEMA.hasAmount?fmtINR(val):fmtN(val)}</div><div class="cmp-cnt">${v.count}</div></div>`;}).join('');
    grid.appendChild(card);
  });
}

function renderComparisonWaterfall(){
  const grid=$('g-cmp-waterfall');grid.innerHTML='';
  if(!SCHEMA.hasAmount||!SCHEMA.primaryGroup)return;
  const amtCol=SCHEMA.primaryAmount,groupCol=SCHEMA.primaryGroup;
  const gb=groupBy(FILT,r=>r[groupCol.header],[[amtCol.header,r=>r[amtCol.header]]]);
  const sorted=Object.entries(gb).sort((a,b)=>b[1].amounts[amtCol.header]-a[1].amounts[amtCol.header]);
  const total=sorted.reduce((s,[,v])=>s+v.amounts[amtCol.header],0);
  const maxVal=sorted[0]?.[1]?.amounts[amtCol.header]||1;
  const card=document.createElement('div');card.className='card';
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#1A8A3A"></span><span class="card-title-text">Waterfall: ${amtCol.header} by ${groupCol.header}</span></div></div>`
    +sorted.map(([k,v],ri)=>{const val=v.amounts[amtCol.header];const pct=Math.round(val/maxVal*100);const sharePct=(val/total*100).toFixed(1);const prev=ri>0?sorted[ri-1][1].amounts[amtCol.header]:null;const delta=prev?Math.round((val-prev)/prev*100):null;return`<div class="wf-row"><div class="wf-label">${k}</div><div class="wf-bar-wrap"><div class="wf-bar" style="width:${pct}%;background:${colColor(k)}"></div></div><div class="wf-val">${fmtINR(val)}</div>${delta!==null?`<div class="wf-delta ${delta>=0?'pos':'neg'}">${delta>=0?'▲':'▼'}${Math.abs(delta)}%</div>`:`<div class="wf-delta" style="color:var(--text3)">${sharePct}%</div>`}</div>`;}).join('');
  grid.appendChild(card);
  if(SCHEMA.amountCols.length>=2){
    const amtCol2=SCHEMA.amountCols[1];
    const gb2=groupBy(FILT,r=>r[groupCol.header],[[amtCol2.header,r=>r[amtCol2.header]]]);
    const sorted2=Object.entries(gb2).sort((a,b)=>b[1].amounts[amtCol2.header]-a[1].amounts[amtCol2.header]);
    const max2=sorted2[0]?.[1]?.amounts[amtCol2.header]||1;
    const card2=document.createElement('div');card2.className='card';
    card2.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#CC3300"></span><span class="card-title-text">Waterfall: ${amtCol2.header}</span></div></div>`
      +sorted2.map(([k,v])=>{const val=v.amounts[amtCol2.header];const pct=Math.round(val/max2*100);return`<div class="wf-row"><div class="wf-label">${k}</div><div class="wf-bar-wrap"><div class="wf-bar" style="width:${pct}%;background:${colColor(k)}"></div></div><div class="wf-val">${fmtINR(val)}</div></div>`;}).join('');
    grid.appendChild(card2);
  }
}

function renderComparisonStacked(){
  const grid=$('g-cmp-stacked');grid.innerHTML='';
  const mainCol=SCHEMA.primaryGroup||SCHEMA.chartCols[0];
  const secCol=SCHEMA.chartCols.find(c=>c.header!==(mainCol||{}).header&&c.uniq.length<=10);
  if(!mainCol||!secCol)return;
  const allGroups=[...new Set(FILT.map(r=>r[mainCol.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const allSecs=[...new Set(FILT.map(r=>r[secCol.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const card=document.createElement('div');card.className='card';
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#FF5A00"></span><span class="card-title-text">${mainCol.header} × ${secCol.header} Breakdown</span></div><div class="card-ctl"><select class="card-sel" id="stk-mode" onchange="rerenderStacked()"><option value="stacked">Stacked</option><option value="grouped">Grouped</option><option value="pct">100%</option></select><select class="card-sel" id="stk-metric" onchange="rerenderStacked()"><option value="count">Count</option>${SCHEMA.hasAmount?`<option value="amount">${SCHEMA.primaryAmount.header}</option>`:''}</select></div></div><div class="cw ch-xl"><canvas id="ch-stacked"></canvas></div>`;
  grid.appendChild(card);
  window._stkData={allGroups,allSecs,mainCol,secCol};rerenderStacked();
}
function rerenderStacked(){
  const{allGroups,allSecs,mainCol,secCol}=window._stkData||{};if(!allGroups)return;
  const mode=($('stk-mode')||{}).value||'stacked';
  const metric=($('stk-metric')||{}).value||'count';
  const stacked=mode!=='grouped';
  const datasets=allSecs.map((s,i)=>({label:s,data:allGroups.map(g=>{const rows=FILT.filter(r=>r[mainCol.header]===g&&r[secCol.header]===s);return metric==='amount'&&SCHEMA.hasAmount?rows.reduce((t,r)=>t+(r[SCHEMA.primaryAmount.header]||0),0):rows.length;}),backgroundColor:a16(colColor(s),0.82),borderRadius:3}));
  if(mode==='pct'){
    const normDs=datasets.map(d=>({...d,data:d.data.map((v,i)=>{const tot=datasets.reduce((s,ds)=>s+ds.data[i],0);return tot?Math.round(v/tot*100):0;})}));
    mkChart('ch-stacked','bar',allGroups,normDs,{_dl:DL_INSIDE,_stacked:true,scales:{x:{...axS(),stacked:true},y:{...axS({beginAtZero:true}),stacked:true,max:100,ticks:{...axS().ticks,callback:v=>v+'%'}}}});
  }else{
    mkChart('ch-stacked','bar',allGroups,datasets,{_dl:mode==='stacked'?DL_INSIDE:makeDL_BAR('n'),_stacked:stacked});
  }
}

function renderCrossTabs(){
  const grid=$('g-cross-main');grid.innerHTML='';
  const catCols=SCHEMA.chartCols.filter(c=>c.uniq.length<=25);
  if(catCols.length<2)return;
  const pairs=[];
  for(let i=0;i<Math.min(catCols.length,5);i++){for(let j=i+1;j<Math.min(catCols.length,5);j++){pairs.push([catCols[i],catCols[j]]);if(pairs.length>=4)break;}if(pairs.length>=4)break;}
  pairs.forEach(([colA,colB],pi)=>{
    const rowKeys=[...new Set(FILT.map(r=>r[colA.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
    const colKeys=[...new Set(FILT.map(r=>r[colB.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
    const data={};
    FILT.forEach(r=>{const rk=r[colA.header],ck=r[colB.header];if(!rk||!ck)return;if(!data[rk])data[rk]={};data[rk][ck]=(data[rk][ck]||0)+1;});
    const maxVal=Math.max(1,...rowKeys.flatMap(rk=>colKeys.map(ck=>data[rk]?.[ck]||0)));
    const isWide=colKeys.length>6;
    const minW=Math.max(400,colKeys.length*64+160);
    const card=document.createElement('div');card.className='card';
    card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:${PAL[(pi+3)%PAL.length]}"></span><span class="card-title-text">${colA.header} × ${colB.header}</span></div>${isWide?`<span class="scroll-badge">↔ scroll</span>`:''}</div>
      <div class="tbl-wrap" style="max-height:340px"><table class="sticky-col" style="min-width:${minW}px">
        <thead><tr><th style="text-align:left;min-width:110px;position:sticky;left:0;z-index:5;background:var(--bg)">${colA.header}</th>${colKeys.map(c=>`<th class="ct-th" title="${c}" style="min-width:58px">${c.length>8?c.slice(0,8)+'…':c}</th>`).join('')}<th style="min-width:52px;text-align:center">Total</th></tr></thead>
        <tbody>${rowKeys.map(rk=>{const rowTot=colKeys.reduce((s,ck)=>s+(data[rk]?.[ck]||0),0);return`<tr><td style="font-weight:700;color:var(--text);white-space:nowrap;position:sticky;left:0;background:var(--surface);z-index:2;box-shadow:2px 0 6px rgba(26,26,26,.05)">${rk}</td>${colKeys.map(ck=>{const v=data[rk]?.[ck]||0;const it=maxVal?v/maxVal:0;const bg=`rgba(255,90,0,${(it*.22+.04).toFixed(2)})`;return`<td style="text-align:center;padding:5px 6px"><div class="heat-cell" style="background:${bg};color:${it>.55?'#fff':'var(--text2)'}">${v||'–'}</div></td>`;}).join('')}<td style="text-align:center;font-family:var(--f3);font-size:10px;font-weight:700;color:var(--text)">${rowTot}</td></tr>`;}).join('')}</tbody>
      </table></div>`;
    grid.appendChild(card);
  });
}

function renderCrossTabAmount(){
  const grid=$('g-cross-amt');grid.innerHTML='';
  if(!SCHEMA.hasAmount||!SCHEMA.primaryGroup||SCHEMA.chartCols.length<1)return;
  const groupCol=SCHEMA.primaryGroup;
  const typeCol=SCHEMA.chartCols.find(c=>c.header!==groupCol.header&&c.uniq.length<=20);
  if(!typeCol)return;
  const rowKeys=[...new Set(FILT.map(r=>r[groupCol.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const colKeys=[...new Set(FILT.map(r=>r[typeCol.header]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const data={};
  FILT.forEach(r=>{const rk=r[groupCol.header],ck=r[typeCol.header];if(!rk||!ck)return;if(!data[rk])data[rk]={};data[rk][ck]=(data[rk][ck]||0)+(r[SCHEMA.primaryAmount.header]||0);});
  const maxVal=Math.max(1,...rowKeys.flatMap(rk=>colKeys.map(ck=>data[rk]?.[ck]||0)));
  const isWide=colKeys.length>5;
  const minW=Math.max(500,colKeys.length*100+170);
  const card=document.createElement('div');card.className='card';
  card.innerHTML=`<div class="card-hdr"><div class="card-title"><span class="card-dot" style="background:#1A8A3A"></span><span class="card-title-text">${groupCol.header} × ${typeCol.header} (${SCHEMA.primaryAmount.header})</span></div>${isWide?`<span class="scroll-badge">↔ scroll</span>`:''}</div>
    <div class="tbl-wrap" style="max-height:380px"><table class="sticky-col" style="min-width:${minW}px">
      <thead><tr><th style="text-align:left;min-width:120px;position:sticky;left:0;z-index:5;background:var(--bg)">${groupCol.header}</th>${colKeys.map(c=>`<th class="ct-th" title="${c}" style="min-width:88px">${c.length>11?c.slice(0,11)+'…':c}</th>`).join('')}<th style="min-width:88px;text-align:center">Total</th></tr></thead>
      <tbody>${rowKeys.map(rk=>{const rowTot=colKeys.reduce((s,ck)=>s+(data[rk]?.[ck]||0),0);return`<tr><td style="font-weight:700;color:var(--text);white-space:nowrap;position:sticky;left:0;background:var(--surface);z-index:2;box-shadow:2px 0 6px rgba(26,26,26,.05)">${rk}</td>${colKeys.map(ck=>{const v=data[rk]?.[ck]||0;const it=maxVal?v/maxVal:0;const bg=`rgba(26,138,58,${(it*.22+.04).toFixed(2)})`;return`<td style="text-align:center;padding:4px 6px"><div class="heat-cell" style="min-width:72px;width:auto;padding:0 6px;background:${bg};color:${it>.55?'#fff':'var(--text2)'};font-size:9px">${v?fmtINR(v):'–'}</div></td>`;}).join('')}<td style="text-align:center;font-family:var(--f3);font-size:10px;font-weight:700;color:var(--text)">${fmtINR(rowTot)}</td></tr>`;}).join('')}</tbody>
    </table></div>`;
  grid.appendChild(card);
}

function setupPivotSelectors(){
  const catOpts=SCHEMA.chartCols.map(c=>`<option value="${c.header}">${c.header}</option>`).join('');
  const amtOpts=SCHEMA.hasAmount?SCHEMA.amountCols.map(c=>`<option value="${c.header}">${c.header}</option>`).join(''):`<option value="count">Count</option>`;
  const pRow=$('pv-row'),pCol=$('pv-col'),pVal=$('pv-val');
  if(pRow)pRow.innerHTML=catOpts||'<option>No category cols</option>';
  if(pCol){pCol.innerHTML=catOpts;const opts=pCol.querySelectorAll('option');if(opts[1])opts[1].selected=true;}
  if(pVal)pVal.innerHTML=amtOpts;
}
function renderPivot(){
  const rowField=($('pv-row')||{}).value,colField=($('pv-col')||{}).value,valField=($('pv-val')||{}).value,agg=($('pv-agg')||{}).value||'sum';
  if(!rowField||!colField||!valField||rowField===colField){$('pivot-wrap').innerHTML='<div style="padding:32px;color:var(--text3);font-size:13px;text-align:center;font-family:var(--f2)">🔄 <strong>Mix it up!</strong> Pick two <em>different</em> fields for rows & columns to unlock your pivot magic.</div>';return;}
  const rowKeys=[...new Set(FILT.map(r=>r[rowField]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const colKeys=[...new Set(FILT.map(r=>r[colField]).filter(v=>v!=null&&String(v).trim()!==''))].sort();
  const data={};
  FILT.forEach(r=>{const rk=r[rowField],ck=r[colField];if(!rk||!ck)return;if(!data[rk])data[rk]={};if(!data[rk][ck])data[rk][ck]=[];const v=valField==='count'?1:(parseFloat(r[valField])||0);data[rk][ck].push(v);});
  const allVals=rowKeys.flatMap(rk=>colKeys.map(ck=>data[rk]?.[ck]?.length?pivotAgg(data[rk][ck],agg):0));
  const maxVal=Math.max(1,...allVals);
  const fmt=v=>agg==='count'?fmtN(Math.round(v)):(SCHEMA.hasAmount?fmtINR(v):fmtN(Math.round(v)));
  const minW=Math.max(500,colKeys.length*88+200);
  $('pivot-wrap').innerHTML=`<table style="min-width:${minW}px">
    <thead><tr><th style="text-align:left;position:sticky;left:0;z-index:5;background:var(--bg);min-width:130px">↓ ${rowField} / ${colField} →</th>${colKeys.map(c=>`<th class="ct-th" title="${c}" style="min-width:80px">${c.length>12?c.slice(0,12)+'…':c}</th>`).join('')}<th style="min-width:80px">Total</th></tr></thead>
    <tbody>${rowKeys.map(rk=>{const rowArr=colKeys.flatMap(ck=>data[rk]?.[ck]||[]);const rowTotal=rowArr.length?pivotAgg(rowArr,agg):0;return`<tr><td class="row-hdr" style="position:sticky;left:0;z-index:2;box-shadow:2px 0 6px rgba(26,26,26,.06)">${rk}</td>${colKeys.map(ck=>{const arr=data[rk]?.[ck]||[];const v=arr.length?pivotAgg(arr,agg):0;const it=maxVal?v/maxVal:0;const bg=`rgba(255,90,0,${(it*.2+.03).toFixed(2)})`;return`<td><div class="heat-cell" style="background:${bg};color:${it>.55?'#fff':'var(--text2)'};">${v?fmt(v):'–'}</div></td>`;}).join('')}<td class="total-cell">${rowTotal?fmt(rowTotal):'–'}</td></tr>`;}).join('')}</tbody>
  </table>`;
  const groups=rowKeys;
  const datasets=colKeys.map((ck,i)=>({label:ck,data:rowKeys.map(rk=>{const arr=data[rk]?.[ck]||[];return arr.length?pivotAgg(arr,agg):0;}),backgroundColor:a16(PAL[i%PAL.length],0.78),borderRadius:5,borderWidth:0}));
  requestAnimationFrame(()=>mkChart('ch-pivot','bar',groups,datasets,{_dl:DL_INSIDE,_stacked:true}));
}

function renderProfile(){
  const missingTotal=SCHEMA.columns.reduce((s,c)=>s+c.missingCount,0);
  $('g-prof-kpi').innerHTML=[
    {label:'Total Columns',value:SCHEMA.columns.length,icon:'🗂',c:'#FF5A00'},
    {label:'Total Rows',value:fmtN(ALL.length),icon:'📄',c:'#CC3300'},
    {label:'Missing Values',value:fmtN(missingTotal),icon:'❓',c:'#D97706'},
    {label:'Numeric Columns',value:SCHEMA.amountCols.length+SCHEMA.numericCols.length,icon:'🔢',c:'#1A8A3A'},
  ].map(k=>`<div class="kpi" style="--kc:${k.c}"><div class="kpi-accent" style="background:${k.c}"></div><div class="kpi-icon">${k.icon}</div><div class="kpi-label">${k.label}</div><div class="kpi-value">${k.value}</div></div>`).join('');
  const grid=$('g-prof-cols');grid.innerHTML='';
  const rp={date:'#0891B2',amount:'#1A8A3A',numeric:'#FF5A00',id:'var(--text3)',group:'#D97706',person:'#CC3300',category:'#D97706',type:'#7C3AED',status:'#DC2626',name:'#15803D',text:'var(--text3)'};
  SCHEMA.columns.filter(c=>!['text'].includes(c.role)).forEach(col=>{
    const card=document.createElement('div');card.className='card';
    const rc=rp[col.role]||'var(--text3)';
    const isNum=col.role==='amount'||col.role==='numeric';
    const statsHtml=isNum?`<div class="prof-stats"><span class="prof-stat">Sum: <strong>${fmtN(Math.round(col.sumNum))}</strong></span><span class="prof-stat">Avg: <strong>${fmtN(Math.round(col.avgNum))}</strong></span><span class="prof-stat">Max: <strong>${fmtN(Math.round(col.maxNum))}</strong></span><span class="prof-stat">Std: <strong>${fmtN(Math.round(col.stdNum))}</strong></span></div>`:`<div class="prof-stats"><span class="prof-stat">Unique: <strong>${col.uniq.length}</strong></span><span class="prof-stat">Missing: <strong>${col.missingCount}</strong></span><span class="prof-stat">Fill: <strong>${Math.round((1-col.missingCount/Math.max(ALL.length,1))*100)}%</strong></span></div>`;
    const topFreq=col.uniq.length>0&&col.uniq.length<=50?Object.entries(groupBy(FILT,r=>r[col.header])).sort((a,b)=>b[1].count-a[1].count).slice(0,6):[];
    const maxFreq=topFreq[0]?.[1]?.count||1;
    const freqHtml=topFreq.length?`<div class="prof-freq">${topFreq.map(([k,v])=>`<div class="prof-freq-row"><div class="prof-freq-lbl" title="${k}">${k||'(blank)'}</div><div class="prof-freq-bar"><div class="prof-freq-fill" style="width:${Math.round(v.count/maxFreq*100)}%;background:${rc}"></div></div><div class="prof-freq-val">${v.count}</div></div>`).join('')}</div>`:'';
    card.innerHTML=`<div class="prof-col"><div class="prof-col-name">${col.header}<span class="prof-role" style="background:rgba(26,26,26,.07);color:${rc}">${col.role}</span></div>${statsHtml}${freqHtml}</div>`;
    grid.appendChild(card);
  });
}

function getVisibleCols(){const mode=($('tbl-col-mode')||{}).value||'key';return mode==='all'?SCHEMA.columns.filter(c=>c.header):SCHEMA.columns.filter(c=>c.header&&!['text'].includes(c.role));}
function renderTable(){
  const vc=getVisibleCols();
  $('tbl-head').innerHTML=vc.map((c,i)=>`<th onclick="sortTable(${i})" style="${c.role==='amount'?'text-align:right':''}min-width:${c.role==='amount'?'110':'80'}px">${c.header}</th>`).join('');
  renderTableRows(vc);
}
function changePageSize(){const v=$('tbl-page-size').value;PG=v==='all'?Infinity:parseInt(v);PAGE=1;renderTableRows(getVisibleCols());}
function sortTable(idx){
  const vc=getVisibleCols();const col=vc[idx];if(!col)return;
  if(SORT.col===col.header)SORT.dir*=-1;else{SORT.col=col.header;SORT.dir=1;}
  document.querySelectorAll('#tbl-head th').forEach((th,i)=>{th.classList.remove('asc','desc');if(i===idx)th.classList.add(SORT.dir===1?'asc':'desc');});
  FILT.sort((a,b)=>{const av=a[col.header]??'',bv=b[col.header]??'';if(col.role==='amount'||col.role==='numeric')return(parseFloat(av)-parseFloat(bv))*SORT.dir;if(!isNaN(parseFloat(av))&&!isNaN(parseFloat(bv)))return(parseFloat(av)-parseFloat(bv))*SORT.dir;return String(av).localeCompare(String(bv))*SORT.dir;});
  PAGE=1;renderTableRows(getVisibleCols());
}
function renderTableRows(vc){
  const total=FILT.length;const pgSize=PG===Infinity?total:PG;const tp=Math.ceil(total/pgSize);
  const slice=FILT.slice((PAGE-1)*pgSize,PAGE*pgSize);
  $('rec-count').textContent=`Showing ${slice.length} of ${total} records`;
  $('tbl-body').innerHTML=slice.map(r=>`<tr>${vc.map((col,ci)=>{const val=r[col.header];
    if(col.role==='amount')return`<td style="text-align:right;min-width:110px"><span class="amt-cell">${val!=null&&val!==''?fmtINR(val):'-'}</span></td>`;
    if(col.role==='numeric')return`<td style="min-width:80px"><span class="num-cell">${val!=null&&val!==''?fmtN(val):'-'}</span></td>`;
    if(col.role==='date')return`<td style="min-width:90px"><span class="date-cell">${val||'-'}</span></td>`;
    if(col.isCat)return`<td style="min-width:80px"><span class="badge ${badgeCls(val)}">${val||'-'}</span></td>`;
    if(col.role==='name')return`<td style="max-width:180px;min-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${val||''}">${val||'-'}</td>`;
    return`<td style="min-width:80px">${val!=null?val:'-'}</td>`;
  }).join('')}</tr>`).join('');
  renderPag(tp);
}
function renderPag(tp){
  const pag=$('pag');if(tp<=1){pag.innerHTML='';return;}
  let h='';
  for(let i=1;i<=tp;i++){if(i===1||i===tp||Math.abs(i-PAGE)<=2)h+=`<button class="pbtn${i===PAGE?' on':''}" onclick="goPage(${i})">${i}</button>`;else if(Math.abs(i-PAGE)===3)h+=`<span class="pdots">…</span>`;}
  pag.innerHTML=h;
}
function goPage(p){PAGE=p;renderTableRows(getVisibleCols());$('tbl-body')?.closest('.card')?.scrollIntoView({behavior:'smooth',block:'start'});}

function exportCSV(){
  const vc=SCHEMA.columns.filter(c=>c.header&&!['text'].includes(c.role));
  const rows=['\uFEFF'+vc.map(c=>'"'+c.header+'"').join(','),...FILT.map(r=>vc.map(c=>{const v=r[c.header];return v!=null?'"'+String(v).replace(/"/g,'""')+'"':'""';}).join(','))];
  const a=document.createElement('a');a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(rows.join('\n'));
  a.download='myagenci_dashboard_'+new Date().toISOString().slice(0,10)+'.csv';a.click();
}

/* ══ AI INSIGHTS ══ */
async function loadAIInsights(){
  const body=$('aip-body');
  body.innerHTML='<div class="aip-loading"><div class="aip-spin"></div>AI is analysing your data…<br><small style="font-size:10px;color:var(--text3);display:block;margin-top:6px;font-family:var(--f3)">Finding patterns, anomalies & opportunities</small></div>';
  const summary=buildDataSummary();
  try{
    const res=await fetch('https://api.anthropic.com/v1/messages',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({model:'claude-sonnet-4-20250514',max_tokens:1200,system:`You are a senior data analyst. Analyse the dataset summary and return ONLY a valid JSON array (no markdown, no explanation) of 7-9 insight objects. Each: {type:"anomaly"|"trend"|"opportunity"|"risk"|"highlight", title:string (max 7 words), text:string (2 sentences max, specific numbers), value:string (optional key metric)}. Use actual numbers.`,messages:[{role:'user',content:`Dataset:\n\n${summary}`}]})});
    const data=await res.json();const raw=data.content?.[0]?.text||'[]';
    let insights;try{insights=JSON.parse(raw.replace(/```json?|```/g,'').trim());}catch{insights=[];}
    renderInsights(insights);renderAIStrips(insights.slice(0,3));
  }catch{
    const fb=genFallbackInsights();renderInsights(fb);renderAIStrips(fb.slice(0,2));
  }
}
function buildDataSummary(){
  const lines=[`Dataset: ${ALL.length} records, ${SCHEMA.columns.length} columns`,`Columns: ${SCHEMA.columns.map(c=>`${c.header}(${c.role})`).join(', ')}`];
  if(SCHEMA.hasAmount)SCHEMA.amountCols.forEach(col=>{const vals=ALL.map(r=>r[col.header]||0).sort((a,b)=>b-a);const tot=vals.reduce((s,v)=>s+v,0);lines.push(`${col.header}: Total=${fmtINR(tot)}, Avg=${fmtINR(tot/ALL.length)}, Max=${fmtINR(vals[0])}`);});
  SCHEMA.chartCols.slice(0,6).forEach(col=>{const gb=groupBy(ALL,r=>r[col.header]);const sorted=Object.entries(gb).sort((a,b)=>b[1].count-a[1].count);const top=sorted.slice(0,4).map(([k,v])=>`${k}:${v.count}`).join(', ');lines.push(`${col.header}: ${top}${sorted.length>4?` +${sorted.length-4} more`:''}`);});
  if(SCHEMA.dateCol){const m={};ALL.forEach(r=>{const mn=r['__month'];if(mn){if(!m[mn])m[mn]={count:0};m[mn].count++;}});const mk=sortMonths(Object.keys(m));if(mk.length>1)lines.push(`Monthly: ${mk.slice(-6).map(mn=>`${mn}:${m[mn].count}`).join(', ')}`);}
  return lines.join('\n');
}
function genFallbackInsights(){
  const ins=[];
  if(SCHEMA.hasAmount){
    const sorted=[...ALL].sort((a,b)=>(b[SCHEMA.primaryAmount.header]||0)-(a[SCHEMA.primaryAmount.header]||0));
    const tot=ALL.reduce((s,r)=>s+(r[SCHEMA.primaryAmount.header]||0),0);
    const topAmt=sorted[0]?.[SCHEMA.primaryAmount.header]||0;
    ins.push({type:'highlight',title:'Highest Value Record',text:`The largest single record is ${fmtINR(topAmt)}, representing ${(topAmt/tot*100).toFixed(1)}% of total ${SCHEMA.primaryAmount.header}.`,value:fmtINR(topAmt)});
    const top10=sorted.slice(0,Math.ceil(ALL.length*.1)).reduce((s,r)=>s+(r[SCHEMA.primaryAmount.header]||0),0);
    const conc=Math.round(top10/tot*100);
    if(conc>60)ins.push({type:'risk',title:'Revenue Concentration Risk',text:`Top 10% of records hold ${conc}% of total value — high concentration is a risk.`,value:conc+'%'});
  }
  if(SCHEMA.primaryPerson){
    const amFns=SCHEMA.hasAmount?[[SCHEMA.primaryAmount.header,r=>r[SCHEMA.primaryAmount.header]]]:[];
    const gb=groupBy(ALL,r=>r[SCHEMA.primaryPerson.header],amFns);
    const sorted=Object.entries(gb).sort((a,b)=>SCHEMA.hasAmount?b[1].amounts[SCHEMA.primaryAmount.header]-a[1].amounts[SCHEMA.primaryAmount.header]:b[1].count-a[1].count);
    if(sorted.length>1){const[top,sec]=sorted;const tv=SCHEMA.hasAmount?top[1].amounts[SCHEMA.primaryAmount.header]:top[1].count;const sv=SCHEMA.hasAmount?sec[1].amounts[SCHEMA.primaryAmount.header]:sec[1].count;ins.push({type:'trend',title:'Top Performer Lead',text:`${top[0]} leads with ${SCHEMA.hasAmount?fmtINR(tv):tv+' records'}, ${Math.round((tv/sv-1)*100)}% ahead of ${sec[0]}.`,value:SCHEMA.hasAmount?fmtINR(tv):String(tv)});}
  }
  SCHEMA.chartCols.slice(0,3).forEach(col=>{const gb=groupBy(ALL,r=>r[col.header]);const sorted=Object.entries(gb).sort((a,b)=>b[1].count-a[1].count);if(sorted.length)ins.push({type:'highlight',title:`${col.header} Leader`,text:`"${sorted[0][0]}" is the top ${col.header} with ${sorted[0][1].count} records (${Math.round(sorted[0][1].count/ALL.length*100)}% share).`,value:sorted[0][0]});});
  return ins.slice(0,7);
}
function renderInsights(insights){
  const body=$('aip-body');if(!insights?.length){body.innerHTML='<div class="aip-loading">No insights generated.</div>';return;}
  const clrs={anomaly:'#DC2626',trend:'#FF5A00',opportunity:'#1A8A3A',risk:'#D97706',highlight:'#CC3300'};
  body.innerHTML=insights.map(ins=>`<div class="ins-card"><div class="ins-type" style="color:${clrs[ins.type]||'#FF5A00'}">▸ ${(ins.type||'insight').toUpperCase()}</div><div class="ins-text"><strong>${ins.title||''}</strong>${ins.title?' — ':''}${ins.text||''}</div>${ins.value?`<div class="ins-val">${ins.value}</div>`:''}</div>`).join('');
}
function renderAIStrips(insights){
  const wrap=$('ai-strips-wrap');if(!insights?.length){wrap.innerHTML='';return;}
  const icons={anomaly:'⚠️',trend:'📈',opportunity:'💡',risk:'🔴',highlight:'🏆'};
  wrap.innerHTML=insights.map(ins=>`<div class="ai-strip"><span class="ai-strip-icon">${icons[ins.type]||'🧠'}</span><div class="ai-strip-text"><strong>${ins.title}</strong> — ${ins.text}</div></div>`).join('');
}
function toggleAIPanel(){AI_OPEN=!AI_OPEN;$('ai-panel').classList.toggle('open',AI_OPEN);$('dash').classList.toggle('panel-open',AI_OPEN);}

/* ══ PDF EXPORT ══ */
async function exportPDF(){
  const btn=$('btn-export');
  if(!btn||btn.disabled)return;
  btn.disabled=true;
  $('pdf-overlay').classList.add('show');
  const TAB_ORDER=[{id:'overview',label:'Overview'},{id:'charts',label:'Charts'},{id:'distribution',label:'Distribution'},{id:'trend',label:'Trend'},{id:'comparison',label:'Compare'},{id:'crosstab',label:'Cross-Tab'},{id:'pivot',label:'Pivot'},{id:'profile',label:'Profile'},{id:'table',label:'Table'}];
  const savedTab=ACTIVE_TAB;
  function expandScrollers(root){const saves=[];root.querySelectorAll('.tbl-wrap,.pivot-wrap,[class$="-wrap"]').forEach(el=>{saves.push({el,ov:el.style.overflow,oy:el.style.overflowY,ox:el.style.overflowX,mh:el.style.maxHeight});el.style.overflow=el.style.overflowY=el.style.overflowX='visible';el.style.maxHeight='none';});return saves;}
  function restoreScrollers(saves){saves.forEach(({el,ov,oy,ox,mh})=>{el.style.overflow=ov;el.style.overflowY=oy;el.style.overflowX=ox;el.style.maxHeight=mh;});}
  const HIDE_IDS=['pdf-overlay','ai-panel','sheet-modal'];
  function ghost(ids,hide){ids.forEach(id=>{const e=$(id);if(e)e.style.visibility=hide?'hidden':'';});}
  function expandTable(){
    const vc=SCHEMA.columns.filter(c=>c.header&&c.role!=='text');
    $('tbl-head').innerHTML=vc.map(c=>`<th>${c.header}</th>`).join('');
    $('tbl-body').innerHTML=FILT.map(r=>`<tr>${vc.map(col=>{const v=r[col.header];if(col.role==='amount')return`<td style="font-family:monospace;font-size:10px;font-weight:700;color:#1A8A3A">${v!=null?fmtINR(v):'-'}</td>`;if(col.isCat)return`<td><span style="font-size:10px">${v||'-'}</span></td>`;return`<td style="font-size:10px">${v!=null?v:'-'}</td>`;}).join('')}</tr>`).join('');
    $('pag').innerHTML='';$('rec-count').textContent=`${FILT.length} total records`;
  }
  try{
    const{jsPDF}=window.jspdf;
    const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
    const PW=210,PH=297,M=8,IW=PW-M*2,HDR=11,BODY_H=PH-M*2-HDR;
    let firstPage=true;
    for(let ti=0;ti<TAB_ORDER.length;ti++){
      const{id,label}=TAB_ORDER[ti];
      $('pdf-lbl').textContent=`Capturing ${label}  (${ti+1} / ${TAB_ORDER.length})…`;
      switchTab(null,id);
      if(id==='table')expandTable();
      await new Promise(r=>setTimeout(r,1500));
      const tabEl=$(`tv-${id}`);if(!tabEl)continue;
      const saves=expandScrollers(tabEl);ghost(HIDE_IDS,true);
      await new Promise(r=>setTimeout(r,120));
      let canvas;
      try{canvas=await html2canvas(tabEl,{scale:1.8,useCORS:true,allowTaint:true,backgroundColor:'#ffffff',scrollX:0,scrollY:0,x:0,y:0,width:Math.max(tabEl.scrollWidth,1260),height:Math.max(tabEl.scrollHeight,100),windowWidth:Math.max(tabEl.scrollWidth,1260),windowHeight:Math.max(tabEl.scrollHeight,100),logging:false});}
      catch(e){console.warn('Capture error',id,e);restoreScrollers(saves);ghost(HIDE_IDS,false);continue;}
      restoreScrollers(saves);ghost(HIDE_IDS,false);
      if(!canvas||canvas.width===0||canvas.height===0)continue;
      const pxPerMm=canvas.width/IW,totalH_mm=canvas.height/pxPerMm,pages=Math.ceil(totalH_mm/BODY_H);
      for(let p=0;p<pages;p++){
        if(!firstPage)pdf.addPage();firstPage=false;
        // if(p===0){pdf.setFillColor(255,90,0);pdf.roundedRect(M,M,IW,HDR-0.5,2,2,'F');pdf.setFontSize(9);pdf.setFont('helvetica','bold');pdf.setTextColor(255,255,255);pdf.text(label,M+4,M+7);pdf.setFontSize(7);pdf.setFont('helvetica','normal');pdf.text(new Date().toLocaleDateString('en-IN'),PW-M-2,M+7,{align:'right'});}
        if(p===0){
  // Header background
  pdf.setFillColor(255,90,0);
  pdf.roundedRect(M,M,IW,HDR+2,2,2,'F');

  // Load and draw Magi logo on the left
  try{
    const logoImg=await new Promise((res,rej)=>{
      const img=new Image();
      img.crossOrigin='anonymous';
      img.onload=()=>res(img);
      img.onerror=rej;
      img.src='/images/magi.png';
    });
    const logoCanvas=document.createElement('canvas');
    const logoH=28,logoW=Math.round(logoImg.naturalWidth*(logoH/logoImg.naturalHeight));
    logoCanvas.width=logoW*2;logoCanvas.height=logoH*2;
    const lctx=logoCanvas.getContext('2d');
    // White background to knock out black logo pixels — invert to white
    lctx.drawImage(logoImg,0,0,logoW*2,logoH*2);
    const logoDataUrl=logoCanvas.toDataURL('image/png');
    const logoMmH=7,logoMmW=logoMmH*(logoW/logoH);
    pdf.addImage(logoDataUrl,'PNG',M+2,M+1.5,logoMmW,logoMmH);
  }catch(e){
    // Fallback: just text if logo fails
    pdf.setFontSize(9);pdf.setFont('helvetica','bold');pdf.setTextColor(255,255,255);
    pdf.text('Magi',M+3,M+7);
  }

  // Tab label — centered
  pdf.setFontSize(9);pdf.setFont('helvetica','bold');pdf.setTextColor(255,255,255);
  pdf.text(label,PW/2,M+7,{align:'center'});

  // Date — right aligned
  pdf.setFontSize(7);pdf.setFont('helvetica','normal');pdf.setTextColor(255,255,255);
  pdf.text(new Date().toLocaleDateString('en-IN'),PW-M-2,M+7,{align:'right'});
}
        const yStart=M+(p===0?HDR:0),availMm=PH-M-yStart,srcY_px=p*BODY_H*pxPerMm,srcH_px=Math.min(availMm*pxPerMm,canvas.height-srcY_px);
        if(srcH_px<2)break;
        const sl=document.createElement('canvas');sl.width=canvas.width;sl.height=Math.ceil(srcH_px);
        sl.getContext('2d').drawImage(canvas,0,srcY_px,canvas.width,srcH_px,0,0,canvas.width,srcH_px);
        pdf.addImage(sl.toDataURL('image/jpeg',0.87),'JPEG',M,yStart,IW,Math.min(sl.height/pxPerMm,availMm));
        pdf.setFontSize(6.5);pdf.setFont('helvetica','normal');pdf.setTextColor(136,136,136);
        pdf.text(`${label}  ·  page ${p+1} of ${pages}`,M,PH-3.5);
        pdf.text('Magi — Smart Dashboard',PW-M,PH-3.5,{align:'right'});
      }
    }
    pdf.save('Magi_Dashboard_'+new Date().toISOString().slice(0,10)+'.pdf');
  }catch(e){console.error('exportPDF error:',e);alert('PDF export failed: '+e.message);}
  finally{
    renderTable();switchTab(null,savedTab);btn.disabled=false;
    $('pdf-overlay').classList.remove('show');$('pdf-lbl').textContent='Generating PDF…';
    ghost(HIDE_IDS,false);
  }
}

/* ══ STATIC FILE AUTO-LOAD ══ */
let _slDotT = null, _slDotI = 0;

function showStaticLoader() {
  const el = $('static-loader');
  if (el) { el.style.display = 'flex'; }
  // Animate dots
  _slDotI = 0;
  _slDotT = setInterval(() => {
    _slDotI = (_slDotI + 1) % 3;
    [1,2,3].forEach(n => {
      const d = $(`sl-dot-${n}`);
      if (d) d.style.opacity = (n - 1) === _slDotI ? '1' : '.3';
    });
  }, 500);
}

function hideStaticLoader() {
  const el = $('static-loader');
  if (el) {
    el.style.opacity = '0';
    el.style.transition = 'opacity .4s ease';
    setTimeout(() => { el.style.display = 'none'; el.style.opacity = ''; el.style.transition = ''; }, 420);
  }
  clearInterval(_slDotT);
}

function setLoaderStep(text, pct) {
  const s = $('sl-step'); if (s) s.textContent = text;
  const b = $('sl-progress-bar'); if (b) b.style.width = pct + '%';
}

async function autoLoadStaticFile() {
  $('upload-screen').style.display = 'none';
  showStaticLoader();
  setLoaderStep('Fetching Excel file…', 8);

  try {
    const res = await fetch('/demo-excel-file/Production Clarity Dashboard.xlsx');
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    setLoaderStep('Reading file data…', 20);
    const blob = await res.blob();
    const file = new File([blob], 'Production Clarity Dashboard.xlsx', {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    });

    setLoaderStep('Uploading to server…', 35);
    const fd = new FormData();
    fd.append('excel_file', file);

    const uploadRes = await fetch('/upload-multi', {
      method: 'POST',
      body: fd,
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      }
    });

    setLoaderStep('Parsing sheet structure…', 52);
    let json;
    try { json = await uploadRes.json(); } catch { throw new Error(`Server error ${uploadRes.status}`); }

    if (!uploadRes.ok || !json.success) throw new Error(json.message || `HTTP ${uploadRes.status}`);

    setLoaderStep('Detecting column semantics…', 65);
    await new Promise(r => setTimeout(r, 180));

    ALL_SHEETS = json.sheets || [];

    setLoaderStep('Building AI schema…', 78);
    await new Promise(r => setTimeout(r, 180));

    // Find "Master_Data" sheet (case-insensitive, ignores spaces/underscores)
    const masterIdx = ALL_SHEETS.findIndex(s =>
      s.sheet.toLowerCase().replace(/[\s_]/g, '') === 'masterdata'
    );
    const sheetIdx = masterIdx >= 0 ? masterIdx : 0;

    setLoaderStep('Generating dashboard…', 90);
    await new Promise(r => setTimeout(r, 200));

    renderDashboard(ALL_SHEETS[sheetIdx], 'Production Clarity Dashboard.xlsx', sheetIdx);

    setLoaderStep('Ready!', 100);
    await new Promise(r => setTimeout(r, 320));

    hideStaticLoader();

  } catch (err) {
    hideStaticLoader();
    $('upload-screen').style.display = 'flex';
    const el = $('up-err');
    el.style.display = 'block';
    el.innerHTML = '⚡  <strong>Loading your intelligence...</strong> The dashboard is being prepared. Please refresh the page — your insights are just seconds away! 🧠✨';
  }
}

// document.addEventListener('DOMContentLoaded',initUpload);
document.addEventListener('DOMContentLoaded', initUpload);
</script>
</body>
</html>