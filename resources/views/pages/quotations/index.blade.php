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
                        <td>{{ $q->lead->name ?? '—' }}<br>
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
                                <a href="{{ route('quotations.create') }}" class="btn-primary" style="display:inline-flex">
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
        <div style="padding: 16px 20px; border-top: 1px solid #f1f1f1;">
            {{ $quotations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
