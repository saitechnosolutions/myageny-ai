@extends('layouts.app')

@section('title', 'Quotations')

@push('styles')
<style>
.page-wrapper { padding: 32px; }
.page-header {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 24px;
}
.page-title { font-size: 22px; font-weight: 700; color: #121212; }
.btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    background-color: #fe5f04; color: #fff;
    padding: 8px 18px; border-radius: 20px;
    font-size: 14px; font-weight: 600; border: none; cursor: pointer;
    text-decoration: none;
}
.btn-primary:hover { background-color: #e05400; }
.btn-sm {
    padding: 4px 12px; font-size: 12px; border-radius: 12px;
}
.card {
    background: #fff; border: 1px solid #e1dee3;
    border-radius: 14px; overflow: hidden;
}
.filter-card {
    background: #fff; border: 1px solid #e1dee3; border-radius: 14px;
    padding: 18px; margin-bottom: 20px;
}
.filter-form {
    display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)) auto auto;
    gap: 12px; align-items: end;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 12px; font-weight: 600; color: #666; }
.filter-input, .filter-select {
    width: 100%; padding: 10px 12px; border: 1px solid #e1dee3; border-radius: 10px;
    font-size: 13px; color: #121212; background: #fff; outline: none;
}
.filter-input:focus, .filter-select:focus {
    border-color: #fe5f04; box-shadow: 0 0 0 3px rgba(254, 95, 4, 0.10);
}
.btn-filter-reset {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 600;
    border: 1px solid #e1dee3; color: #444; text-decoration: none; background: #fff;
}
.btn-filter-reset:hover { border-color: #cfc9d2; background: #fafafa; }
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #f8f8f8; }
th {
    padding: 12px 16px; font-size: 12px; font-weight: 600;
    color: #7c7c7c; text-align: left; border-bottom: 1px solid #e1dee3;
}
td {
    padding: 13px 16px; font-size: 13px; color: #121212;
    border-bottom: 1px solid #f1f1f1; vertical-align: middle;
}
tbody tr:hover { background: #fdf8f5; }
tbody tr:last-child td { border-bottom: none; }
.badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
}
.badge-approved  { background: #edfaf3; color: #1a7a52; border: 1px solid #b6ead0; }
.badge-pending   { background: #fff8ec; color: #9a6200; border: 1px solid #ffd98a; }
.action-btns { display: flex; gap: 6px; }
.btn-outline-sm {
    padding: 4px 12px; border-radius: 10px; font-size: 12px;
    font-weight: 500; border: 1px solid #e1dee3;
    background: #fff; cursor: pointer; text-decoration: none; color: #444;
}
.btn-outline-sm:hover { border-color: #fe5f04; color: #fe5f04; }
.btn-danger-sm {
    padding: 4px 12px; border-radius: 10px; font-size: 12px;
    font-weight: 500; border: 1px solid #ffc4c4;
    background: #fff5f5; cursor: pointer; color: #c00;
    text-decoration: none;
}
.alert-success {
    background: #edfaf3; border: 1px solid #b6ead0; color: #1a7a52;
    padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;
}
.empty-state { text-align: center; padding: 60px 20px; color: #9e9e9e; }
.empty-state i { font-size: 42px; margin-bottom: 12px; display: block; }
@media (max-width: 1200px) {
    .filter-form { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
    .filter-form { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="page-wrapper">

    {{-- Header --}}
    <div class="page-header">
        <div class="page-title">Quotations</div>

    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('quotations.index') }}" class="filter-form">
            @if(request('lead_id'))
                <input type="hidden" name="lead_id" value="{{ request('lead_id') }}">
            @endif

            <div class="filter-group">
                <label class="filter-label" for="quotation_no">Quotation No</label>
                <input
                    type="text"
                    id="quotation_no"
                    name="quotation_no"
                    class="filter-input"
                    value="{{ request('quotation_no') }}"
                    placeholder="Search quotation no">
            </div>

            <div class="filter-group">
                <label class="filter-label" for="start_date">Start Date</label>
                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    class="filter-input"
                    value="{{ request('start_date') }}">
            </div>

            <div class="filter-group">
                <label class="filter-label" for="end_date">End Date</label>
                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    class="filter-input"
                    value="{{ request('end_date') }}">
            </div>

            <div class="filter-group">
                <label class="filter-label" for="status">Status</label>
                <select id="status" name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="approved_by">Approved By</label>
                <select id="approved_by" name="approved_by" class="filter-select">
                    <option value="">All Users</option>
                    @foreach($approvers as $approver)
                        <option value="{{ $approver->id }}" @selected((string) request('approved_by') === (string) $approver->id)>
                            {{ $approver->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary">Apply Filter</button>
            <a href="{{ route('quotations.index', request()->filled('lead_id') ? ['lead_id' => request('lead_id')] : []) }}" class="btn-filter-reset">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Quotation No</th>
                        <th>Lead</th>
                        <th>Date</th>
                        <th>Valid Until</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                    <tr>
                        <td>{{ $loop->iteration + ($quotations->firstItem() - 1) }}</td>
                        <td><strong>{{ $q->quotation_no }}</strong></td>
                        <td>{{ $q->lead?->contact_name ?? '—' }}<br>
                            <small style="color:#9e9e9e">{{ $q->lead->company_name ?? '' }}</small>
                        </td>
                        <td>{{ $q->quotation_date->format('d M Y') }}</td>
                        <td>{{ $q->valid_until->format('d M Y') }}</td>
                        <td><strong>₹{{ number_format($q->total_amount, 2) }}</strong></td>
                        <td>
                            @if($q->is_approved)
                                <span class="badge badge-approved"><i class="bi bi-check-circle-fill"></i> Approved</span>
                            @else
                                <span class="badge badge-pending"><i class="bi bi-clock-fill"></i> Pending</span>
                            @endif
                        </td>
                        <td>{{ $q->approver->name ?? '—' }}</td>
                        <td>
                            <div class="action-btns">
                                <a href="/quotations/{{ $q->id }}/pdf" class="btn-outline-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if(!$q->is_approved)
                                <form method="POST" action="{{ route('quotations.approve', $q) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <button class="btn-outline-sm" style="border-color:#4caf50;color:#2e7d32">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('quotations.destroy', $q) }}"
                                      onsubmit="return confirm('Delete this quotation?')" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="btn-danger-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-file-earmark-text"></i>
                                No quotations found.
                                <br><br>
                                <a href="/quotations/create" class="btn-primary" style="display:inline-flex">
                                    Create First Quotation
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($quotations->hasPages())
        <div style="padding: 0;">
            @include('partials.table-pagination', ['paginator' => $quotations])
        </div>
        @endif
    </div>
</div>
@endsection
