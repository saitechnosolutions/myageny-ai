<style>
.site-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 32px;
    border-bottom: 1px solid #e1dee3;
    height: 72px;
    background-color: #fcfcfc;
}
.breadcrumbs { display: flex; align-items: center; gap: 2px; font-size: 14px; }
.crumb-item { color: #9e9e9e; }
.crumb-item.active { color: #121212; font-weight: 500; }
.header-actions { display: flex; align-items: center; gap: 12px; }
.icon-btn-group { display: flex; gap: 12px; }
.icon-btn {
    width: 32px; height: 32px; border: 1px solid #e1dee3;
    border-radius: 16px; display: flex; align-items: center;
    justify-content: center; background-color: #fcfcfc; cursor: pointer;
}
.btn-ai-insight {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: 16px;
    background: linear-gradient(90deg, #fff0e6 0%, #fff 100%);
    border: 1px solid #ffe0d0; cursor: pointer;
}
.btn-text { color: #fa6203; font-size: 14px; font-weight: 600; }
.user-menu { display: flex; align-items: center; gap: 4px; cursor: pointer; }
</style>

<header class="site-header">
    <div class="breadcrumbs">
        <span class="crumb-item">@yield('breadcrumb-1', 'Home')</span>
        <img src="{{ asset('images/42_3018.svg') }}" alt="/" class="crumb-sep">
        <span class="crumb-item active">@yield('breadcrumb-2', 'Dashboard')</span>
    </div>
    <div class="header-actions">
        <div class="icon-btn-group">
            <div class="icon-btn">
                <img src="{{ asset('images/42_3022.svg') }}" alt="Messages">
            </div>
            <div class="icon-btn">
                <img src="{{ asset('images/42_3024.svg') }}" alt="Notifications">
            </div>
        </div>
        <button class="btn-ai-insight">
            <span class="btn-text">Get AI Insight</span>
            <img src="{{ asset('images/42_3027.svg') }}" alt="Flare">
        </button>
        <div class="user-menu">
            <img src="{{ asset('images/42_3029.svg') }}" alt="User">
            <img src="{{ asset('images/42_3030.svg') }}" alt="Down">
        </div>
    </div>
</header>
