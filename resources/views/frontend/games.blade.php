@extends('layout.app')

@section('title', 'InterG Studio')
@section('description', __('Game dan aplikasi mobile karya InterG Studio — tersedia di Google Play dan App Store.'))

@section('content')

<section class="section section--page studio">
  <div class="container">
    <div class="studio-head">
      <img src="{{ asset('images/interg-studio.png') }}" alt="" width="88" height="88" class="studio-logo">
      <div>
        <h1 class="section-title">InterG Studio</h1>
        <p class="section-sub">{{ __('Game dan aplikasi mobile buatan tim kami. Pilih aplikasi untuk melihat screenshot, deskripsi, dan link download.') }}</p>
      </div>
      <a href="https://play.google.com/store/apps/developer?id=InterG+Studio" class="btn btn-dark studio-dev" target="_blank" rel="noopener">
        @include('partials.play-icon') {{ __('Lihat di Google Play') }}
      </a>
    </div>

    @if ($games->isNotEmpty())
      {{-- Compact grid: icon and name only; everything else is on the app page. --}}
      <ul class="app-grid">
        @foreach ($games as $game)
          @php($app = $game->listing())
          <li>
            <a href="{{ lroute('games.show', $game) }}" class="app-tile">
              <span class="app-tile-icon">
                @if ($app['icon'])
                  <img src="{{ $app['icon'] }}" alt="" loading="lazy" width="80" height="80">
                @else
                  <span aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($app['name'], 0, 1)) }}</span>
                @endif
              </span>
              <span class="app-tile-name">{{ $app['name'] }}</span>
              <span class="app-tile-meta">
                @if ($app['category']){{ __($app['category']) }}@endif
                @if ($app['rating'])<span class="app-tile-rating">★ {{ number_format($app['rating'], 1) }}</span>@endif
              </span>
              <span class="app-tile-stores">
                @if ($game->play_package)<span title="Google Play">@include('partials.play-icon')<span class="sr-only">Google Play</span></span>@endif
                @if ($game->app_store_id)<span title="App Store">@include('partials.apple-icon')<span class="sr-only">App Store</span></span>@endif
              </span>
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <p class="empty-state">{{ __('Belum ada aplikasi yang ditampilkan.') }}</p>
    @endif
  </div>
</section>

@endsection
