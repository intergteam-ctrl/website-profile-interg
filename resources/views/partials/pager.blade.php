@if ($paginator->hasPages())
  <nav class="pager" aria-label="{{ __('Navigasi halaman') }}">
    @if ($paginator->onFirstPage())
      <span class="btn btn-ghost" aria-disabled="true">← {{ __('Sebelumnya') }}</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-ghost" rel="prev">← {{ __('Sebelumnya') }}</a>
    @endif

    <span class="pager-info">{{ __('Halaman :page dari :last', ['page' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</span>

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-ghost" rel="next">{{ __('Berikutnya') }} →</a>
    @else
      <span class="btn btn-ghost" aria-disabled="true">{{ __('Berikutnya') }} →</span>
    @endif
  </nav>
@endif
