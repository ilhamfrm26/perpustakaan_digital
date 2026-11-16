@if ($paginator->hasPages())
<div class="pager">
    @if ($paginator->onFirstPage()) <span>‹ Sebelumnya</span> @else <a href="{{ $paginator->previousPageUrl() }}">‹ Sebelumnya</a> @endif
    <span>Halaman {{ $paginator->currentPage() }}</span>
    @if ($paginator->hasMorePages()) <a href="{{ $paginator->nextPageUrl() }}">Berikutnya ›</a> @else <span>Berikutnya ›</span> @endif
</div>
@endif
