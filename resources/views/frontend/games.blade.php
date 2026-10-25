@extends('layout.app')

@section('title', 'InterG Studio')
@section('description', __('Game dan aplikasi mobile karya InterG Studio — tersedia di Google Play.'))

@section('content')

<section class="section section--page studio">
  <div class="container">
    <div class="studio-head">
      <img src="{{ asset('images/interg-studio.png') }}" alt="" width="88" height="88" class="studio-logo">
      <div>
        <h1 class="section-title">InterG Studio</h1>
        <p class="section-sub">{{ __('Game dan aplikasi mobile buatan tim kami — dari puzzle santai hingga aplikasi gizi. Semua tersedia di Google Play.') }}</p>
      </div>
      <a href="https://play.google.com/store/apps/developer?id=InterG+Studio" class="btn btn-dark studio-dev" target="_blank" rel="noopener">
        @include('partials.play-icon') {{ __('Lihat di Google Play') }}
      </a>
    </div>

    @if ($games->isNotEmpty())
      <ul class="app-grid">
        @foreach ($games as $game)
          @php($play = $game->play_package ? $game->play() : null)
          @php($name = $play['name'] ?? $game->title)
          @php($icon = $play['icon'] ?? $game->image_url)
          @php($summary = \Illuminate\Support\Str::limit(strtok((string) ($play['description'] ?? $game->description ?? ''), "\n") ?: '', 150))
          <li>
            <article class="app-tile">
              <a href="{{ lroute('games.show', $game) }}" class="app-tile-link">
                <span class="app-tile-icon">
                  @if ($icon)
                    <img src="{{ $icon }}" alt="" loading="lazy" width="72" height="72">
                  @else
                    <span aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($name, 0, 1)) }}</span>
                  @endif
                </span>
                <span class="app-tile-body">
                  <span class="app-tile-name">{{ $name }}</span>
                  <span class="app-tile-meta">
                    @if (! empty($play['category'])){{ __($play['category']) }}@endif
                    @if (! empty($play['rating']))<span class="app-tile-rating" aria-label="{{ __('Rating') }} {{ $play['rating'] }}">★ {{ number_format($play['rating'], 1) }}</span>@endif
                  </span>
                  @if ($summary)<span class="app-tile-desc">{{ $summary }}</span>@endif
                </span>
              </a>
              <div class="app-tile-actions">
                <a href="{{ lroute('games.show', $game) }}" class="link-arrow">{{ __('Lihat detail') }}</a>
                @if ($game->play_package)
                  <a href="{{ $game->play_url }}" class="app-tile-play" target="_blank" rel="noopener">@include('partials.play-icon') Google Play</a>
                @endif
              </div>
            </article>
          </li>
        @endforeach
      </ul>
    @else
      <p class="empty-state">{{ __('Belum ada aplikasi yang ditampilkan.') }}</p>
    @endif
  </div>
</section>

@endsection
