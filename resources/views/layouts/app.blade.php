<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'myAgenci.ai')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* ===== GLOBAL RESET & BASE ===== */
        :root {
            --font-family: 'Inter', sans-serif;
            --color-bg: #fcfcfc;
            --color-bg-secondary: #f8f8f8;
            --color-text-primary: #121212;
            --color-text-secondary: #9e9e9e;
            --color-text-tertiary: #8e8e8e;
            --color-primary: #60308c;
            --color-accent: #fa6203;
            --color-border: #e1dee3;
            --color-white: #ffffff;
            --color-success: #469d89;
            --color-danger: #ff5a55;
            --color-warning: #c99411;
            --bg-color: #fcfcfc;
            --text-primary: #121212;
            --text-secondary: #7c7c7c;
            --text-tertiary: #9e9e9e;
            --border-color: #e1dee3;
            --primary-orange: #fe5f04;
            --accent-green: #469d89;
            --accent-red: #ff5a55;
            --accent-purple: #60308c;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: var(--font-family);
            background-color: var(--color-bg);
            color: var(--color-text-primary);
            display: flex;
            -webkit-font-smoothing: antialiased;
        }
        main {
            flex-grow: 1; display: flex;
            flex-direction: column;
            overflow-y: auto; position: relative;
        }
        .main-content-scroll {
            flex-grow: 1; overflow-y: auto;
            padding: 0 32px 32px 32px;
        }
        .sticky-header {
            position: sticky; top: 0;
            background-color: var(--color-bg); z-index: 10;
        }
        img, svg { display: block; max-width: 100%; }
        h1, h2, h3, h4, h5, h6, p { margin: 0; }
        a { text-decoration: none; color: inherit; }
        button { background: none; border: none; cursor: pointer; font-family: inherit; }
        .flex-row { display: flex; flex-direction: row; align-items: center; }
        .flex-col { display: flex; flex-direction: column; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; } .gap-3 { gap: 12px; } .gap-4 { gap: 16px; }
        .text-sm { font-size: 14px; } .text-xs { font-size: 12px; }
        .font-medium { font-weight: 500; } .font-semibold { font-weight: 600; }
        .text-gray { color: var(--color-text-secondary); }
        .container { display: flex; width: 100%; max-width: 1440px; margin: 0 auto; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e1dee3; border-radius: 3px; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 256px; flex-shrink: 0;
            border-right: 1px solid #e1dee3;
            padding: 20px; display: flex; flex-direction: column;
            background-color: #fcfcfc; height: 100vh;
            position: sticky; top: 0; overflow-y: auto;
        }
        .sidebar-header { margin-bottom: 20px; }
        .logo-container { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .logo-icon { width: 32px; height: 32px; border-radius: 20px; overflow: hidden; }
        .logo-img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .logo-text { font-weight: 700; font-size: 16px; color: #121212; flex-grow: 1; }
        .collapse-icon { cursor: pointer; }
        .search-bar {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px; background-color: #fcfcfc;
            border: 1px solid #e1dee3; border-radius: 16px; height: 32px;
        }
        .search-placeholder { flex-grow: 1; color: #9e9e9e; font-size: 14px; }
        .sidebar-nav { flex-grow: 1; display: flex; flex-direction: column; gap: 24px; }
        .nav-section { display: flex; flex-direction: column; gap: 8px; }
        .nav-title { font-size: 12px; color: #8e8e8e; font-weight: 600; margin-bottom: 4px; letter-spacing: 0.5px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; }
        .nav-items { display: flex; flex-direction: column; gap: 2px; }
        .nav-item {
            display: flex; align-items: center; height: 37px;
            cursor: pointer; position: relative; text-decoration: none;
        }
        .nav-item.active .nav-content { background-color: #f0f0f0; border-radius: 20px; }
        .active-indicator {
            position: absolute; left: -20px; width: 4px; height: 24px;
            background-color: #fe5f04; border-radius: 0 4px 4px 0;
        }
        .nav-content {
            display: flex; align-items: center; gap: 8px;
            padding: 0 12px; width: 100%; height: 100%;
            color: #2e2e2e; font-size: 14px; font-weight: 500;
        }
        .nav-content:hover { background-color: #f8f8f8; border-radius: 20px; }
        .chevron { margin-left: auto; }
        .user-profile {
            display: flex; align-items: center; gap: 12px;
            padding: 12px; background-color: #fcfcfc;
            border: 1px solid #e1dee3; border-radius: 16px; margin-top: 20px;
        }
        .user-info { display: flex; flex-direction: column; flex-grow: 1; }
        .user-name { font-size: 14px; font-weight: 700; color: #121212; }
        .user-role { font-size: 11px; color: #9e9e9e; font-weight: 500; }
        .user-avatar-v {
            width: 32px; height: 32px; background-color: #fe5f04;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; color: white; font-size: 14px; font-weight: 700;
        }
        .submenu { display: none; padding-left: 40px; transition: max-height 0.3s ease; }
        .submenu.show { display: block; }
        .submenu-item {
            display: block; padding: 8px 0; color: #aaa;
            text-decoration: none; font-size: 14px;
        }
        .submenu-item.active { color: #ff7c30; font-weight: 600; }
        .has-dropdown .chevron { transition: transform 0.3s ease; }
        .select2-container { width: 100% !important; }
        .has-dropdown.open .chevron { transform: rotate(90deg); }

        /* ===== OLD HEADER MAGI BUTTON (kept for per-page headers) ===== */
        .btn-ai-insight {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 14px 6px 8px; border-radius: 20px;
            background: linear-gradient(135deg, #FF5A00, #CC3300);
            border: none; cursor: pointer;
            box-shadow: 0 4px 12px rgba(255,90,0,.28);
            transition: all .2s ease;
            position: relative; overflow: hidden;
        }
        .btn-ai-insight::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            pointer-events: none;
        }
        .btn-ai-insight:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(255,90,0,.38); }
        .btn-ai-insight:active { transform: translateY(0); }
        .btn-magi-logo {
            width: 22px; height: 22px;
            background: rgba(255,255,255,.15); border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
        }
        .btn-magi-logo img { width: 18px; height: auto; display: block; }
        .btn-text { color: #fff; font-size: 13px; font-weight: 700; letter-spacing: .1px; }
        .btn-ai-pulse {
            width: 6px; height: 6px; border-radius: 50%;
            background: rgba(255,255,255,.85);
            animation: magi-pulse 2s ease-in-out infinite; flex-shrink: 0;
        }
        @keyframes magi-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.7); }
        }

        /* ===== MAGI FAB ===== */
        #magi-fab {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9998;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            pointer-events: none;
        }

        /* ── Hover mini card ── */
        #magi-mini-card {
            background: #1a0a2e;
            border: 1px solid rgba(255,90,0,0.35);
            border-radius: 18px;
            padding: 14px 16px;
            width: 230px;
            opacity: 0;
            transform: translateY(10px) scale(0.95);
            transition: opacity 0.28s ease, transform 0.28s ease;
            pointer-events: none;
            box-shadow: 0 12px 40px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,90,0,0.12);
        }
        #magi-fab:hover #magi-mini-card {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        .mmc-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding-bottom: 9px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .mmc-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2d1155, #1a0a2e);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1.5px solid rgba(255,90,0,0.4);
        }
        .mmc-avatar img {
            width: 38px;
            height: 38px;
            object-fit: cover;
            object-position: 15% center;
            mix-blend-mode: screen;
            filter: none;
        }
        .mmc-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }
        .mmc-badge {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: rgba(255,90,0,0.2);
            color: #FF5A00;
            padding: 2px 7px;
            border-radius: 20px;
            margin-left: auto;
            border: 1px solid rgba(255,90,0,0.3);
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .mmc-live-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #FF5A00;
            animation: mmc-blink 2s ease-in-out infinite;
        }
        @keyframes mmc-blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }
        .mmc-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            font-family: 'Inter', sans-serif;
        }
        .mmc-stat:last-of-type { border-bottom: none; }
        .mmc-stat-val { font-weight: 700; font-size: 12px; color: #fff; }
        .mmc-stat-val.green  { color: #4dffc4; }
        .mmc-stat-val.orange { color: #FF5A00; }
        .mmc-open-btn {
            width: 100%;
            margin-top: 11px;
            padding: 9px;
            background: linear-gradient(135deg, #FF5A00, #CC3300);
            border: none;
            border-radius: 11px;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.4px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: opacity 0.15s, transform 0.15s;
        }
        .mmc-open-btn:hover { opacity: 0.88; transform: translateY(-1px); }

        /* ── Orb button ── */
        #magi-orb-btn {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            position: relative;
            padding: 0;
            background: transparent;
            pointer-events: auto;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        #magi-orb-btn:hover { transform: translateY(-3px) scale(1.07); }
        #magi-orb-btn:active { transform: scale(0.94); }

        /* Spinning conic ring */
        #magi-orb-ring {
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, #FF5A00, #ff66aa, #9933FF, #FF5A00);
            animation: orb-spin 5s linear infinite;
            z-index: 0;
        }
        #magi-orb-ring::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            background: conic-gradient(from 0deg,
                rgba(255,90,0,0.4),
                rgba(255,51,102,0.2),
                rgba(153,51,255,0.4),
                rgba(255,90,0,0.4));
            animation: orb-spin 5s linear infinite, ring-pulse 2.8s ease-in-out infinite;
            z-index: -1;
        }
        @keyframes orb-spin { to { transform: rotate(360deg); } }
        @keyframes ring-pulse {
            0%, 100% { opacity: 0.5; transform: scale(1) rotate(0deg); }
            50%       { opacity: 1;   transform: scale(1.1) rotate(180deg); }
        }

        /* Inner dark circle */
        #magi-orb-inner {
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: linear-gradient(145deg, #2d1155, #1a0a2e);
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /*
            KEY FIX: mix-blend-mode: screen
            The PNG has black bg → screen blend makes black transparent
            on the dark purple inner circle, showing only the white character.
            object-position: 15% center crops to the girl character side only.
        */
        #magi-orb-img {
            width: 60px;
            height: 30px;
            object-fit: cover;
            object-position: 15% center;
            mix-blend-mode: screen;
            filter: none;
            position: relative;
            z-index: 2;
            flex-shrink: 0;
        }

        /* Tooltip label */
        #magi-fab-label {
            background: #1a0a2e;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            padding: 5px 13px;
            border-radius: 20px;
            white-space: nowrap;
            opacity: 0;
            transform: translateX(8px);
            transition: opacity 0.22s ease, transform 0.22s ease;
            pointer-events: none;
            border: 1px solid rgba(255,90,0,0.28);
            font-family: 'Inter', sans-serif;
        }
        #magi-fab:hover #magi-fab-label {
            opacity: 1;
            transform: translateX(0);
        }

        /* ── Ripple ── */
        .magi-ripple {
            position: fixed;
            border-radius: 50%;
            background: rgba(255,90,0,0.18);
            pointer-events: none;
            width: 64px;
            height: 64px;
            animation: magi-ripple-out 0.65s ease-out forwards;
        }
        @keyframes magi-ripple-out {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(5.5); }
        }

        /* ===== MAGI SLIDE-OVER PANEL ===== */
        #magi-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(18,18,18,0.55);
            backdrop-filter: blur(6px);
            align-items: stretch;
            justify-content: flex-end;
        }
        #magi-overlay.open {
            display: flex;
            animation: magi-bg-in 0.25s ease;
        }
        @keyframes magi-bg-in {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        #magi-panel {
            width: 100vw;
            height: 100vh;
            background: #fff;
            border-left: 3px solid #FF5A00;
            box-shadow: -8px 0 40px rgba(255,90,0,0.12);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: magi-slide-in 0.32s cubic-bezier(0.22,1,0.36,1);
        }
        @keyframes magi-slide-in {
            from { transform: translateX(100%); opacity: 0.6; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* Panel top bar */
        #magi-panel-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background: linear-gradient(135deg, #FF5A00, #CC3300);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }
        #magi-panel-bar::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent);
            pointer-events: none;
        }
        .mpb-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        .mpb-mascot-wrap {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #020202;
            border: 2px solid rgba(255,255,255,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .mpb-mascot-wrap img {
            width: 60px;
            height: 27px;
            object-fit: cover;
            object-position: 15% center;
            mix-blend-mode: screen;
            filter: none;
        }
        .mpb-text-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .mpb-name {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.2px;
            font-family: 'Inter', sans-serif;
            line-height: 1;
        }
        .mpb-sub {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.75);
            font-family: 'Inter', sans-serif;
        }
        .mpb-badge {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            background: rgba(255,255,255,0.18);
            color: #fff;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .mpb-live-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #fff;
            animation: mmc-blink 2s ease-in-out infinite;
        }
        .mpb-close {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.16);
            border: none;
            border-radius: 9px;
            color: #fff;
            font-size: 19px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            position: relative;
            z-index: 1;
            font-family: 'Inter', sans-serif;
            line-height: 1;
        }
        .mpb-close:hover { background: rgba(255,255,255,0.3); }

        #magi-iframe {
            flex: 1;
            border: none;
            width: 100%;
        }
    </style>

    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('scripts')
</head>
<body>

    @include('layouts.sidebar')

    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')

    <script>
        $(document).ready(function() {
            $('.select2').select2({ allowClear: true, width: '100%' });
            $('.select2').select2({ dropdownParent: $('#pp-modal-add-product') });
        });

        function toggleDropdown(element) {
            const submenu = element.nextElementSibling;
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
                element.classList.remove('open');
                return;
            }
            document.querySelectorAll('.submenu').forEach(menu => menu.classList.remove('show'));
            document.querySelectorAll('.has-dropdown').forEach(item => item.classList.remove('open'));
            submenu.classList.add('show');
            element.classList.add('open');
        }
    </script>

    {{-- ===== MAGI GLOBAL FAB ===== --}}
    <div id="magi-fab">

        {{-- Hover mini stats card --}}
        <div id="magi-mini-card">
    <div class="mmc-header">
        <div>
            <img src="{{ asset('images/magi.png') }}" alt="Magi">
        </div>
        <span class="mmc-badge">
            <span class="mmc-live-dot"></span>Live
        </span>
    </div>
    <p style="font-size:12px;color:rgba(255,255,255,0.72);line-height:1.65;margin:0 0 12px;font-family:'Inter',sans-serif;">
        Your AI-powered smart dashboard — instant charts, cross-tabs, trend analysis, and AI insights across all your data tables.
    </p>
    <button class="mmc-open-btn" onclick="openMagiPanel()">
        Open Full Dashboard →
    </button>
</div>

        {{-- Tooltip label --}}
        <span id="magi-fab-label">Magi Insight</span>

        {{-- Spinning orb button --}}
        <button id="magi-orb-btn" onclick="magiOrbClick(event)">
            <div id="magi-orb-ring"></div>
            <div id="magi-orb-inner">
                <img id="magi-orb-img"
                     src="{{ asset('images/magi.png') }}"
                     alt="Magi">
            </div>
        </button>

    </div>

    {{-- ===== MAGI SLIDE-OVER PANEL ===== --}}
    <div id="magi-overlay" onclick="handleMagiOverlayClick(event)">
        <div id="magi-panel">
            <div id="magi-panel-bar">
                <div class="mpb-brand">
                    <div class="mpb-mascot-wrap">
                        <img src="{{ asset('images/magi.png') }}" alt="Magi">
                    </div>
                    <div class="mpb-text-col">
                        <span class="mpb-name">Magi Ai</span>
                        <span class="mpb-sub">Analysing with AI…</span>
                    </div>
                    <span class="mpb-badge">
                        <span class="mpb-live-dot"></span>Live Analytics
                    </span>
                </div>
                <button class="mpb-close" onclick="closeMagiPanel()">✕</button>
            </div>
            <iframe id="magi-iframe" src="" title="Magi Smart Dashboard"></iframe>
        </div>
    </div>

    <script>
        /* ── FAB orb click — ripple + open panel ── */
        function magiOrbClick(e) {
            const orb  = document.getElementById('magi-orb-btn');
            const rect = orb.getBoundingClientRect();
            const ripple = document.createElement('div');
            ripple.className   = 'magi-ripple';
            ripple.style.left  = rect.left + 'px';
            ripple.style.top   = rect.top  + 'px';
            document.body.appendChild(ripple);
            setTimeout(() => ripple.remove(), 700);
            openMagiPanel();
        }

        /* ── Panel open ── */
        function openMagiPanel() {
            const overlay = document.getElementById('magi-overlay');
            const iframe  = document.getElementById('magi-iframe');
            if (!iframe.src || iframe.src === window.location.href) {
                iframe.src = '/magi';
            }
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        /* ── Panel close ── */
        function closeMagiPanel() {
            document.getElementById('magi-overlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        /* ── Click backdrop to close ── */
        function handleMagiOverlayClick(e) {
            if (e.target === document.getElementById('magi-overlay')) {
                closeMagiPanel();
            }
        }

        /* ── Escape key to close ── */
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeMagiPanel();
        });
    </script>

</body>
</html>