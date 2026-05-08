@extends('layouts.app')

@section('title', 'New Lead')

@push('styles')
    @include('pages.leads.form_style')
@endpush

@section('content')
<div class="lf-page">

    <div class="lf-topbar">
        <div>
            <div class="lf-title">New Lead</div>
            <div class="lf-crumb"><a href="{{ route('leads.index') }}">Leads</a> › Create</div>
        </div>
        <div class="lf-topbar-right">
            <button type="submit" form="leadForm" class="lf-btn lf-btn-primary">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Create Lead
            </button>
            <a href="{{ route('leads.index') }}" class="lf-btn lf-btn-outline">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </div>
    </div>

    <div class="lf-body">
    <form method="POST" action="{{ route('leads.store') }}" id="leadForm">
        @csrf
        @include('pages.leads.form', ['lead' => null])
    </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Priority selector
document.querySelectorAll('.lf-pri-opt').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.lf-pri-opt').forEach(o => o.className = 'lf-pri-opt');
        el.classList.add('selected-' + el.dataset.val);
        el.querySelector('input').checked = true;
    });
});

// Status selector
document.querySelectorAll('.lf-status-opt').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.lf-status-opt').forEach(o => {
            o.className = 'lf-status-opt';
        });
        el.classList.add('sel-' + el.dataset.val);
        el.querySelector('input').checked = true;
    });
});
</script>
@endpush
