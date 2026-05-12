@extends('layouts.app')

@section('title', 'HRMS Dashboard')

@push('styles')
<style>
.hrms-dashboard{
    min-height:100%;
    padding:28px;
    background:
        radial-gradient(circle at top left, rgba(254,95,4,.10), transparent 24%),
        linear-gradient(180deg,#fff7f1 0%,#f8f5f1 42%,#f4f5f7 100%);
}
.hrms-shell{max-width:1440px;margin:0 auto;display:flex;flex-direction:column;gap:22px}
.hrms-hero{
    display:grid;grid-template-columns:minmax(0,1.4fr) minmax(320px,.8fr);gap:20px;
}
.hrms-card{
    background:rgba(255,255,255,.94);
    border:1px solid #e9e1da;
    border-radius:24px;
    box-shadow:0 20px 44px rgba(18,18,18,.05);
}
.hrms-hero-card{
    padding:28px 30px;
    background:
        radial-gradient(circle at top right, rgba(255,199,164,.55), transparent 28%),
        linear-gradient(135deg,#fffaf7 0%,#ffffff 58%,#fff5ee 100%);
}
.hrms-eyebrow{
    display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;
    background:#fff1e8;color:#c25513;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;
}
.hrms-title{margin:14px 0 8px;font-size:34px;line-height:1.08;font-weight:800;color:#121212}
.hrms-subtitle{max-width:700px;font-size:14px;line-height:1.7;color:#737373;margin:0}
.hrms-hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:22px}
.hrms-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    padding:11px 18px;border-radius:14px;border:1px solid transparent;text-decoration:none;
    font-size:13px;font-weight:700;transition:all .18s ease;cursor:pointer;
}
.hrms-btn-primary{background:linear-gradient(135deg,#fe5f04,#ff7c30);color:#fff;box-shadow:0 14px 24px rgba(254,95,4,.18)}
.hrms-btn-primary:hover{transform:translateY(-1px)}
.hrms-btn-ghost{background:#fff;color:#121212;border-color:#e5ddd6}
.hrms-btn-ghost:hover{background:#faf7f5}
.hrms-aside{
    padding:24px;
    display:flex;flex-direction:column;gap:16px;
    background:linear-gradient(180deg,#fff8f3 0%,#fff 100%);
}
.hrms-aside-title{font-size:15px;font-weight:800;color:#121212}
.hrms-aside-copy{font-size:13px;line-height:1.6;color:#7a7a7a}
.hrms-mini-list{display:flex;flex-direction:column;gap:10px}
.hrms-mini-item{
    display:flex;justify-content:space-between;align-items:center;gap:10px;
    padding:12px 14px;border-radius:16px;background:#fff;border:1px solid #f0e4da;
}
.hrms-mini-item strong{font-size:13px;color:#121212}
.hrms-mini-item span{font-size:12px;color:#8a8a8a}
.hrms-mini-pill{
    min-width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;
    background:#fff3eb;color:#fe5f04;font-weight:800;font-size:14px;
}
.hrms-stats{
    display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px;
}
.hrms-stat-card{padding:20px 22px}
.hrms-stat-label{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#989898}
.hrms-stat-value{margin-top:10px;font-size:30px;font-weight:800;color:#121212;line-height:1}
.hrms-stat-meta{margin-top:8px;font-size:12px;color:#7d7d7d}
.hrms-panels{
    display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;
}
.hrms-panel{padding:24px}
.hrms-panel-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;margin-bottom:18px}
.hrms-panel-title{font-size:18px;font-weight:800;color:#121212}
.hrms-panel-sub{font-size:13px;line-height:1.6;color:#7b7b7b;margin-top:6px}
.hrms-link{
    color:#fe5f04;text-decoration:none;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;
}
.hrms-feature-list{display:grid;gap:12px}
.hrms-feature{
    display:flex;gap:12px;align-items:flex-start;padding:14px;border-radius:18px;background:#faf7f4;border:1px solid #f0e9e3;
}
.hrms-feature-icon{
    width:42px;height:42px;border-radius:14px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
    background:#fff;color:#fe5f04;font-size:18px;font-weight:800;border:1px solid #f1ddd0;
}
.hrms-feature strong{display:block;font-size:14px;color:#121212}
.hrms-feature span{display:block;font-size:12px;line-height:1.6;color:#808080;margin-top:4px}
.hrms-quick-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.hrms-quick-card{
    padding:18px;border-radius:18px;text-decoration:none;background:linear-gradient(180deg,#fff 0%,#faf7f4 100%);
    border:1px solid #efe6df;transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease;
}
.hrms-quick-card:hover{transform:translateY(-2px);border-color:#f3c8a8;box-shadow:0 14px 24px rgba(18,18,18,.05)}
.hrms-quick-kicker{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#aa7b5b}
.hrms-quick-title{margin-top:8px;font-size:16px;font-weight:800;color:#121212}
.hrms-quick-copy{margin-top:6px;font-size:12px;line-height:1.6;color:#7d7d7d}

/* Department Stats */
.hrms-dept-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px}
.hrms-dept-card{padding:16px;border-radius:14px;background:#fff;border:1px solid #f0e9e3}
.hrms-dept-name{font-size:14px;font-weight:700;color:#121212}
.hrms-dept-count{font-size:24px;font-weight:800;color:#fe5f04;margin-top:4px}

/* Horizontal Department Cards */
.hrms-dept-horizontal-grid{display:grid;grid-template-columns:1fr;gap:12px}
.hrms-dept-horizontal-card{
    display:grid;grid-template-columns:1fr auto auto auto;gap:20px;align-items:center;
    padding:18px 20px;border-radius:16px;background:linear-gradient(135deg,#fff 0%,#faf7f4 100%);
    border:1px solid #f0e9e3;transition:all .18s ease;
}
.hrms-dept-horizontal-card:hover{border-color:#f3c8a8;box-shadow:0 8px 16px rgba(18,18,18,.05)}
.hrms-dept-horizontal-info{display:flex;flex-direction:column;gap:4px}
.hrms-dept-horizontal-name{font-size:16px;font-weight:800;color:#121212}
.hrms-dept-horizontal-desc{font-size:12px;color:#8a8a8a}
.hrms-dept-horizontal-stat{display:flex;flex-direction:column;align-items:center;gap:4px;padding:12px 16px;border-radius:12px;background:#fff;border:1px solid #f0e9e3}
.hrms-dept-horizontal-label{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#989898}
.hrms-dept-horizontal-value{font-size:20px;font-weight:800;color:#fe5f04;line-height:1}

/* Birthday Cards */
.hrms-birthday-grid{display:grid;gap:12px}
.hrms-birthday-card{display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:#fff;border:1px solid #f0e9e3}
.hrms-birthday-avatar{width:48px;height:48px;border-radius:12px;background:#fe5f04;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:800}
.hrms-birthday-info{flex:1}
.hrms-birthday-name{font-size:14px;font-weight:700;color:#121212}
.hrms-birthday-role{font-size:12px;color:#7d7d7d;margin-top:2px}

/* Holiday Cards */
.hrms-holiday-list{display:flex;flex-direction:column;gap:10px}
.hrms-holiday-item{display:flex;gap:12px;align-items:center;padding:12px;border-radius:14px;background:#fff;border:1px solid #f0e9e3}
.hrms-holiday-date{width:60px;height:60px;border-radius:12px;background:#fe5f04;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:800;text-align:center}
.hrms-holiday-info{flex:1}
.hrms-holiday-name{font-size:14px;font-weight:700;color:#121212}
.hrms-holiday-desc{font-size:12px;color:#7d7d7d;margin-top:2px}

/* Charts */
.hrms-chart-container{height:300px;position:relative}
.hrms-chart-legend{display:flex;gap:20px;justify-content:center;margin-top:16px}
.hrms-chart-legend-item{display:flex;align-items:center;gap:6px;font-size:12px;color:#7d7d7d}
.hrms-chart-dot{width:8px;height:8px;border-radius:50%}

/* Announcements */
.hrms-announcement-list{display:flex;flex-direction:column;gap:12px}
.hrms-announcement-item{padding:16px;border-radius:16px;border:1px solid #f0e9e3}
.hrms-announcement-priority-high{background:linear-gradient(135deg,#fef2f2,#fee2e2);border-color:#fecaca}
.hrms-announcement-priority-medium{background:linear-gradient(135deg,#fefce8,#fde68a);border-color:#fde68a}
.hrms-announcement-title{font-size:14px;font-weight:700;color:#121212;margin-bottom:4px}
.hrms-announcement-message{font-size:13px;color:#7d7d7d;margin-bottom:8px}
.hrms-announcement-date{font-size:11px;color:#9ca3af}

@media (max-width: 1080px){
    .hrms-hero,.hrms-panels{grid-template-columns:1fr}
    .hrms-stats{grid-template-columns:repeat(3,minmax(0,1fr))}
    .hrms-dept-horizontal-card{grid-template-columns:1fr auto}
}
@media (max-width: 720px){
    .hrms-dashboard{padding:18px}
    .hrms-title{font-size:28px}
    .hrms-stats,.hrms-quick-grid{grid-template-columns:1fr}
    .hrms-hero-card,.hrms-aside,.hrms-panel,.hrms-stat-card{padding:20px}
    .hrms-dept-horizontal-card{grid-template-columns:1fr;gap:12px}
}
</style>
@endpush

@section('content')
<div class="hrms-dashboard">
    <div class="hrms-shell">


        <!-- Key Metrics -->
        <section class="hrms-stats">
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Total Employees</div>
                <div class="hrms-stat-value">{{ $stats['employees_total'] }}</div>
                <div class="hrms-stat-meta">Active workforce</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Present Today</div>
                <div class="hrms-stat-value">{{ $stats['today_present'] }}</div>
                <div class="hrms-stat-meta">Marked present</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Late Today</div>
                <div class="hrms-stat-value">{{ $stats['today_late'] }}</div>
                <div class="hrms-stat-meta">Late arrivals</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Absent Today</div>
                <div class="hrms-stat-value">{{ $stats['today_absent'] }}</div>
                <div class="hrms-stat-meta">Not present</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Interns</div>
                <div class="hrms-stat-value">{{ $stats['interns_total'] }}</div>
                <div class="hrms-stat-meta">Intern workforce</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Pending Requests</div>
                <div class="hrms-stat-value">{{ $stats['employees_pending'] }}</div>
                <div class="hrms-stat-meta">Awaiting approval</div>
            </div>
        </section>

        <!-- Main Content Panels -->
        <section class="hrms-panels">
            <!-- Department-wise Employee Count & Salary -->


            <!-- Today's Birthdays -->
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">🎂 Today's Birthdays</div>
                        <div class="hrms-panel-sub">Celebrate with your colleagues</div>
                    </div>
                </div>
                <div class="hrms-birthday-grid">
                    @forelse($stats['today_birthdays'] as $employee)
                    <div class="hrms-birthday-card">
                        <div class="hrms-birthday-avatar">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                        <div class="hrms-birthday-info">
                            <div class="hrms-birthday-name">{{ $employee->name }}</div>
                            <div class="hrms-birthday-role">{{ $employee->role->name ?? 'Employee' }}</div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:20px;color:#9ca3af;">
                        No birthdays today
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Holidays -->
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">📅 Upcoming Holidays</div>
                        <div class="hrms-panel-sub">Next 7 days schedule</div>
                    </div>
                </div>
                <div class="hrms-holiday-list">
                    @forelse($stats['upcoming_holidays'] as $holiday)
                    <div class="hrms-holiday-item">
                        <div class="hrms-holiday-date">
                            <div>{{ $holiday->holiday_date->format('M') }}</div>
                            <div>{{ $holiday->holiday_date->format('d') }}</div>
                        </div>
                        <div class="hrms-holiday-info">
                            <div class="hrms-holiday-name">{{ $holiday->reason }}</div>
                            <div class="hrms-holiday-desc">{{ $holiday->holiday_date->format('l, F j, Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:20px;color:#9ca3af;">
                        No upcoming holidays
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Today's Attendance Chart -->
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">📊 Today's Attendance</div>
                        <div class="hrms-panel-sub">Visual breakdown of attendance status</div>
                    </div>
                </div>
                <div class="hrms-chart-container">
                    <canvas id="attendanceChart"></canvas>
                </div>
                <div class="hrms-chart-legend">
                    <div class="hrms-chart-legend-item">
                        <div class="hrms-chart-dot" style="background:#10b981"></div>
                        <span>Present ({{ $stats['today_present'] }})</span>
                    </div>
                    <div class="hrms-chart-legend-item">
                        <div class="hrms-chart-dot" style="background:#f59e0b"></div>
                        <span>Late ({{ $stats['today_late'] }})</span>
                    </div>
                    <div class="hrms-chart-legend-item">
                        <div class="hrms-chart-dot" style="background:#ef4444"></div>
                        <span>Absent ({{ $stats['today_absent'] }})</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Leave Chart -->
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">📈 Monthly Leave Trends</div>
                        <div class="hrms-panel-sub">Leave requests over the past 6 months</div>
                    </div>
                </div>
                <div class="hrms-chart-container">
                    <canvas id="leaveChart"></canvas>
                </div>
            </div>

            <!-- Payroll Information -->
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">💰 Payroll Overview</div>
                        <div class="hrms-panel-sub">Monthly salary processing schedule</div>
                    </div>
                </div>
                <div class="hrms-feature-list">
                    <div class="hrms-feature">
                        <div class="hrms-feature-icon">1️⃣</div>
                        <div>
                            <strong>1st of Month</strong>
                            <span>{{ $stats['salary_day_1_employees'] }} employees receive salary</span>
                        </div>
                    </div>
                    <div class="hrms-feature">
                        <div class="hrms-feature-icon">🔟</div>
                        <div>
                            <strong>10th of Month</strong>
                            <span>{{ $stats['salary_day_10_employees'] }} employees receive salary</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements -->
            <div class="hrms-card hrms-panel" style="grid-column: span 2;">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">📢 Announcements</div>
                        <div class="hrms-panel-sub">Important updates and notices</div>
                    </div>
                </div>
                <div class="hrms-announcement-list">
                    @foreach($stats['announcements'] as $announcement)
                    <div class="hrms-announcement-item hrms-announcement-priority-{{ $announcement['priority'] }}">
                        <div class="hrms-announcement-title">{{ $announcement['title'] }}</div>
                        <div class="hrms-announcement-message">{{ $announcement['message'] }}</div>
                        <div class="hrms-announcement-date">{{ \Carbon\Carbon::parse($announcement['date'])->format('M j, Y') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">⚡ Quick Actions</div>
                        <div class="hrms-panel-sub">Frequently used HR operations</div>
                    </div>
                </div>
                <div class="hrms-quick-grid">
                    <a href="{{ route('employee-onboarding.index') }}" class="hrms-quick-card">
                        <div class="hrms-quick-kicker">Employee</div>
                        <div class="hrms-quick-title">Employee Management</div>
                        <div class="hrms-quick-copy">View and manage all employee records</div>
                    </a>
                    <a href="{{ route('attendance.index') }}" class="hrms-quick-card">
                        <div class="hrms-quick-kicker">Attendance</div>
                        <div class="hrms-quick-title">Daily Attendance</div>
                        <div class="hrms-quick-copy">Monitor employee attendance records</div>
                    </a>
                    <a href="{{ route('interns.index') }}" class="hrms-quick-card">
                        <div class="hrms-quick-kicker">Intern</div>
                        <div class="hrms-quick-title">Intern Management</div>
                        <div class="hrms-quick-copy">Track intern joining forms and progress</div>
                    </a>
                    <a href="{{ route('settings.departments.index') }}" class="hrms-quick-card">
                        <div class="hrms-quick-kicker">Department</div>
                        <div class="hrms-quick-title">Department Setup</div>
                        <div class="hrms-quick-copy">Manage organizational departments</div>
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Today's Attendance Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Present', 'Late', 'Absent'],
        datasets: [{
            data: [{{ $stats['today_present'] }}, {{ $stats['today_late'] }}, {{ $stats['today_absent'] }}],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Monthly Leave Chart
const leaveCtx = document.getElementById('leaveChart').getContext('2d');
const leaveData = @json($stats['monthly_leave_data']);
new Chart(leaveCtx, {
    type: 'line',
    data: {
        labels: leaveData.map(item => item.month),
        datasets: [{
            label: 'Leave Requests',
            data: leaveData.map(item => item.leaves),
            borderColor: '#fe5f04',
            backgroundColor: 'rgba(254, 95, 4, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
