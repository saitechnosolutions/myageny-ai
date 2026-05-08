@extends('layouts.app')

@section('title', 'Lead Price Requests')

@push('styles')
<style>
.page-wrap{padding:32px}
.page-head{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:24px}
.page-title{font-size:22px;font-weight:700;color:#121212}
.filter-card,.table-card{background:#fff;border:1px solid #e1dee3;border-radius:14px}
.filter-card{padding:18px;margin-bottom:20px}
.filter-form{display:grid;grid-template-columns:repeat(5,minmax(0,1fr)) auto auto;gap:12px;align-items:end}
.filter-group{display:flex;flex-direction:column;gap:6px}
.filter-label{font-size:12px;font-weight:600;color:#666}
.filter-input,.filter-select,.table-input{width:100%;padding:10px 12px;border:1px solid #e1dee3;border-radius:10px;font-size:13px;background:#fff}
.filter-input:focus,.filter-select:focus,.table-input:focus{border-color:#fe5f04;box-shadow:0 0 0 3px rgba(254,95,4,.1);outline:none}
.btn-primary,.btn-reset,.btn-approve,.btn-reject{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:600;border:1px solid transparent;text-decoration:none;cursor:pointer}
.btn-primary{background:#fe5f04;color:#fff}
.btn-reset{background:#fff;border-color:#e1dee3;color:#444}
.btn-approve{background:#f0fdf4;border-color:#bbf7d0;color:#166534}
.btn-reject{background:#fef2f2;border-color:#fecaca;color:#b91c1c}
.table-responsive{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th,td{padding:14px 16px;border-bottom:1px solid #f1f1f1;text-align:left;vertical-align:top}
th{font-size:12px;font-weight:700;color:#7c7c7c;background:#fafafa}
td{font-size:13px;color:#121212}
.badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
.badge-pending{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa}
.badge-approved{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
.badge-rejected{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}
.meta{font-size:11px;color:#8a8a8a;margin-top:4px}
.money-old{font-size:12px;color:#8a8a8a}
.money-new{font-size:15px;font-weight:700;color:#121212}
.money-diff-up{font-size:12px;font-weight:700;color:#dc2626}
.money-diff-down{font-size:12px;font-weight:700;color:#15803d}
.actions{display:flex;flex-direction:column;gap:8px;min-width:180px}
.empty{padding:56px 20px;text-align:center;color:#9e9e9e}
@media (max-width:1200px){.filter-form{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:768px){.page-head{flex-direction:column;align-items:flex-start}.filter-form{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div class="page-title">Lead Price Change Requests</div>
    </div>

    @if(session('success'))
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c">
            {{ session('error') }}
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('lead-price-requests.index') }}" class="filter-form">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    @foreach(\App\Models\LeadProductPriceRequest::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Lead ID</label>
                <input type="number" name="lead_id" value="{{ request('lead_id') }}" class="filter-input" placeholder="Lead ID">
            </div>
            <div class="filter-group">
                <label class="filter-label">Requested By</label>
                <select name="requested_by" class="filter-select">
                    <option value="">All Users</option>
                    @foreach($requesters as $requester)
                        <option value="{{ $requester->id }}" @selected((string) request('requested_by') === (string) $requester->id)>{{ $requester->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
            </div>
            <button type="submit" class="btn-primary">Apply Filter</button>
            <a href="{{ route('lead-price-requests.index') }}" class="btn-reset">Reset</a>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lead / Deal</th>
                        <th>Product</th>
                        <th>Requested By</th>
                        <th>Price Change</th>
                        <th>Qty / Disc</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $priceRequest)
                        @php
                            $difference = $priceRequest->price_difference;
                            $badgeClass = $priceRequest->status === 'approved' ? 'badge-approved' : ($priceRequest->status === 'rejected' ? 'badge-rejected' : 'badge-pending');
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration + ($requests->firstItem() - 1) }}</td>
                            <td>
                                <strong>Lead #{{ $priceRequest->lead_id }}</strong>
                                <div class="meta">{{ $priceRequest->lead?->company_name ?? 'Lead' }}</div>
                                <div class="meta">Deal: {{ $priceRequest->deal_name }}</div>
                            </td>
                            <td>
                                <strong>{{ $priceRequest->product_name }}</strong>
                                @if($priceRequest->remarks)
                                    <div class="meta">Remarks: {{ $priceRequest->remarks }}</div>
                                @endif
                            </td>
                            <td>
                                {{ $priceRequest->requestedBy?->name ?? 'User' }}
                                <div class="meta">{{ $priceRequest->created_at->format('d M Y h:i A') }}</div>
                            </td>
                            <td>
                                <div class="money-old">Base: ₹{{ number_format($priceRequest->original_unit_price, 2) }}</div>
                                <div class="money-new">Asked: ₹{{ number_format($priceRequest->requested_unit_price, 2) }}</div>
                                <div class="{{ $difference > 0 ? 'money-diff-up' : 'money-diff-down' }}">
                                    {{ $difference > 0 ? '+' : '' }}₹{{ number_format($difference, 2) }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ $priceRequest->quantity }}</strong> qty
                                <div class="meta">{{ number_format($priceRequest->discount_percent, 2) }}% discount</div>
                                <div class="meta">Total: ₹{{ number_format($priceRequest->requested_total, 2) }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($priceRequest->status) }}</span>
                                @if($priceRequest->approvedBy)
                                    <div class="meta">By {{ $priceRequest->approvedBy->name }}</div>
                                @endif
                                @if($priceRequest->rejection_reason)
                                    <div class="meta">Reason: {{ $priceRequest->rejection_reason }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    @if($priceRequest->status === 'pending')
                                        <form method="POST" action="{{ route('lead-price-requests.approve', $priceRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-approve" style="width:100%">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('lead-price-requests.reject', $priceRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="rejection_reason" class="table-input" placeholder="Reject reason (optional)">
                                            <button type="submit" class="btn-reject" style="width:100%;margin-top:8px">Reject</button>
                                        </form>
                                    @else
                                        <a href="{{ route('leads.show', $priceRequest->lead_id) }}" class="btn-reset">View Lead</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty">No price change requests found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            @include('partials.table-pagination', ['paginator' => $requests])
        @endif
    </div>
</div>
@endsection
