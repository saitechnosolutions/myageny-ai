@extends('layouts.app')
@section('title', 'Facebook Campaign Integration - Settings')

@push('styles')
@include('pages.settings.partials.table-styles')
<style>
.crm-filter-card {
    background: #fff;
    border: 1px solid #e1dee3;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 18px;
}

.crm-filter-form {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    align-items: end;
}

.crm-select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e1dee3;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    font-family: inherit;
    background: #fff;
}

.crm-select:focus {
    border-color: #fe5f04;
    box-shadow: 0 0 0 3px rgba(254, 95, 4, 0.1);
}

.crm-filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.crm-user-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.crm-user-empty {
    color: #9e9e9e;
    font-size: 13px;
}

@media (max-width: 1100px) {
    .crm-filter-form {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 700px) {
    .crm-page-header {
        flex-direction: column;
        gap: 14px;
    }

    .crm-filter-form {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<main class="main-content">
    <div class="crm-page-body">
        <div class="crm-page-header">
            <div>
                <h2 class="crm-title">Facebook Campaign Integration</h2>
                <p class="crm-subtitle">Track where your leads originate from</p>
            </div>
            <div class="crm-header-actions">
                <a href="{{ route('settings.index') }}" class="crm-btn crm-btn-ghost">Back</a>
                <a href="/settings/auth/facebook" target="_blank" class="crm-btn crm-btn-primary">+ Login Facebook</a>
            </div>
        </div>

        @include('pages.settings.partials.alert')

        <div class="crm-filter-card">
            <form method="GET" action="{{ route('settings.facebook-integration') }}" class="crm-filter-form">
                <div>
                    <label class="crm-label" for="campaign_name">Campaign Name</label>
                    <input
                        id="campaign_name"
                        type="text"
                        name="campaign_name"
                        class="crm-input"
                        value="{{ request('campaign_name') }}"
                        placeholder="Search campaign name">
                </div>

                <div>
                    <label class="crm-label" for="campaign_id">Campaign ID</label>
                    <input
                        id="campaign_id"
                        type="text"
                        name="campaign_id"
                        class="crm-input"
                        value="{{ request('campaign_id') }}"
                        placeholder="Search campaign ID">
                </div>

                <div>
                    <label class="crm-label" for="assigned_user">Assigned User</label>
                    <select id="assigned_user" name="assigned_user" class="crm-select">
                        <option value="">All Assigned Users</option>
                        @foreach ($activeUsers as $user)
                            <option value="{{ $user->id }}" @selected((string) request('assigned_user') === (string) $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="crm-filter-actions">
                    <button type="submit" class="crm-btn crm-btn-primary">Apply Filter</button>
                    @if (request()->filled('campaign_name') || request()->filled('campaign_id') || request()->filled('assigned_user'))
                        <a href="{{ route('settings.facebook-integration') }}" class="crm-btn crm-btn-ghost">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead style="text-transform: uppercase">
                    <tr>
                        <th>#</th>
                        <th>Campaign Name</th>
                        <th>Campaign ID</th>
                        <th>Assigned Users</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaignMasters as $i => $campaignMaster)
                        <tr>
                            <td>{{ $campaignMasters->firstItem() + $i }}</td>
                            <td>
                                <span class="crm-badge crm-badge-blue">{{ $campaignMaster->campaign_name }}</span>
                            </td>
                            <td>{{ $campaignMaster->camp_id ?: $campaignMaster->ad_id ?: '-' }}</td>
                            <td>
                                @if ($campaignMaster->assignedUsers->isNotEmpty())
                                    <div class="crm-user-list">
                                        @foreach ($campaignMaster->assignedUsers as $assignedUser)
                                            <span class="crm-badge crm-badge-purple">{{ $assignedUser->user_name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="crm-user-empty">No users assigned</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button class="crm-icon-btn" onclick="openEdit({{ $campaignMaster->id }}, '{{ addslashes($campaignMaster->campaign_name) }}')">Edit</button>
                                <form action="{{ route('settings.lead-sources.destroy', $campaignMaster) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('Delete this source?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="crm-icon-btn danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="crm-empty">No Facebook campaigns found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($campaignMasters->hasPages())
                @include('partials.table-pagination', ['paginator' => $campaignMasters])
            @endif
            @if (false && $campaignMasters->hasPages())
                <div class="crm-pagination">
                    <div class="crm-page-info">
                        Page {{ $campaignMasters->currentPage() }} of {{ $campaignMasters->lastPage() }}
                    </div>
                    <div class="crm-page-links">
                        @if ($campaignMasters->onFirstPage())
                            <span class="crm-page-link disabled">Prev</span>
                        @else
                            <a href="{{ $campaignMasters->previousPageUrl() }}" class="crm-page-link">Prev</a>
                        @endif

                        @foreach ($campaignMasters->getUrlRange(max(1, $campaignMasters->currentPage() - 2), min($campaignMasters->lastPage(), $campaignMasters->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="crm-page-link {{ $page == $campaignMasters->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if ($campaignMasters->hasMorePages())
                            <a href="{{ $campaignMasters->nextPageUrl() }}" class="crm-page-link">Next</a>
                        @else
                            <span class="crm-page-link disabled">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="addModal" class="crm-modal-overlay" style="display:none">
        <div class="crm-modal">
            <div class="crm-modal-header">
                <h3>Add Lead Source</h3>
                <button onclick="closeModal('addModal')">x</button>
            </div>
            <form action="{{ route('settings.lead-sources.store') }}" method="POST">
                @csrf
                <div class="crm-modal-body">
                    <label class="crm-label">Source Name <span class="req">*</span></label>
                    <input type="text" name="name" class="crm-input" placeholder="e.g. Website, Referral, Cold Call..." required>
                </div>
                <div class="crm-modal-footer">
                    <button type="button" class="crm-btn crm-btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="crm-btn crm-btn-primary">Save Source</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="crm-modal-overlay" style="display:none">
        <div class="crm-modal">
            <div class="crm-modal-header">
                <h3>Edit Lead Source</h3>
                <button onclick="closeModal('editModal')">x</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="crm-modal-body">
                    <label class="crm-label">Source Name <span class="req">*</span></label>
                    <input type="text" id="editName" name="name" class="crm-input" required>
                </div>
                <div class="crm-modal-footer">
                    <button type="button" class="crm-btn crm-btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="crm-btn crm-btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</main>

@push('scripts')
@include('pages.settings.partials.modal-scripts')
<script>
function openEdit(id, name) {
    document.getElementById('editName').value = name;
    document.getElementById('editForm').action = `/settings/lead-sources/${id}`;
    openModal('editModal');
}
</script>
@endpush
@endsection
