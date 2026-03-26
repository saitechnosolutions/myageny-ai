{{-- Sidebar - matches exact design from static HTML --}}
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo-icon">
                <img src="{{ asset('images/de5a089ae19a67b2b6f7f59203abf1b0ba0f9474.png') }}" alt="Logo" class="logo-img">
            </div>
            <span class="logo-text">myAgenci.ai</span>
            <img src="{{ asset('images/42_3060.svg') }}" alt="Collapse" class="collapse-icon">
        </div>

        <div class="search-bar">
            <img src="{{ asset('images/42_3062.svg') }}" alt="Search">
            <span class="search-placeholder">Search...</span>

        </div>
    </div>

    <nav class="sidebar-nav">
        {{-- OVERVIEW --}}
        <div class="nav-section">
            <div class="nav-title">OVERVIEW</div>
            <div class="nav-items">
                 <a href="{{ url('/dashboard') }}" class="nav-item {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
                    @if(request()->is('dashboard') || request()->is('/'))
                        <div class="active-indicator"></div>
                    @endif
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3074.svg') }}" alt="Home">
                        <span>Dashboard</span>
                    </div>
                </a>

                 <a href="javascript:void(0)"
   class="nav-item has-dropdown {{ request()->is('masters*') ? 'active open' : '' }}"
   onclick="toggleDropdown(this)">

    @if(request()->is('lead*'))
        <div class="active-indicator"></div>
    @endif

    <div class="nav-content">
        <img src="{{ asset('images/42_3079.svg') }}" alt="Sales">
        <span>Masters</span>
        <img src="{{ asset('images/42_3081.svg') }}" alt="Expand" class="chevron">
    </div>
</a>

<!-- Dropdown Menu -->
<div class="submenu {{ request()->is('masters*') ? 'show' : '' }}">

    <a href="{{ url('/products') }}" class="submenu-item {{ request()->is('products') ? 'active' : '' }}">
        Products
    </a>
</div>

                <a href="javascript:void(0)"
   class="nav-item has-dropdown {{ request()->is('lead*') ? 'active open' : '' }}"
   onclick="toggleDropdown(this)">

    @if(request()->is('lead*'))
        <div class="active-indicator"></div>
    @endif

    <div class="nav-content">
        <img src="{{ asset('images/42_3079.svg') }}" alt="Sales">
        <span>Leads</span>
        <img src="{{ asset('images/42_3081.svg') }}" alt="Expand" class="chevron">
    </div>
</a>

<!-- Dropdown Menu -->
<div class="submenu {{ request()->is('lead*') ? 'show' : '' }}">
    <a href="{{ url('/leads') }}" class="submenu-item {{ request()->is('leads') ? 'active' : '' }}">
        All Leads
    </a>

    <a href="{{ url('/leads/create') }}" class="submenu-item {{ request()->is('leads/create') ? 'active' : '' }}">
        Add Lead
    </a>
</div>

            </div>
        </div>
    </nav>

    <div class="user-profile">
        <div class="user-avatar-v">V</div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role">{{ Auth::user()->role_name }}</span>
        </div>
        <img src="{{ asset('images/42_3166.svg') }}" alt="Selector">

    </div>
 @include('layouts.logout_btn')
</aside>

