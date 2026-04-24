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
    <!-- Font Awesome (If needed) & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <style>
        /* ===== GLOBAL RESET & BASE ===== */
        :root {
            --font-family: 'Inter', sans-serif;
            /* Dashboard page variables */
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
            /* Lead page variables */
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
            margin: 0;
            padding: 0;
            font-family: var(--font-family);
            background-color: var(--color-bg);
            color: var(--color-text-primary);
            display: flex;

            -webkit-font-smoothing: antialiased;
        }
        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
        }
        .main-content-scroll {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0 32px 32px 32px;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            background-color: var(--color-bg);
            z-index: 10;
        }
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"] {
            accent-color: #ff7c30;
            color-scheme: light;
        }
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            border-radius: 8px;
            padding: 4px;
            transition: background-color .15s ease, opacity .15s ease;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover,
        input[type="time"]::-webkit-calendar-picker-indicator:hover,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
            background-color: rgba(255, 124, 48, 0.14);
        }
        input[type="date"]:focus,
        input[type="time"]:focus,
        input[type="datetime-local"]:focus {
            border-color: #ff7c30 !important;
            box-shadow: 0 0 0 3px rgba(255, 124, 48, 0.12) !important;
        }
        main table thead th {
            text-transform: uppercase;
            letter-spacing: .6px;
        }
        .app-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 14px 18px;
            border-top: 1px solid #f0eef2;
            background: #fff;
        }
        .app-pagination__info {
            font-size: 12px;
            color: #8a8a8a;
        }
        .app-pagination__info strong {
            color: #121212;
            font-weight: 700;
        }
        .app-pagination__links {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .app-pagination__link,
        .app-pagination__ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            padding: 6px 11px;
            border-radius: 8px;
            border: 1px solid #e1dee3;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #666;
            text-decoration: none;
            transition: all .15s ease;
        }
        .app-pagination__link:hover,
        .app-pagination__link.is-active {
            background: #fe5f04;
            border-color: #fe5f04;
            color: #fff;
        }
        .app-pagination__link.is-disabled {
            opacity: .45;
            cursor: default;
            pointer-events: none;
        }
        .app-pagination__ellipsis {
            border-color: transparent;
            background: transparent;
            color: #9e9e9e;
            min-width: auto;
            padding: 6px 2px;
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
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .text-gray { color: var(--color-text-secondary); }
        .container { display: flex; width: 100%; max-width: 1440px; margin: 0 auto; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e1dee3; border-radius: 3px; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 256px;
            flex-shrink: 0;
            border-right: 1px solid #e1dee3;
            padding: 20px;
            display: flex;
            flex-direction: column;
            background-color: #fcfcfc;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
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
        .shortcut-hint { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #121212; }
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
        .shortcut-icon {
            width: 20px; height: 20px; background-color: #f0f0f0;
            border-radius: 6px; display: flex; align-items: center;
            justify-content: center; font-size: 12px;
        }
        .shortcut-text { display: flex; align-items: center; justify-content: space-between; flex-grow: 1; }
        .user-profile {
            display: flex; align-items: center; gap: 12px;
            padding: 12px; background-color: #fcfcfc;
            border: 1px solid #e1dee3; border-radius: 16px;
            margin-top: 20px;
        }
        .user-info { display: flex; flex-direction: column; flex-grow: 1; }
        .user-name { font-size: 14px; font-weight: 700; color: #121212; }
        .user-role { font-size: 11px; color: #9e9e9e; font-weight: 500; }
        .user-avatar-v {
            width: 32px; height: 32px; background-color: #fe5f04;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; color: white;
            font-size: 14px; font-weight: 700;
        }

        .submenu {
    display: none;
    padding-left: 40px;
    transition: max-height 0.3s ease;
}

.submenu.show {
    display: block;
}

.submenu-item {
    display: block;
    padding: 8px 0;
    color: #aaa;
    text-decoration: none;
    font-size: 14px;
    transition: max-height 0.3s ease;
}

.submenu-item.active {
    color: #ff7c30;
    font-weight: 600;
}

.has-dropdown .chevron {
    transition: transform 0.3s ease;
}

.select2-container
{
    width:100% !important;
}

.has-dropdown.open .chevron {
    transform: rotate(90deg);
}

    </style>
    @stack('styles')
    <!-- ApexCharts CDN -->
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
        $('.select2').select2({
            allowClear: true,
            width: '100%'
        });

        $('.select2').select2({
            dropdownParent: $('#pp-modal-add-product')
        });
});

function toggleDropdown(element) {
    const submenu = element.nextElementSibling;

    // Close if already open (THIS WAS MISSING 🔥)
    if (submenu.classList.contains('show')) {
        submenu.classList.remove('show');
        element.classList.remove('open');
        return;
    }

    // Close all other dropdowns
    document.querySelectorAll('.submenu').forEach(menu => {
        menu.classList.remove('show');
    });

    document.querySelectorAll('.has-dropdown').forEach(item => {
        item.classList.remove('open');
    });

    // Open current
    submenu.classList.add('show');
    element.classList.add('open');
}

    </script>
</body>
</html>
