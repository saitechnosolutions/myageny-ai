@extends('layouts.app')

@section('title', 'HRMS Dashboard')

@section('content')
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
    display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;
}
.hrms-stat-card{padding:20px 22px}
.hrms-stat-label{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#989898}
.hrms-stat-value{margin-top:10px;font-size:30px;font-weight:800;color:#121212;line-height:1}
.hrms-stat-meta{margin-top:8px;font-size:12px;color:#7d7d7d}
.hrms-panels{
    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;
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
@media (max-width: 1080px){
    .hrms-hero,.hrms-panels{grid-template-columns:1fr}
    .hrms-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width: 720px){
    .hrms-dashboard{padding:18px}
    .hrms-title{font-size:28px}
    .hrms-stats,.hrms-quick-grid{grid-template-columns:1fr}
    .hrms-hero-card,.hrms-aside,.hrms-panel,.hrms-stat-card{padding:20px}
}
</style>

<div class="hrms-dashboard">
    <div class="hrms-shell">
        <section class="hrms-hero">
            <div class="hrms-card hrms-hero-card">
                <div class="hrms-eyebrow">HRMS Workspace</div>
                <h1 class="hrms-title">Manage onboarding, interns, and daily people operations from one dashboard.</h1>
                <p class="hrms-subtitle">Use this HRMS home to jump into employee onboarding, intern joining workflows, and the most common admin actions without hunting through separate screens.</p>
                <div class="hrms-hero-actions">
                    <a href="{{ route('employee-onboarding.create') }}" class="hrms-btn hrms-btn-primary">Add Employee</a>
                    <a href="{{ route('interns.create') }}" class="hrms-btn hrms-btn-ghost">Add Intern Form</a>
                </div>
            </div>

            <aside class="hrms-card hrms-aside">
                <div>
                    <div class="hrms-aside-title">Quick Snapshot</div>
                    <div class="hrms-aside-copy">A simple overview of your current onboarding flow so HR can see what needs attention first.</div>
                </div>
                <div class="hrms-mini-list">
                    <div class="hrms-mini-item">
                        <div>
                            <strong>Pending Verifications</strong>
                            <span>Employee records waiting for review</span>
                        </div>
                        <div class="hrms-mini-pill">{{ $stats['employees_pending'] }}</div>
                    </div>
                    <div class="hrms-mini-item">
                        <div>
                            <strong>Verified Employees</strong>
                            <span>Profiles completed and approved</span>
                        </div>
                        <div class="hrms-mini-pill">{{ $stats['employees_verified'] }}</div>
                    </div>
                    <div class="hrms-mini-item">
                        <div>
                            <strong>Intern Forms</strong>
                            <span>Total intern onboarding records</span>
                        </div>
                        <div class="hrms-mini-pill">{{ $stats['interns_total'] }}</div>
                    </div>
                </div>
            </aside>
        </section>

        <section class="hrms-stats">
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Employees</div>
                <div class="hrms-stat-value">{{ $stats['employees_total'] }}</div>
                <div class="hrms-stat-meta">Total employee onboarding records</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Pending</div>
                <div class="hrms-stat-value">{{ $stats['employees_pending'] }}</div>
                <div class="hrms-stat-meta">Profiles waiting for validation</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Verified</div>
                <div class="hrms-stat-value">{{ $stats['employees_verified'] }}</div>
                <div class="hrms-stat-meta">Completed employee onboardings</div>
            </div>
            <div class="hrms-card hrms-stat-card">
                <div class="hrms-stat-label">Interns</div>
                <div class="hrms-stat-value">{{ $stats['interns_total'] }}</div>
                <div class="hrms-stat-meta">Intern joining forms on file</div>
            </div>
        </section>

        <section class="hrms-panels">
            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">Core HRMS Areas</div>
                        <div class="hrms-panel-sub">Open the main sections your HR team will use most often.</div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="hrms-link">Main Dashboard</a>
                </div>
                <div class="hrms-quick-grid">
                    <a href="{{ route('employee-onboarding.index') }}" class="hrms-quick-card">
                        <div class="hrms-quick-kicker">Employee</div>
                        <div class="hrms-quick-title">Employee Onboarding</div>
                        <div class="hrms-quick-copy">Manage employee records, documents, verification status, and profile details.</div>
                    </a>
                    <a href="{{ route('interns.index') }}" class="hrms-quick-card">
                        <div class="hrms-quick-kicker">Intern</div>
                        <div class="hrms-quick-title">Intern Joining Forms</div>
                        <div class="hrms-quick-copy">Track submitted intern forms, uploaded documents, and onboarding summaries.</div>
                    </a>
                </div>
            </div>

            <div class="hrms-card hrms-panel">
                <div class="hrms-panel-head">
                    <div>
                        <div class="hrms-panel-title">Recommended Workflow</div>
                        <div class="hrms-panel-sub">A clean handoff path for keeping people operations consistent.</div>
                    </div>
                </div>
                <div class="hrms-feature-list">
                    <div class="hrms-feature">
                        <div class="hrms-feature-icon">1</div>
                        <div>
                            <strong>Create or collect onboarding records</strong>
                            <span>Start with employee or intern intake so identity, contact details, and documents are captured early.</span>
                        </div>
                    </div>
                    <div class="hrms-feature">
                        <div class="hrms-feature-icon">2</div>
                        <div>
                            <strong>Review pending submissions</strong>
                            <span>Use the pending count to prioritize approvals, corrections, and missing documentation follow-up.</span>
                        </div>
                    </div>
                    <div class="hrms-feature">
                        <div class="hrms-feature-icon">3</div>
                        <div>
                            <strong>Move verified records forward</strong>
                            <span>Once records are clean, HR can continue the next internal steps with more confidence and less rework.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
