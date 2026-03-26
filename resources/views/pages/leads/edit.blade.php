@extends('layouts.app')

@section('title', 'Edit Lead — ' . $lead->company_name)

@include('pages.leads.form_style')

@section('content')
<div class="lf-page">
    <div class="lf-topbar">
        <div>
            <div class="lf-title">Edit Lead</div>
            <div class="lf-crumb">
                <a href="{{ route('leads.index') }}">Leads</a> ›
                <a href="{{ route('leads.show', $lead) }}">{{ $lead->company_name }}</a> › Edit
            </div>
        </div>
        <div class="lf-topbar-right">
            <a href="{{ route('leads.show', $lead) }}" class="lf-btn lf-btn-outline">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                View Lead
            </a>
            <a href="{{ route('leads.index') }}" class="lf-btn lf-btn-outline">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </div>
    </div>

    <div class="lf-body">
        @if(session('success'))
        <div style="padding:12px 16px;border-radius:10px;font-size:13px;display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;margin-bottom:16px;">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {!! session('success') !!}
        </div>
        @endif

        <form method="POST" action="{{ route('leads.update', $lead) }}" id="leadForm">
            @csrf @method('PUT')
            @include('pages.leads.form', ['lead' => $lead])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.lf-pri-opt').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.lf-pri-opt').forEach(o => o.className='lf-pri-opt');
        el.classList.add('selected-'+el.dataset.val);
        el.querySelector('input').checked=true;
    });
});
document.querySelectorAll('.lf-status-opt').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.lf-status-opt').forEach(o => { o.className='lf-status-opt'; });
        el.classList.add('sel-'+el.dataset.val);
        el.querySelector('input').checked=true;
    });
});
</script>
@endpush
