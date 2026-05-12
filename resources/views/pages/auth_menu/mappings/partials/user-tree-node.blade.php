@php
    $children = collect($node['children'] ?? []);
    $hasChildren = $children->isNotEmpty();
@endphp

<li class="umap-tree-item {{ ($isRoot ?? false) ? 'is-open' : '' }}">
    <button type="button"
            class="umap-tree-node"
            data-tree-toggle
            data-has-children="{{ $hasChildren ? '1' : '0' }}"
            {{ $hasChildren ? '' : 'disabled' }}>
        <span class="umap-tree-main">
            <span class="umap-tree-toggle">{{ ($isRoot ?? false) && $hasChildren ? '-' : ($hasChildren ? '+' : '') }}</span>
            <span class="umap-tree-copy">
                <span class="umap-tree-name">{{ $node['name'] }}</span>
                <span class="umap-tree-meta">{{ $node['email'] }} · {{ $children->count() }} direct report{{ $children->count() === 1 ? '' : 's' }}</span>
            </span>
        </span>
        <span class="umap-tree-role">{{ $node['role'] }}</span>
    </button>

    @if($hasChildren)
        <ul class="umap-tree-children">
            @foreach($children as $child)
                @include('pages.auth_menu.mappings.partials.user-tree-node', ['node' => $child, 'isRoot' => false])
            @endforeach
        </ul>
    @endif
</li>
