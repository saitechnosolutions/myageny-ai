@extends('layouts.app')

@section('title', 'myAgenci.ai Dashboard')

@push('styles')
<style>
/* ===== DASHBOARD PAGE STYLES (from 0db67e10/index.html) ===== */

/* Main Content */
.main-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.dashboard-content {
    flex-grow: 1;
    overflow-y: auto;
    padding: 0 32px 32px 32px;
}

/* Site Header */
.site-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 32px;
    background-color: var(--color-bg);
    position: sticky;
    top: 0;
    z-index: 10;
}
.breadcrumbs { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #9e9e9e; }
.breadcrumb-item.active { color: #121212; font-weight: 500; }
.header-actions { display: flex; align-items: center; gap: 12px; }
.icon-btn {
    width: 32px; height: 32px; border-radius: 16px;
    background-color: #fcfcfc; border: 1px solid #e1dee3;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.ai-insight-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px;
    background-color: #ffffff;
    font-size: 14px; color: #fa6203;
    font-weight: 600; cursor: pointer;
    border: 1.5px solid #fa6203;
}
.profile-dropdown { display: flex; align-items: center; gap: 4px; cursor: pointer; }

/* Dashboard Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 32px;
}
.dashboard-left { display: flex; flex-direction: column; gap: 24px; }

/* Hero Section */
.hero-section {
    position: relative;
    background-color: #efeef0;
    border: 1px solid #e1dee3;
    border-radius: 16px;
    padding: 24px;
    height: 231px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.hero-bg-image {
    position: absolute; top: 0; right: 0;
    width: 60%; height: 100%;
    background-image: url('{{ asset("images/48_763.svg") }}');
    background-size: contain; background-repeat: no-repeat; background-position: right center;
    pointer-events: none;
}
.hero-text h2 { font-size: 14px; color: #4e4e4e; margin-bottom: 8px; font-weight: 400; }
.hero-text h1 { font-size: 32px; line-height: 1.2; font-weight: 700; }
.gradient-text {
    background: linear-gradient(90deg, #fe5f04 0%, #d58900 50%, #fe5f04 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hero-input-container {
    display: flex; align-items: center;
    background-color: #fcfcfc; border-radius: 20px;
    padding: 8px 12px; gap: 8px;
    box-shadow: 0px 2px 12px rgba(0,0,0,0.1); z-index: 1;
}
.hero-input {
    border: none; outline: none; flex-grow: 1;
    font-size: 14px; color: #9e9e9e; background: transparent;
    font-family: 'Inter', sans-serif;
}
.hero-input-actions { display: flex; align-items: center; gap: 12px; }
.send-btn {
    width: 24px; height: 24px; background-color: #fe5f04;
    border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer;
}

/* Section Common */
.section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
.cards-row { display: flex; gap: 16px; }
.action-card {
    flex: 1; background-color: #fcfcfc; border: 1px solid #e1dee3;
    border-radius: 12px; padding: 12px 16px;
    display: flex; align-items: center; gap: 12px;
}
.card-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.bg-cream { background-color: #f0eadb; }
.bg-peach { background-color: #f3e6da; }
.bg-rose { background-color: #f5e0e6; }
.card-info { display: flex; flex-direction: column; flex-grow: 1; }
.card-header { display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 2px; }
.card-sub { font-size: 12px; color: #9e9e9e; }

/* Stats Grid */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card {
    background-color: #fcfcfc; border: 1px solid #e1dee3;
    border-radius: 12px; padding: 16px; display: flex; flex-direction: column;
}
.stat-header { display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 2px; }
.stat-period { font-size: 12px; color: #9e9e9e; margin-bottom: 12px; }
.stat-value-row { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
.stat-value { font-size: 24px; font-weight: 600; }
.badge-stable {
    background-color: #f4eff9; color: #60308c;
    font-size: 10px; padding: 2px 6px; border-radius: 20px;
}
.chart-area { margin-top: auto; }

/* Table Section */
.table-header {
    display: flex; justify-content: space-between;
    align-items: flex-end; margin-bottom: 16px;
}
.table-title-group h3 { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
.table-title-group p { font-size: 12px; color: #9e9e9e; }
.table-actions { display: flex; gap: 8px; }
.btn-outline {
    display: flex; align-items: center; gap: 6px; padding: 6px 12px;
    border: 1px solid #e1dee3; border-radius: 16px;
    background-color: #fcfcfc; font-size: 12px; cursor: pointer;
}
.btn-purple {
    display: flex; align-items: center; gap: 6px; padding: 6px 12px;
    background-color: #ede6f4; color: #3f1b5f;
    border-radius: 16px; font-size: 12px; cursor: pointer;
}
.data-table {
    background-color: #fcfcfc; border: 1px solid #e1dee3;
    border-radius: 12px; overflow: hidden;
}
.table-row {
    display: flex; align-items: center;
    padding: 12px 16px; border-bottom: 1px solid #f1f1f1; font-size: 12px;
}
.table-row:last-child { border-bottom: none; }
.header-row { background-color: #f8f8f8; font-weight: 500; color: #9e9e9e; }
.checkbox {
    width: 14px; height: 14px; border: 1px solid #e1dee3;
    border-radius: 4px; margin-right: 16px; background-color: #f9f9f9;
    display: flex; align-items: center; justify-content: center;
}
.checkbox.checked { background-color: #60308c; border-color: #60308c; }
.col-name { width: 150px; font-weight: 600; color: #121212; }
.col-status { width: 120px; }
.col-id { width: 80px; color: #121212; }
.col-role { width: 120px; color: #121212; }
.col-tl { width: 100px; color: #121212; }
.col-action { flex-grow: 1; display: flex; gap: 8px; justify-content: flex-end; }
.status-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 20px; font-size: 10px;
}
.status-badge.inactive { background-color: #ffdfdf; color: #ff5a55; border: 1px solid #ff9696; }
.status-badge.notice { background-color: #faf4ff; color: #41225d; border: 1px solid #f3edf9; }
.status-badge.active { background-color: #f1fdf6; color: #225247; border: 1px solid #e6f6ed; }
.table-row.selected { background: linear-gradient(90deg, #ebe5f0 0%, #f4f4f4 100%); }

/* Right Column */
.dashboard-right { display: flex; flex-direction: column; gap: 32px; }
.leaderboard-card {
    position: relative; height: 507px; border-radius: 12px;
    overflow: hidden; background-color: #fcfcfc; border: 1px solid #e1dee3;
}
.leaderboard-bg img { width: 100%; height: 100%; object-fit: cover; }
.leaderboard-content {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    padding: 20px; display: flex; flex-direction: column;
    align-items: center; text-align: center;
}
.vertical-text {
    position: absolute; right: 20px; top: 50px;
    writing-mode: vertical-rl; transform: rotate(180deg);
    font-size: 32px; font-weight: 700; color: transparent;
    -webkit-text-stroke: 1px #838383; opacity: 0.3;
}
.performer-info {
    margin-top: auto; margin-bottom: 40px; width: 100%;
    display: flex; flex-direction: column; align-items: center;
}
.performer-image { width: 200px; height: 200px; margin-bottom: 16px; }
.performer-title { font-size: 14px; color: #434343; margin-bottom: 4px; }
.performer-name { font-size: 28px; font-weight: 700; color: #a30000; margin-bottom: 8px; }
.performer-role {
    background-color: #ffffff; padding: 4px 12px; border-radius: 12px;
    font-size: 12px; font-weight: 600; color: #434343; margin-bottom: 12px;
}
.performer-desc { font-size: 12px; color: #434343; line-height: 1.4; max-width: 220px; }

/* Birthdays & Events */
.birthdays-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.birthdays-header h3 { font-size: 16px; font-weight: 600; }
.latest-btn {
    display: flex; align-items: center; gap: 8px;
    background-color: #fcfcfc; border: 1px solid #e1dee3;
    padding: 4px 4px 4px 12px; border-radius: 20px; font-size: 12px;
}
.search-circle {
    width: 24px; height: 24px; background-color: #f8f8f8;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
}
.events-list { display: flex; flex-direction: column; gap: 12px; }
.event-item {
    background-color: #fcfcfc; border: 1px solid #e9e9e9;
    border-radius: 12px; padding: 12px;
    display: flex; align-items: center; gap: 12px;
}
.event-avatars { position: relative; width: 40px; height: 40px; display: flex; align-items: center; }
.event-avatars img { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fcfcfc; }
.stripe-logo {
    position: absolute; bottom: -2px; right: -2px;
    width: 16px; height: 16px; background: white;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.event-details { flex-grow: 1; display: flex; flex-direction: column; gap: 4px; }
.event-row { display: flex; justify-content: space-between; font-size: 10px; }
.event-role { color: #60308c; font-weight: 600; }
.text-red { color: #853434; }
.text-maroon { color: #851f3c; }
.event-date { color: #9e9e9e; }
.event-name { font-size: 14px; font-weight: 600; color: #121212; }
</style>
@endpush

@section('content')
<main class="main-content">
    <!-- Site Header -->
    <header class="site-header">
        <div class="breadcrumbs">
            <span class="breadcrumb-item">Overview</span>
            <img src="{{ asset('images/48_651.svg') }}" alt="/">
            <span class="breadcrumb-item active">Home</span>
        </div>
        <div class="header-actions">
            <div class="icon-btn"><img src="{{ asset('images/48_655.svg') }}" alt="Message"></div>
            <div class="icon-btn"><img src="{{ asset('images/48_657.svg') }}" alt="Bell"></div>
            <div class="ai-insight-btn">
                <span>Get AI Insight</span>
                <img src="{{ asset('images/48_660.svg') }}" alt="Stars">
            </div>
            <div class="profile-dropdown">
                <img src="{{ asset('images/48_662.svg') }}" alt="Profile">
                <img src="{{ asset('images/48_663.svg') }}" alt="Down">
            </div>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="dashboard-grid">
        <!-- Left Column -->
        <div class="dashboard-left">

            <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-text">
                    <h2>Welcome back, Prasanna ☀️</h2>
                    <h1>Describe the <span class="gradient-text">Prompt</span><br>What You Want</h1>
                </div>
                <div class="hero-input-container">
                    <img src="{{ asset('images/48_872.svg') }}" alt="Sparkle">
                    <input type="text" placeholder="Example: When a project created &amp; details" class="hero-input">
                    <div class="hero-input-actions">
                        <img src="{{ asset('images/I48_875_3_81224.svg') }}" alt="Voice">
                        <div class="send-btn"><img src="{{ asset('images/48_877.svg') }}" alt="Send"></div>
                    </div>
                </div>
                <div class="hero-bg-image"></div>
            </section>

            <!-- Get Started -->
            <section>
                <h3 class="section-title">Get Started</h3>
                <div class="cards-row">
                    <div class="action-card">
                        <div class="card-icon bg-cream"><img src="{{ asset('images/48_772.svg') }}" alt="Leave"></div>
                        <div class="card-info">
                            <div class="card-header">
                                <span>Leave Managament</span>
                                <img src="{{ asset('images/48_776.svg') }}" alt="Add">
                            </div>
                            <span class="card-sub">Leave Approval |</span>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="card-icon bg-peach"><img src="{{ asset('images/48_780.svg') }}" alt="Permission"></div>
                        <div class="card-info">
                            <div class="card-header">
                                <span>Permission Managem.</span>
                                <img src="{{ asset('images/48_784.svg') }}" alt="Add">
                            </div>
                            <span class="card-sub">Permission |</span>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="card-icon bg-rose"><img src="{{ asset('images/48_788.svg') }}" alt="Holiday"></div>
                        <div class="card-info">
                            <div class="card-header">
                                <span>Holiday Management</span>
                                <img src="{{ asset('images/48_792.svg') }}" alt="Add">
                            </div>
                            <span class="card-sub">Leave Calendar</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Snapshot -->
            <section>
                <h3 class="section-title">Quick Snapshot</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Attendance Health</span>
                            <img src="{{ asset('images/48_671.svg') }}" alt="Info">
                        </div>
                        <span class="stat-period">last 7 days</span>
                        <div class="stat-value-row">
                            <span class="stat-value">99%</span>
                            <span class="badge-stable">Stable</span>
                        </div>
                        <div class="chart-area"><div id="attendanceChart"></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Total Present</span>
                            <img src="{{ asset('images/48_688.svg') }}" alt="Info">
                        </div>
                        <span class="stat-period">13 Feb</span>
                        <div class="stat-value-row">
                            <span class="stat-value">90/110</span>
                        </div>
                        <div class="chart-area"><div id="presentChart"></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Total Absent</span>
                            <img src="{{ asset('images/48_722.svg') }}" alt="Info">
                        </div>
                        <span class="stat-period">13 Feb</span>
                        <div class="stat-value-row">
                            <span class="stat-value">94%</span>
                        </div>
                        <div class="chart-area"><div id="absentChart"></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Month Wise</span>
                            <img src="{{ asset('images/48_738.svg') }}" alt="Info">
                        </div>
                        <span class="stat-period">last 7 days</span>
                        <div class="stat-value-row">
                            <span class="stat-value">99%</span>
                            <span class="badge-stable">Stable</span>
                        </div>
                        <div class="chart-area"><div id="monthWiseChart"></div></div>
                    </div>
                </div>
            </section>

            <!-- Employees Status -->
            <section>
                <div class="table-header">
                    <div class="table-title-group">
                        <h3>Employees Status</h3>
                        <p>Recent automation runs across workflows and systems</p>
                    </div>
                    <div class="table-actions">
                        <div class="icon-btn"><img src="{{ asset('images/48_800.svg') }}" alt="Filter"></div>
                        <div class="btn-outline">
                            <span>Export</span>
                            <img src="{{ asset('images/48_803.svg') }}" alt="Download">
                        </div>
                        <div class="btn-purple">
                            <span>1 selected</span>
                            <img src="{{ asset('images/48_806.svg') }}" alt="Close">
                        </div>
                    </div>
                </div>
                <div class="data-table">
                    <div class="table-row header-row">
                        <div class="checkbox"></div>
                        <div class="col-name">Name</div>
                        <div class="col-status">Status</div>
                        <div class="col-id">ID</div>
                        <div class="col-role">Job Role</div>
                        <div class="col-tl">TL</div>
                        <div class="col-action">Action</div>
                    </div>
                    <div class="table-row">
                        <div class="checkbox"></div>
                        <div class="col-name">Pragatheesh</div>
                        <div class="col-status">
                            <span class="status-badge inactive">
                                <img src="{{ asset('images/48_824.svg') }}" alt=""> Inactive
                            </span>
                        </div>
                        <div class="col-id">FM-1001</div>
                        <div class="col-role">Junior Designer</div>
                        <div class="col-tl">Prasanna</div>
                        <div class="col-action">
                            <img src="{{ asset('images/48_830.svg') }}" alt="Reload">
                            <img src="{{ asset('images/48_831.svg') }}" alt="Link">
                        </div>
                    </div>
                    <div class="table-row selected">
                        <div class="checkbox checked"><img src="{{ asset('images/48_835.svg') }}" alt="✓"></div>
                        <div class="col-name">Vinoth Kumar</div>
                        <div class="col-status">
                            <span class="status-badge notice">
                                <img src="{{ asset('images/48_840.svg') }}" alt=""> Notice Period
                            </span>
                        </div>
                        <div class="col-id">FM-1002</div>
                        <div class="col-role">Senior Designer</div>
                        <div class="col-tl">Prasanna</div>
                        <div class="col-action">
                            <img src="{{ asset('images/48_846.svg') }}" alt="Reload">
                            <img src="{{ asset('images/48_847.svg') }}" alt="Link">
                        </div>
                    </div>
                    <div class="table-row">
                        <div class="checkbox"></div>
                        <div class="col-name">Saravanan</div>
                        <div class="col-status">
                            <span class="status-badge active">
                                <img src="{{ asset('images/48_855.svg') }}" alt=""> Active
                            </span>
                        </div>
                        <div class="col-id">FM-1003</div>
                        <div class="col-role">Junior Designer</div>
                        <div class="col-tl">Prasanna</div>
                        <div class="col-action">
                            <img src="{{ asset('images/48_861.svg') }}" alt="Reload">
                            <img src="{{ asset('images/48_862.svg') }}" alt="Link">
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Column -->
        <div class="dashboard-right">
            <!-- Leaderboard Card -->
            <div class="leaderboard-card">
                <div class="leaderboard-bg">
                    <img src="{{ asset('images/d8adde71e9684f2e9950e07585a9dbc87f1037d2.png') }}" alt="Background">
                </div>
                <div class="leaderboard-content">
                    <div class="vertical-text">Leader Board</div>
                    <div class="performer-info">
                        <div class="performer-image">
                            <img src="{{ asset('images/62e9d4bb57d43afc6cf67e276e4757d3860deb15.png') }}" alt="Dewald">
                        </div>
                        <div class="performer-title">Top Performer the Month</div>
                        <h2 class="performer-name">Dewald Brevis</h2>
                        <div class="performer-role">Production Manager</div>
                        <p class="performer-desc">Your hard work, dedication, and commitment to excellence truly set you apart. Keep up the outstanding performance!</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Birthdays & Events -->
            <div class="birthdays-section">
                <div class="birthdays-header">
                    <h3>Upcoming<br>Birthdays &amp; Events</h3>
                    <div class="latest-btn">
                        <span>Latest</span>
                        <img src="{{ asset('images/48_884.svg') }}" alt="Down">
                        <div class="search-circle"><img src="{{ asset('images/48_886.svg') }}" alt="Search"></div>
                    </div>
                </div>
                <div class="events-list">
                    <div class="event-item">
                        <div class="event-avatars">
                            <img src="{{ asset('images/5719c33f57124b4bb4056631c60710e0bf990276.png') }}" alt="User">
                        </div>
                        <div class="event-details">
                            <div class="event-row">
                                <span class="event-role">Junior Graphic Designer</span>
                                <span class="event-date">13 Jan</span>
                            </div>
                            <div class="event-name">Sheik Abdullah</div>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-avatars">
                            <img src="{{ asset('images/7b036e7e504b2a0adaebaf379979334e7ddab29b.png') }}" alt="User">
                        </div>
                        <div class="event-details">
                            <div class="event-row">
                                <span class="event-role text-red">Mahasivaratri</span>
                                <span class="event-date">15 Jan</span>
                            </div>
                            <div class="event-name">Holiday</div>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-avatars">
                            <img src="{{ asset('images/38a24495454e02789f38efe4cfb91abb19e84990.png') }}" alt="User">
                            <div class="stripe-logo"><img src="{{ asset('images/48_905.svg') }}" alt="Stripe"></div>
                        </div>
                        <div class="event-details">
                            <div class="event-row">
                                <span class="event-role text-maroon">Mobile App Developer</span>
                                <span class="event-date">18 Jan</span>
                            </div>
                            <div class="event-name">Abhi Jay</div>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-avatars">
                            <img src="{{ asset('images/eed7dba5ea152c5e0e3e2b490b42b92b946dcb5d.png') }}" alt="User">
                        </div>
                        <div class="event-details">
                            <div class="event-row">
                                <span class="event-role text-maroon">Sales</span>
                                <span class="event-date">20 Jan</span>
                            </div>
                            <div class="event-name">Murunalini</div>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-avatars">
                            <img src="{{ asset('images/4bb5e3043e76584b85a5b6cf43a5a830939d45ff.png') }}" alt="User">
                            <img src="{{ asset('images/094d8fff7d62e0ee8e93a07008f473916a56b405.png') }}" alt="User" style="margin-left:-15px;">
                        </div>
                        <div class="event-details">
                            <div class="event-row">
                                <span class="event-role">Production Manager</span>
                                <span class="event-date">25 Jan</span>
                            </div>
                            <div class="event-name">Mathiyazahgan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div><!-- end dashboard-content -->
</main>
@endsection

@push('scripts')
<script>
    // ----- ApexCharts Initializations -----

    // 1. Attendance Health (RadialBar)
    var attendanceOptions = {
        series: [99],
        chart: { type: 'radialBar', height: 180, sparkline: { enabled: true } },
        plotOptions: {
            radialBar: {
                startAngle: -90, endAngle: 90,
                track: { background: "#f0f0f0", strokeWidth: '97%', margin: 5 },
                dataLabels: { show: false }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light', shadeIntensity: 0.4,
                inverseColors: false, opacityFrom: 1, opacityTo: 1,
                stops: [0, 50, 100], colorStops: [
                    { offset: 0, color: "#60308c", opacity: 1 },
                    { offset: 100, color: "#a070cc", opacity: 1 }
                ]
            }
        },
        stroke: { dashArray: 4 },
        labels: ['Attendance'],
    };
    new ApexCharts(document.querySelector("#attendanceChart"), attendanceOptions).render();

    // 2. Total Present (Bar)
    var presentOptions = {
        series: [{ name: 'Present', data: [44, 55, 41, 67, 22, 43, 21, 49, 33, 52] }],
        chart: { type: 'bar', height: 60, sparkline: { enabled: true } },
        plotOptions: { bar: { columnWidth: '50%', borderRadius: 2 } },
        colors: ['#fe5f04'],
        fill: {
            type: 'gradient',
            gradient: { shade: 'light', type: "vertical", shadeIntensity: 0.25, opacityFrom: 1, opacityTo: 0.85 }
        },
        tooltip: { enabled: false }
    };
    new ApexCharts(document.querySelector("#presentChart"), presentOptions).render();

    // 3. Total Absent (Bar)
    var absentOptions = {
        series: [{ name: 'Absent', data: [12, 18, 11, 25, 9, 21, 15, 12, 18, 14] }],
        chart: { type: 'bar', height: 60, sparkline: { enabled: true } },
        plotOptions: { bar: { columnWidth: '50%', borderRadius: 2 } },
        colors: ['#225247'],
        tooltip: { enabled: false }
    };
    new ApexCharts(document.querySelector("#absentChart"), absentOptions).render();

    // 4. Month Wise (RadialBar)
    var monthWiseOptions = {
        series: [99],
        chart: { type: 'radialBar', height: 180, sparkline: { enabled: true } },
        plotOptions: {
            radialBar: {
                startAngle: -90, endAngle: 90,
                track: { background: "#f0f0f0", strokeWidth: '97%', margin: 5 },
                dataLabels: { show: false }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                stops: [0, 100], colorStops: [
                    { offset: 0, color: "#60308c", opacity: 1 },
                    { offset: 100, color: "#a070cc", opacity: 1 }
                ]
            }
        },
        labels: ['Month Wise'],
    };
    new ApexCharts(document.querySelector("#monthWiseChart"), monthWiseOptions).render();
</script>
@endpush
