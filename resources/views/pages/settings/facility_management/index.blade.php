@extends('layouts.app')
@section('title', 'Facility Management')

@push('styles')
@include('pages.settings.partials.table-styles')
@endpush

@section('content')
<main class="main-content">
    <div class="crm-page-body">
        <div class="crm-page-header">
            <div>
                <h2 class="crm-title">Facility Management</h2>
                <p class="crm-subtitle">Track office mopping, office cleaning, and toilet cleaning schedules.</p>
            </div>
            <div class="crm-header-actions">
                <a href="" class="crm-btn crm-btn-ghost">Back</a>
                <a href="{{ route('facility-management.create') }}" class="crm-btn crm-btn-primary">+ Add Facility Entry</a>
            </div>
        </div>

        @include('pages.settings.partials.alert')

        <div style="margin-bottom:16px; display:flex; gap:10px; flex-wrap:wrap;">
            <form method="GET" action="{{ route('facility-management.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; width:100%;">
                <input type="text" name="search" class="crm-input" value="{{ request('search') }}" placeholder="Search facility entry title or remarks" style="max-width:360px;">
                <button type="submit" class="crm-btn crm-btn-primary">Search</button>
                @if(request()->filled('search'))
                    <a href="{{ route('facility-management.index') }}" class="crm-btn crm-btn-ghost">Reset</a>
                @endif
            </form>
        </div>

        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Office Mopping</th>
                        <th>Office Cleaning</th>
                        <th>Toilet Cleaning</th>
                        <th>Remarks</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($facilityEntries as $entry)
                    <tr>
                        <td>{{ ($facilityEntries->firstItem() ?? 1) + $loop->index }}</td>
                        <td><strong>{{ $entry->title }}</strong></td>
                        <td>{{ $entry->office_mopping_date?->format('d M Y') ?? 'N/A' }}</td>
                        <td>{{ $entry->office_cleaning_date?->format('d M Y') ?? 'N/A' }}</td>
                        <td>{{ $entry->toilet_cleaning_date?->format('d M Y') ?? 'N/A' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($entry->remarks ?: '—', 60) }}</td>
                        <td class="text-right">
                            <a href="{{ route('facility-management.edit', $entry) }}" class="crm-icon-btn" title="Edit">✏️</a>
                            <form action="{{ route('facility-management.destroy', $entry) }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('Delete this facility entry?')">
                                @csrf
                                @method('DELETE')
                                <button class="crm-icon-btn danger" title="Delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="crm-empty">No facility management records found.</td></tr>
                @endforelse
                </tbody>
            </table>

            @if($facilityEntries->hasPages())
                @include('partials.table-pagination', ['paginator' => $facilityEntries])
            @endif
        </div>
    </div>
</main>
@endsection
