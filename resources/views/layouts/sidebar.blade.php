{{-- Sidebar - matches exact design from static HTML --}}
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            {{--  <div class="logo-icon">
                <img src="{{ asset('images/my_agenci_logo.png') }}" alt="Logo" class="logo-img">
            </div>
            <span class="logo-text">myAgenci.ai</span>  --}}
            <img src="{{ asset('images/my_agenci_logo.png') }}" alt="Logo" class="logo-img">
            {{--  <img src="{{ asset('images/42_3060.svg') }}" alt="Collapse" class="collapse-icon">  --}}
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
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="8" height="8" rx="2"></rect>
                            <rect x="13" y="3" width="8" height="5" rx="2"></rect>
                            <rect x="13" y="10" width="8" height="11" rx="2"></rect>
                            <rect x="3" y="13" width="8" height="8" rx="2"></rect>
                        </svg>
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
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"></path>
            <path d="M12 12l8-4.5"></path>
            <path d="M12 12v9"></path>
            <path d="M12 12L4 7.5"></path>
        </svg>
        <span>Masters</span>
        <img src="{{ asset('images/42_3081.svg') }}" alt="Expand" class="chevron">
    </div>
</a>

<!-- Dropdown Menu -->
<div class="submenu {{ request()->is('masters*') ? 'show' : 'show' }}">

    <a href="{{ url('/products') }}" class="submenu-item {{ request()->is('products') ? 'active' : '' }}">

        Products
    </a>
      <a href="{{ url('/lead/form-customization') }}" class="submenu-item {{ request()->is('products') ? 'active' : '' }}">

        Form Customization
    </a>
</div>



    <a href="javascript:void(0)"
   class="nav-item has-dropdown {{ request()->is('lead*') ? 'active open' : '' }}"
   onclick="toggleDropdown(this)">

    @if(request()->is('lead*'))
        <div class="active-indicator"></div>
    @endif

    <div class="nav-content">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="10" cy="7" r="4"></circle>
            <path d="M20 8v6"></path>
            <path d="M23 11h-6"></path>
        </svg>
        <span>Leads</span>
        <img src="{{ asset('images/42_3081.svg') }}" alt="Expand" class="chevron">
    </div>
</a>

<!-- Dropdown Menu -->
<div class="submenu {{ request()->is('lead*') ? 'show' : 'show' }}">
    <a href="{{ url('/leads') }}" class="submenu-item {{ request()->is('leads') ? 'active' : '' }}">
        All Leads
    </a>

    <a href="{{ url('/leads/create') }}" class="submenu-item {{ request()->is('leads/create') ? 'active' : '' }}">
        Add Lead
    </a>
</div>

 <a href="{{ url('/quotations') }}" class="nav-item {{ request()->is('quotations') || request()->is('/') ? 'active' : '' }}">
                    @if(request()->is('quotations') || request()->is('/'))
                        <div class="active-indicator"></div>
                    @endif
                    <div class="nav-content">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <path d="M14 3v6h6"></path>
                            <line x1="8" y1="13" x2="16" y2="13"></line>
                            <line x1="8" y1="17" x2="14" y2="17"></line>
                        </svg>
                        <span>Quotations</span>
                    </div>
                </a>

                <a href="{{ url('/settings') }}" class="nav-item {{ request()->is('settings') || request()->is('/') ? 'active' : '' }}">
                    @if(request()->is('settings') || request()->is('/'))
                        <div class="active-indicator"></div>
                    @endif
                    <div class="nav-content">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Settings</span>
                    </div>
                </a>

                <a href="{{ url('/authentications') }}" class="nav-item {{ request()->is('authentications') || request()->is('/') ? 'active' : '' }}">
                    @if(request()->is('settings') || request()->is('/'))
                        <div class="active-indicator"></div>
                    @endif
                    <div class="nav-content">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Authentications</span>
                    </div>
                </a>

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
