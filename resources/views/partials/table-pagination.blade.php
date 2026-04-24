@php
    $startPage = max(1, $paginator->currentPage() - 2);
    $endPage = min($paginator->lastPage(), $paginator->currentPage() + 2);
@endphp

<div class="app-pagination">
    <div class="app-pagination__info">
        Showing
        <strong>{{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}</strong>
        of
        <strong>{{ $paginator->total() }}</strong>
    </div>

    <div class="app-pagination__links">
        @if($paginator->onFirstPage())
            <span class="app-pagination__link is-disabled">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="app-pagination__link">Prev</a>
        @endif

        @if($startPage > 1)
            <a href="{{ $paginator->url(1) }}" class="app-pagination__link">1</a>
            @if($startPage > 2)
                <span class="app-pagination__ellipsis">...</span>
            @endif
        @endif

        @foreach(range($startPage, $endPage) as $page)
            <a href="{{ $paginator->url($page) }}" class="app-pagination__link {{ $page === $paginator->currentPage() ? 'is-active' : '' }}">{{ $page }}</a>
        @endforeach

        @if($endPage < $paginator->lastPage())
            @if($endPage < $paginator->lastPage() - 1)
                <span class="app-pagination__ellipsis">...</span>
            @endif
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="app-pagination__link">{{ $paginator->lastPage() }}</a>
        @endif

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="app-pagination__link">Next</a>
        @else
            <span class="app-pagination__link is-disabled">Next</span>
        @endif
    </div>
</div>
