@extends('layouts.app')

@section('title', 'Intern Joining Forms')

@push('styles')
    @include('pages.hrms.employee_onboarding.styles')
    @include('pages.hrms.Interns.intern_joining_forms.styles')
@endpush

@section('content')
<div class="intern-page">
    <div class="intern-shell">
        <div class="eob-topbar">
            <div>
                <div class="eob-title">Intern Joining Forms</div>
                <div class="eob-breadcrumb">HRMS > Intern Joining Forms</div>
            </div>
            <div class="eob-actions">
                <a href="{{ route('hrms.dashboard') }}" class="eob-btn eob-btn-ghost">Back</a>
                <a href="{{ route('interns.create') }}" class="eob-btn eob-btn-primary">Add Intern Form</a>
            </div>
        </div>
        <div class="intern-body">
            @if(session('success'))
                <div class="intern-alert intern-alert-success">{!! session('success') !!}</div>
            @endif



            <div class="intern-card intern-list-card">
                <div class="intern-card-head">
                    <div>
                        <div class="intern-card-title">Intern Records</div>
                        <div class="intern-card-subtitle">Review submitted intern details, contact information, and declaration dates in one place.</div>
                    </div>
                    <div class="intern-results-chip">{{ $forms->total() }} total</div>
                </div>

                <div class="intern-filter-wrap">
                    <form method="GET" action="{{ route('interns.index') }}" class="intern-filter-form">
                        <div class="intern-filter-field">
                            <label class="intern-filter-label">Search Intern</label>
                            <input type="text" name="search" class="intern-input" value="{{ request('search') }}" placeholder="Search by name, email, mobile, aadhaar">
                        </div>
                        <div class="intern-filter-actions">
                            <button type="submit" class="btn btn-primary">Search</button>
                            @if(request()->filled('search'))
                                <a href="{{ route('interns.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="table-responsive intern-list-table-wrap">
                    <table class="table table-hover align-middle intern-list-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Intern</th>
                                <th>Contact</th>
                                <th>Date of Birth</th>
                                <th>Declaration Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($forms as $item)
                            <tr>
                                <td>
                                    <div class="intern-row-index">{{ ($forms->firstItem() ?? 1) + $loop->index }}</div>
                                </td>
                                <td>
                                    <div class="intern-person-cell">
                                        <div class="intern-person-avatar">{{ strtoupper(substr($item->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="intern-person-name">{{ $item->name }}</div>
                                            <div class="intern-person-sub">{{ $item->father_name ?: 'Father name not added' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="intern-contact-main">{{ $item->mobile ?: 'N/A' }}</div>
                                    <div class="intern-contact-sub">{{ $item->email ?: 'Email not added' }}</div>
                                </td>
                                <td>
                                    <div class="intern-date-main">{{ optional($item->date_of_birth)->format('d M Y') ?: 'N/A' }}</div>
                                    <div class="intern-date-sub">{{ optional($item->date_of_birth)->age ? optional($item->date_of_birth)->age . ' yrs' : 'Age unavailable' }}</div>
                                </td>
                                <td>
                                    <div class="intern-date-main">{{ optional($item->declaration_date)->format('d M Y') ?: 'N/A' }}</div>
                                    <div class="intern-date-sub">{{ $item->declaration_place ?: 'Place not added' }}</div>
                                </td>
                                <td class="text-end">
                                    <div class="intern-action-group">
                                        <a href="{{ route('interns.show', $item) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="{{ route('interns.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="{{ route('interns.destroy', $item) }}" onsubmit="return confirm('Delete this intern form?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-5">
                                    <div class="intern-empty-state">
                                        <strong>No intern forms found</strong>
                                        <span>Try a different search or create a new intern joining form.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($forms->hasPages())
                    @include('partials.table-pagination', ['paginator' => $forms])
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
