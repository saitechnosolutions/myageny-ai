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
            <div class="shortcut-hint">
                <img src="{{ asset('images/42_3065.svg') }}" alt="Cmd">
                <span>F</span>
            </div>
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
                        <span>Home</span>
                    </div>
                </a>
                <a href="{{ url('/lead') }}" class="nav-item {{ request()->is('lead') ? 'active' : '' }}">
                    @if(request()->is('lead'))
                        <div class="active-indicator"></div>
                    @endif
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3079.svg') }}" alt="Sales">
                        <span>Sales</span>
                        <img src="{{ asset('images/42_3081.svg') }}" alt="Expand" class="chevron">
                    </div>
                </a>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3085.svg') }}" alt="HRMS">
                        <span>HRMS</span>
                        <img src="{{ asset('images/42_3087.svg') }}" alt="Expand" class="chevron">
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3091.svg') }}" alt="Account">
                        <span>Account</span>
                        <img src="{{ asset('images/42_3093.svg') }}" alt="Expand" class="chevron">
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3097.svg') }}" alt="Reports">
                        <span>Reports</span>
                        <img src="{{ asset('images/42_3099.svg') }}" alt="Expand" class="chevron">
                    </div>
                </div>
            </div>
        </div>

        {{-- SHORTCUTS --}}
        <div class="nav-section">
            <div class="section-header">
                <div class="nav-title">SHORTCUTS</div>
                <div class="add-shortcut">
                    <img src="{{ asset('images/42_3104.svg') }}" alt="Add">
                </div>
            </div>
            <div class="nav-items">
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3109.svg') }}" alt="Drag">
                        <div class="shortcut-icon">🔍</div>
                        <div class="shortcut-text">
                            <span>HR Management</span>
                            <img src="{{ asset('images/42_3114.svg') }}" alt="Expand">
                        </div>
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3118.svg') }}" alt="Drag">
                        <div class="shortcut-icon">🧩</div>
                        <div class="shortcut-text">
                            <span>Payroll Processing</span>
                            <img src="{{ asset('images/42_3123.svg') }}" alt="Expand">
                        </div>
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3127.svg') }}" alt="Drag">
                        <div class="shortcut-icon">📊</div>
                        <div class="shortcut-text">
                            <span>Sales Ops</span>
                            <img src="{{ asset('images/42_3132.svg') }}" alt="Expand">
                        </div>
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3136.svg') }}" alt="Drag">
                        <div class="shortcut-icon">📃</div>
                        <div class="shortcut-text">
                            <span>Invoice &amp; Billing</span>
                            <img src="{{ asset('images/42_3141.svg') }}" alt="Expand">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOOLS --}}
        <div class="nav-section">
            <div class="nav-title">TOOLS</div>
            <div class="nav-items">
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3149.svg') }}" alt="Integrations">
                        <span>Integrations</span>
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3154.svg') }}" alt="Settings">
                        <span>Settings</span>
                    </div>
                </div>
                <div class="nav-item">
                    <div class="nav-content">
                        <img src="{{ asset('images/42_3159.svg') }}" alt="Help Centre">
                        <span>Help Centre</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="user-profile">
        <div class="user-avatar-v">V</div>
        <div class="user-info">
            <span class="user-name">Vinothini</span>
            <span class="user-role">HR Management</span>
        </div>
        <img src="{{ asset('images/42_3166.svg') }}" alt="Selector">
    </div>
</aside>
