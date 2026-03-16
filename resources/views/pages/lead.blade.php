@extends('layouts.app')

@section('title', 'myAgenci.ai - Lead')

@push('styles')
<style>
/* ===== LEAD PAGE STYLES (from 8797ac5f/index.html) ===== */

/* Layout */
.dashboard-container {
    display: flex;
    width: 100%;
    max-width: 1440px;
    margin: 0 auto;
    min-height: 100vh;
    background-color: #fcfcfc;
}

/* Main Content */
.main-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.lead-content {
    flex-grow: 1;
    overflow-y: auto;
    padding: 32px;
}
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 32px;
    border-bottom: 1px solid #e1dee3;
    height: 72px;
    background-color: var(--color-bg);
    position: sticky;
    top: 0;
    z-index: 10;
}
.breadcrumbs { display: flex; align-items: center; gap: 2px; font-size: 14px; }
.crumb-item { color: #9e9e9e; }
.crumb-item.active { color: #121212; font-weight: 500; }
.header-actions { display: flex; align-items: center; gap: 12px; }
.icon-btn-group { display: flex; gap: 12px; }
.icon-btn {
    width: 32px; height: 32px; border: 1px solid #e1dee3;
    border-radius: 16px; display: flex; align-items: center;
    justify-content: center; background-color: #fcfcfc;
}
.btn-ai-insight {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px;
    background: #ffffff;
    border: 1.5px solid #fa6203; cursor: pointer;
}
.btn-text { color: #fa6203; font-size: 14px; font-weight: 600; }
.user-menu { display: flex; align-items: center; gap: 4px; cursor: pointer; }
.content-body { padding: 32px; overflow-y: auto; }
.section-title { font-size: 18px; font-weight: 600; color: #121212; margin-bottom: 16px; }

/* Cards Row */
.cards-row { display: flex; gap: 12px; margin-bottom: 40px; }
.card {
    flex: 1; background-color: #fcfcfc; border: 1px solid #e1dee3;
    border-radius: 12px; padding: 12px 16px;
    display: flex; align-items: center; gap: 12px;
}
.card-icon-bg { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.bg-yellow { background-color: #f0eadb; }
.bg-orange { background-color: #f3e6da; }
.bg-pink { background-color: #f5e0e6; }
.card-content { display: flex; flex-direction: column; gap: 4px; }
.card-header { display: flex; align-items: center; gap: 8px; }
.card-title { font-size: 14px; font-weight: 600; color: #121212; }
.card-subtitle { font-size: 12px; color: #7c7c7c; }

/* Table Section */
.table-section {
    background-color: #fcfcfc; border: 1px solid #e1dee3;
    border-radius: 12px; padding: 4px;
}
.table-toolbar {
    display: flex; justify-content: space-between;
    align-items: flex-end; padding: 16px 20px; margin-bottom: 10px;
}
.table-title { font-size: 18px; font-weight: 600; color: #121212; margin: 0 0 4px 0; }
.table-subtitle { font-size: 14px; color: #7c7c7c; margin: 0; }
.toolbar-actions { display: flex; align-items: center; gap: 8px; }
.action-group { display: flex; gap: 8px; }
.action-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 12px; background-color: #fcfcfc;
    border: 1px solid #e1dee3; border-radius: 16px;
    font-size: 14px; color: #9e9e9e; cursor: pointer;
}
.selected-indicator {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 12px; background-color: #ffd3b9;
    border-radius: 16px; color: #863506; font-size: 14px; font-weight: 500;
}

/* Data Table */
.data-table-container { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
.data-table-container th {
    background-color: #f8f8f8; padding: 12px 16px;
    font-size: 12px; color: #7c7c7c; font-weight: 500;
    text-align: left;
}
.data-table-container tr { border-radius: 8px; }
.data-table-container td { padding: 12px 16px; font-size: 14px; vertical-align: middle; background-color: #ffffff; }
.data-table-container tr td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
.data-table-container tr td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; height: 60px;}

.header-row th {
    border: 1px solid #f1f1f1;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
    height: 60px;
}
.data-table-container tbody tr:hover td {
    background: linear-gradient(90deg, #FFAB7A 0%, #F4F4F4 34%, #FFCBAD 100%);
    opacity: 0.9;
    cursor: pointer;
}
.text-dark { color: #121212; font-weight: 500; }
.flex-cell { display: flex; align-items: center; gap: 8px; }
.cell-avatar { width: 21px; height: 21px; }

/* Column Widths (Approximated for Table) */
.col-check { width: 30px; }
.col-menu { width: 30px; }
.col-id { width: 80px; }
.col-source { width: 100px; }
.col-company { width: 120px; }
.col-contact { width: 160px; }
.col-deal { width: 100px; }
.col-amount { width: 100px; }
.col-stage { width: 100px; }
.col-owner { width: 150px; }
.col-icons { text-align: right; display: flex; gap: 8px; justify-content: flex-end; align-items: center; }

.action-icon {
    font-size: 18px; color: #7c7c7c;
    cursor: pointer; transition: color 0.2s;
    vertical-align: middle;
}
.action-icon:hover { color: #ff7860; }

/* Badges */
.badge {
    display: flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 20px;
    font-size: 12px; font-weight: 500;
}
.badge-dot { width: 8px; height: 8px; border-radius: 50%; }
.badge-icon { width: 12px; height: 12px; display: flex; align-items: center; justify-content: center; }
.badge-icon img { width: 100%; }
.badge-green { background-color: #f1fdf6; border: 1px solid #e6f6ed; color: #225247; }
.badge-dot.green { background-color: #469d89; }
.badge-red { background-color: #ffdfdf; border: 1px solid #ff9696; color: #ff5a55; }
.badge-blue { background-color: #f5f5ff; border: 1px solid #46479d; color: #46479d; }
.badge-dot.blue { background-color: #46479d; }
.badge-orange-light { background-color: #fff8f4; border: 1px solid #fde5d8; color: #fe5f04; }
.badge-dot.orange { background-color: #fe5f04; }
.badge-purple-light { background-color: #faf4ff; border: 1px solid #f3edf9; color: #41225d; }
.badge-icon.purple { background-color: #60308c; border-radius: 50%; padding: 2px; }
.badge-teal-light { background-color: #ebfffd; border: 1px solid #82deb9; color: #29a875; }

.checkbox {
    width: 14px; height: 14px; background-color: #f9f9f9;
    border: 1px solid #e1dee3; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
}
.checkbox img { display: none; width: 10px; pointer-events: none; }
.checkbox.checked { background-color: #ff7860; border-color: #f96b19; }
.checkbox.checked img { display: block; }
.menu-dots {
    width: 14px; height: 14px; background-color: #d9d9d9;
    mask: url('{{ asset("images/42_3178.svg") }}') no-repeat center;
    -webkit-mask: url('{{ asset("images/42_3178.svg") }}') no-repeat center;
    mask-size: contain; -webkit-mask-size: contain;
}
</style>
@endpush

@section('content')
<main class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="breadcrumbs">
            <span class="crumb-item">Home</span>
            <img src="{{ asset('images/42_3018.svg') }}" alt="/" class="crumb-sep">
            <span class="crumb-item active">Lead</span>
        </div>
        <div class="header-actions">
            <div class="icon-btn-group">
                <div class="icon-btn"><img src="{{ asset('images/42_3022.svg') }}" alt="Messages"></div>
                <div class="icon-btn"><img src="{{ asset('images/42_3024.svg') }}" alt="Notifications"></div>
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

    <div class="lead-content">
        <div class="section-title">Get Started</div>

        <!-- Cards -->
        <div class="cards-row">
            <div class="card">
                <div class="card-icon-bg bg-yellow">
                    <img src="{{ asset('images/42_3034.svg') }}" alt="Icon">
                </div>
                <div class="card-content">
                    <div class="card-header">
                        <span class="card-title">Leads Allocation</span>
                        <img src="{{ asset('images/42_3038.svg') }}" alt="Plus">
                    </div>
                    <span class="card-subtitle">Leads Allocate to Someone</span>
                </div>
            </div>
            <div class="card">
                <div class="card-icon-bg bg-orange">
                    <img src="{{ asset('images/42_3042.svg') }}" alt="Icon">
                </div>
                <div class="card-content">
                    <div class="card-header">
                        <span class="card-title">Permission Managem.</span>
                        <img src="{{ asset('images/42_3046.svg') }}" alt="Plus">
                    </div>
                    <span class="card-subtitle">Permission | </span>
                </div>
            </div>
            <div class="card">
                <div class="card-icon-bg bg-pink">
                    <img src="{{ asset('images/42_3050.svg') }}" alt="Icon">
                </div>
                <div class="card-content">
                    <div class="card-header">
                        <span class="card-title">Holiday Management</span>
                        <img src="{{ asset('images/42_3054.svg') }}" alt="Plus">
                    </div>
                    <span class="card-subtitle">Leave Calendar </span>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-toolbar">
                <div class="table-info">
                    <h2 class="table-title">Leads Sheet</h2>
                    <p class="table-subtitle">Recent automation runs across workflows and systems</p>
                </div>
                <div class="toolbar-actions">
                    <div class="action-group">
                        <div class="action-btn">
                            <img src="{{ asset('images/42_3494.svg') }}" alt="Filter">
                        </div>
                        <div class="action-btn">
                            <span>Export</span>
                            <img src="{{ asset('images/42_3497.svg') }}" alt="Download">
                        </div>
                    </div>
                    <div class="selected-indicator">
                        <span>1 selected</span>
                        <img src="{{ asset('images/42_3500.svg') }}" alt="Close">
                    </div>
                </div>
            </div>

            <table class="data-table-container">
                <thead>
                    <tr class="header-row">
                        <th class="col-check"><div class="checkbox"></div></th>
                        <th class="col-menu"><div class="menu-dots"></div></th>
                        <th class="col-id">ID</th>
                        <th class="col-source">Lead Source</th>
                        <th class="col-company">Company Name</th>
                        <th class="col-contact">Contact Name</th>
                        <th class="col-deal">Deal Name</th>
                        <th class="col-amount">Deal Amount</th>
                        <th class="col-stage">Stage</th>
                        <th class="col-owner">Owner Holder</th>
                        <th class="col-action">Action</th>
                        <th class="col-icons"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3194.svg') }}" class="cell-avatar" alt="Meta">
                                <span class="text-dark">Meta</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-orange-light"><div class="badge-dot orange"></div><span>Ad Campaign</span></div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-001</td>
                        <td class="col-source text-dark">Invitation</td>
                        <td class="col-company text-dark">₹2,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3217.svg') }}" class="cell-avatar" alt="VW">
                                <span class="text-dark">Volkswagen</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-red">
                                <div class="badge-icon"><img src="{{ asset('images/a47dc465019148b2f1d5d3f4121f47703c320028.png') }}" alt="x"></div>
                                <span>Lost</span>
                            </div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3230.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3242.svg') }}" class="cell-avatar" alt="VW">
                                <span class="text-dark">Volkswagen</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-teal-light"><span>Direct Visit</span></div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3264.svg') }}" class="cell-avatar" alt="VW">
                                <span class="text-dark">Volkswagen</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-blue"><div class="badge-dot blue"></div><span>New</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3275.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3287.svg') }}" class="cell-avatar" alt="V">
                                <span class="text-dark">V - Star</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3298.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 6 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3309.svg') }}" class="cell-avatar" alt="Meta">
                                <span class="text-dark">Meta</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3320.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 7 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3332.svg') }}" class="cell-avatar" alt="V">
                                <span class="text-dark">V - Star</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3343.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 8 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3354.svg') }}" class="cell-avatar" alt="Meta">
                                <span class="text-dark">Meta</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3365.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 9 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3332.svg') }}" class="cell-avatar" alt="V">
                                <span class="text-dark">V - Star</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3343.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>

                    <!-- Row 10 -->
                    <tr>
                        <td class="col-check">
                            <div class="checkbox">
                                <img src="{{ asset('images/42_3213.svg') }}" alt="✓">
                            </div>
                        </td>
                        <td class="col-menu"><div class="menu-dots"></div></td>
                        <td class="col-id text-dark">STS-002</td>
                        <td class="col-source text-dark">Logo</td>
                        <td class="col-company text-dark">₹10,000.0</td>
                        <td class="col-contact">
                            <div class="flex-cell">
                                <img src="{{ asset('images/42_3354.svg') }}" class="cell-avatar" alt="Meta">
                                <span class="text-dark">Meta</span>
                            </div>
                        </td>
                        <td class="col-deal text-dark">-</td>
                        <td class="col-amount text-dark">-</td>
                        <td class="col-stage">
                            <div class="badge badge-green"><div class="badge-dot green"></div><span>Won</span></div>
                        </td>
                        <td class="col-owner text-dark">Saravanan - BDE</td>
                        <td class="col-action">
                            <div class="badge badge-purple-light">
                                <div class="badge-icon purple"><img src="{{ asset('images/42_3365.svg') }}" alt="up"></div>
                                <span>Reference</span>
                            </div>
                        </td>
                        <td class="col-icons">
                            <i class="bi bi-arrow-clockwise action-icon" title="Reload"></i>
                            <i class="bi bi-link-45deg action-icon" title="Link"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><!-- end table-section -->
    </div><!-- end lead-content -->
</main>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent row click if added later
            this.classList.toggle('checked');
        });
    });
});
</script>
@endpush
@endsection