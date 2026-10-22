{{-- Address bar of the folder (file manager / FTP-like) view. Expects: $segments (list of [label, url|null]), $count (string) --}}
<div class="fx-bar">
  <span class="fx-dots" aria-hidden="true"><i></i><i></i><i></i></span>
  <nav class="fx-path" aria-label="Lokasi folder">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z"/></svg>
    <a href="{{ url('/') }}">{{ preg_replace('#^https?://#', '', url('/')) }}</a><b>/</b>
    @foreach ($segments as [$label, $href])
      @if ($href)
        <a href="{{ $href }}">{{ $label }}</a><b>/</b>
      @else
        <span class="fx-here">{{ $label }}</span><b>/</b>
      @endif
    @endforeach
  </nav>
  <span class="fx-count">{{ $count }}</span>
</div>
