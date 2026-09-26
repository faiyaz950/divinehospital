@if ($paginator->hasPages())
    <nav class="a-pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="a-btn a-btn--ghost is-disabled">← Newer</span>
        @else
            <a class="a-btn a-btn--ghost" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Newer</a>
        @endif
        <span class="a-muted a-small">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a class="a-btn a-btn--ghost" href="{{ $paginator->nextPageUrl() }}" rel="next">Older →</a>
        @else
            <span class="a-btn a-btn--ghost is-disabled">Older →</span>
        @endif
    </nav>
@endif
