@extends('layouts.app')
@section('title', 'Create Facility Entry')

@push('styles')
@include('pages.settings.partials.table-styles')
<style>
.crm-form-wrap { max-width:760px; margin:0 auto; background:#fff; border:1px solid #e1dee3; border-radius:16px; overflow:hidden; }
.crm-form-head { padding:20px 24px; border-bottom:1px solid #f1f1f1; }
.crm-form-body { padding:24px; display:flex; flex-direction:column; gap:18px; }
.crm-form-foot { padding:20px 24px; border-top:1px solid #f1f1f1; display:flex; justify-content:flex-end; gap:10px; }
.crm-textarea { width:100%; min-height:120px; padding:10px 14px; border:1px solid #e1dee3; border-radius:10px; font-size:14px; outline:none; font-family:inherit; resize:vertical; }
.crm-textarea:focus { border-color:#fe5f04; box-shadow:0 0 0 3px rgba(254,95,4,.1); }
</style>
@endpush

@section('content')
<main class="main-content">
    <div class="crm-page-body">
        <div class="crm-page-header">
            <div>
                <h2 class="crm-title">Create Facility Entry</h2>
                <p class="crm-subtitle">Add a new office cleaning schedule for facility management.</p>
            </div>
            <div class="crm-header-actions">
                <a href="{{ route('facility-management.index') }}" class="crm-btn crm-btn-ghost">Back</a>
            </div>
        </div>

        @include('pages.settings.partials.alert')

        <form method="POST" action="{{ route('facility-management.store') }}">
            @csrf
            <div class="crm-form-wrap">
                <div class="crm-form-head">
                    <h3 style="margin:0; font-size:16px; font-weight:700;">Facility Details</h3>
                </div>
                <div class="crm-form-body">
                    <div>
                        <label class="crm-label">Title <span class="req">*</span></label>
                        <input type="text" name="title" class="crm-input" value="{{ old('title') }}" required>
                    </div>

                    <div>
                        <label class="crm-label">Office Mopping Date</label>
                        <input type="date" name="office_mopping_date" class="crm-input" value="{{ old('office_mopping_date') }}">
                    </div>

                    <div>
                        <label class="crm-label">Office Cleaning Date</label>
                        <input type="date" name="office_cleaning_date" class="crm-input" value="{{ old('office_cleaning_date') }}">
                    </div>

                    <div>
                        <label class="crm-label">Toilet Cleaning Date</label>
                        <input type="date" name="toilet_cleaning_date" class="crm-input" value="{{ old('toilet_cleaning_date') }}">
                    </div>

                    <div>
                        <label class="crm-label">Remarks</label>
                        <textarea name="remarks" class="crm-textarea" placeholder="Optional notes or cleaning instructions">{{ old('remarks') }}</textarea>
                    </div>
                </div>
                <div class="crm-form-foot">
                    <a href="{{ route('facility-management.index') }}" class="crm-btn crm-btn-ghost">Cancel</a>
                    <button type="submit" class="crm-btn crm-btn-primary">Create Entry</button>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection
