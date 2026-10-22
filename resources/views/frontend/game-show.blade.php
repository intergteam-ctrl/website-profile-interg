@extends('layout.app')

@section('title', $game->title.' — Games')
@section('description', \Illuminate\Support\Str::limit($game->description ?: 'Main '.$game->title.' langsung di browser.', 155))

@section('content')

<section class="section section--page">
  <div class="container">
    <nav class="game-crumbs" aria-label="Breadcrumb"><a href="{{ route('games') }}">Games</a> <span aria-hidden="true">/</span> <span>{{ $game->title }}</span></nav>

    <div class="game-head">
      <h1 class="section-title">{{ $game->title }}</h1>
      <div class="game-actions">
        <button type="button" class="btn btn-red" id="gameFullscreen">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
          Layar penuh
        </button>
        <button type="button" class="btn btn-ghost" id="gameRestart">Mulai ulang</button>
      </div>
    </div>

    <div class="game-stage" id="gameStage">
      {{-- Sandboxed: no allow-same-origin, so the game cannot reach this site's cookies or admin session. --}}
      <iframe id="gameFrame" src="{{ route('games.play', $game) }}" title="{{ $game->title }}"
              sandbox="allow-scripts allow-pointer-lock allow-popups allow-modals"
              allow="fullscreen; gamepad; autoplay" loading="eager" referrerpolicy="no-referrer"></iframe>
    </div>

    @if ($game->description)
      <p class="game-about">{{ $game->description }}</p>
    @endif

    @if ($others->isNotEmpty())
      <h2 class="game-more-title">Game lainnya</h2>
      <ul class="game-grid game-grid--small">
        @foreach ($others as $other)
          <li>
            <a href="{{ route('games.show', $other) }}" class="game-card">
              <span class="game-cover">
                @if ($other->image_url)
                  <img src="{{ $other->image_url }}" alt="" loading="lazy" decoding="async">
                @else
                  <span class="game-cover-ph" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($other->title, 0, 1)) }}</span>
                @endif
              </span>
              <span class="game-info"><span class="game-title">{{ $other->title }}</span></span>
            </a>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
  var stage = document.getElementById('gameStage');
  var frame = document.getElementById('gameFrame');
  document.getElementById('gameFullscreen').addEventListener('click', function () {
    var el = stage;
    (el.requestFullscreen || el.webkitRequestFullscreen || function () {}).call(el);
    frame.focus();
  });
  document.getElementById('gameRestart').addEventListener('click', function () {
    frame.src = frame.src;
    frame.focus();
  });
})();
</script>
@endpush
