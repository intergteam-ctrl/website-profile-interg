@if ($paginator->hasPages())
  <nav class="pager" aria-label="Navigasi halaman">
    @if ($paginator->onFirstPage())
      <span class="btn btn-navy" aria-disabled="true">← Sebelumnya</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-navy" rel="prev">← Sebelumnya</a>
    @endif

    <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-navy" rel="next">Berikutnya →</a>
    @else
      <span class="btn btn-navy" aria-disabled="true">Berikutnya →</span>
    @endif
  </nav>
@endif
