@extends('layouts.app')

@section('title', 'Settings - myAgenci.ai')

@push('styles')
<style>
.settings-page {
    min-height: 100%;
    padding: 28px;
    background:
        radial-gradient(circle at top left, rgba(254, 95, 4, 0.10), transparent 30%),
        linear-gradient(180deg, #f8f6f2 0%, #f3f5f8 100%);
}
.settings-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 24px;
    padding: 28px;
    border: 1px solid #e6e8ee;
    border-radius: 22px;
    background: linear-gradient(135deg, #fff9f3 0%, #ffffff 55%, #f7f9fc 100%);
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.05);
}
.settings-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    border-radius: 999px;
    background: #fff1e8;
    color: #c2410c;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .7px;
    text-transform: uppercase;
    margin-bottom: 12px;
}
.settings-title {
    margin: 0;
    font-size: 30px;
    font-weight: 800;
    line-height: 1.1;
    color: #111827;
}
.settings-subtitle {
    margin: 10px 0 0;
    max-width: 640px;
    font-size: 14px;
    line-height: 1.7;
    color: #6b7280;
}
.settings-glance {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.settings-glance-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid #ece7e3;
    background: rgba(255, 255, 255, 0.92);
    color: #4b5563;
    font-size: 12px;
    font-weight: 700;
}
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 18px;
}
.settings-card {
    position: relative;
    overflow: hidden;
    display: block;
    padding: 22px;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
    text-decoration: none;
    color: inherit;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}
.settings-card:hover {
    transform: translateY(-4px);
    border-color: #fed7aa;
    box-shadow: 0 18px 36px rgba(254, 95, 4, 0.12);
}
.settings-card::after {
    content: '';
    position: absolute;
    right: -28px;
    bottom: -28px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    opacity: .15;
}
.settings-card.status::after { background: #f59e0b; }
.settings-card.source::after { background: #f97316; }
.settings-card.outcome::after { background: #ec4899; }
.settings-card.subcategory::after { background: #3b82f6; }
.settings-card.product-category::after { background: #8b5cf6; }
.settings-card.attribute::after { background: #14b8a6; }
.settings-card.quotation::after { background: #0ea5e9; }
.settings-card.facebook::after { background: #2563eb; }
.settings-card.department::after { background: #059669; }
.settings-card.holiday::after { background: #ef4444; }
.settings-card-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    font-size: 22px;
}
.settings-card-icon.status {
    background: linear-gradient(135deg, #fff7ed, #fef3c7);
    color: #b45309;
}
.settings-card-icon.source {
    background: linear-gradient(135deg, #fff7ed, #ffedd5);
    color: #ea580c;
}
.settings-card-icon.outcome {
    background: linear-gradient(135deg, #fdf2f8, #fce7f3);
    color: #db2777;
}
.settings-card-icon.subcategory {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: #2563eb;
}
.settings-card-icon.product-category {
    background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    color: #7c3aed;
}
.settings-card-icon.attribute {
    background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
    color: #0f766e;
}
.settings-card-icon.quotation {
    background: linear-gradient(135deg, #eff6ff, #e0f2fe);
    color: #0369a1;
}
.settings-card-icon.facebook {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: #1d4ed8;
}
.settings-card-icon.department {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    color: #047857;
}
.settings-card-icon.holiday {
    background: linear-gradient(135deg, #fff1f2, #ffe4e6);
    color: #be123c;
}
.settings-card-title {
    margin: 0 0 8px;
    font-size: 17px;
    font-weight: 800;
    color: #111827;
}
.settings-card-text {
    margin: 0;
    max-width: 280px;
    font-size: 13px;
    line-height: 1.7;
    color: #6b7280;
}
.settings-card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    font-size: 12px;
    font-weight: 800;
    color: #111827;
}
@media (max-width: 768px) {
    .settings-page {
        padding: 18px;
    }
    .settings-hero {
        padding: 22px;
        flex-direction: column;
        align-items: flex-start;
    }
    .settings-title {
        font-size: 24px;
    }
}
</style>
@endpush

@section('content')
<div class="settings-page">
    <div class="settings-hero">
        <div>
            <div class="settings-kicker">
                <i class="bi bi-sliders2"></i>
                Workspace Setup
            </div>
            <h2 class="settings-title">Settings</h2>
            <p class="settings-subtitle">Manage the core CRM options your team relies on every day, from lead flow and outcomes to product setup, quotations, and integrations.</p>
        </div>

        <div class="settings-glance">
            <div class="settings-glance-item">
                <i class="bi bi-diagram-3"></i>
                Lead Pipeline
            </div>
            <div class="settings-glance-item">
                <i class="bi bi-box-seam"></i>
                Product Setup
            </div>
            <div class="settings-glance-item">
                <i class="bi bi-plug"></i>
                Integrations
            </div>
        </div>
    </div>

    <div class="settings-grid">

        @can('lead_status.menuview')
        <a href="{{ route('settings.lead-statuses.index') }}" class="settings-card status">
            <div class="settings-card-icon status">
                <i class="bi bi-signpost-split"></i>
            </div>
            <h4 class="settings-card-title">Lead Status</h4>
            <p class="settings-card-text">Organize pipeline stages so teams can track lead progress clearly from new enquiry to closure.</p>
            <span class="settings-card-link">
                Open Lead Status
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('lead_source.menuview')
        <a href="{{ route('settings.lead-sources.index') }}" class="settings-card source">
            <div class="settings-card-icon source">
                <i class="bi bi-broadcast-pin"></i>
            </div>
            <h4 class="settings-card-title">Lead Source</h4>
            <p class="settings-card-text">Track which campaigns, channels, and referrals are generating incoming leads for the business.</p>
            <span class="settings-card-link">
                Open Lead Source
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('outcome_category.menuview')
        <a href="{{ route('settings.outcome-categories.index') }}" class="settings-card outcome">
            <div class="settings-card-icon outcome">
                <i class="bi bi-collection"></i>
            </div>
            <h4 class="settings-card-title">Outcome Category</h4>
            <p class="settings-card-text">Group lead or call outcomes into clean top-level buckets for easier reporting and follow-up rules.</p>
            <span class="settings-card-link">
                Open Outcome Categories
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('outcome_sub_category.menuview')
        <a href="{{ route('settings.outcome-sub-categories.index') }}" class="settings-card subcategory">
            <div class="settings-card-icon subcategory">
                <i class="bi bi-diagram-2"></i>
            </div>
            <h4 class="settings-card-title">Outcome Sub Category</h4>
            <p class="settings-card-text">Add more detailed outcome labels under each category to refine your team's tracking and analysis.</p>
            <span class="settings-card-link">
                Open Sub Categories
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('product_category.menuview')
        <a href="{{ route('settings.product-category.index') }}" class="settings-card product-category">
            <div class="settings-card-icon product-category">
                <i class="bi bi-grid-1x2"></i>
            </div>
            <h4 class="settings-card-title">Product Category</h4>
            <p class="settings-card-text">Structure your catalog into well-defined product groups so configuration and reporting stay manageable.</p>
            <span class="settings-card-link">
                Open Product Categories
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('product_attributes.menuview')
        <a href="{{ route('settings.product-attribute.index') }}" class="settings-card attribute">
            <div class="settings-card-icon attribute">
                <i class="bi bi-sliders"></i>
            </div>
            <h4 class="settings-card-title">Product Attributes</h4>
            <p class="settings-card-text">Define attribute sets and configurable product details your sales and quotation workflows depend on.</p>
            <span class="settings-card-link">
                Open Product Attributes
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('departments.menuview')
        <a href="{{ route('settings.departments.index') }}" class="settings-card department">
            <div class="settings-card-icon department">
                <i class="bi bi-people"></i>
            </div>
            <h4 class="settings-card-title">Departments</h4>
            <p class="settings-card-text">Maintain department master data for HR structure, onboarding alignment, and employee organization.</p>
            <span class="settings-card-link">
                Open Departments
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('holiday_calendar.menuview')

        <a href="{{ route('settings.holiday-calendars.index') }}" class="settings-card holiday">
            <div class="settings-card-icon holiday">
                <i class="bi bi-calendar-event"></i>
            </div>
            <h4 class="settings-card-title">Holiday Calendar</h4>
            <p class="settings-card-text">Manage company holidays, import annual calendars from Excel, and view them in a monthly calendar layout.</p>
            <span class="settings-card-link">
                Open Holiday Calendar
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('quotation_settings.menuview')
        <a href="/settings/quotation-setting" class="settings-card quotation">
            <div class="settings-card-icon quotation">
                <i class="bi bi-file-earmark-richtext"></i>
            </div>
            <h4 class="settings-card-title">Quotation Settings</h4>
            <p class="settings-card-text">Control branding, numbering, company information, terms, and signatures used in generated quotations.</p>
            <span class="settings-card-link">
                Open Quotation Settings
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan

        @can('facebook_integration.menuview')
        <a href="/settings/facebook-integration" class="settings-card facebook">
            <div class="settings-card-icon facebook">
                <i class="bi bi-facebook"></i>
            </div>
            <h4 class="settings-card-title">Facebook Integration</h4>
            <p class="settings-card-text">Connect campaign flows and manage mapped lead intake from Facebook forms inside your CRM.</p>
            <span class="settings-card-link">
                Open Facebook Integration
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>
        @endcan
    </div>
</div>
@endsection
