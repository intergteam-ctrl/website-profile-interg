@extends('layout.app')

@section('title', __('Games'))
@section('description', __('Kumpulan game ringan berbasis browser dari Inter G Queen Bumindo — buka folder lalu klik file untuk main.'))

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="section-head section-head--center">
      <h1 class="section-title">{{ __('Games') }}</h1>
      <p class="section-sub">{{ __('Game ringan berbasis browser dari tim kami. Buka folder, lalu klik file untuk bermain langsung tanpa instal.') }}</p>
    </div>

    <div class="fx" role="region" aria-label="{{ __('Folder Games') }}">
      @include('partials.fx-bar', ['segments' => [['games', null]], 'count' => __(':n folder', ['n' => $games->count()])])

      <div class="fx-head" aria-hidden="true"><span>{{ __('Nama') }}</span><span>{{ __('Ukuran') }}</span><span>{{ __('Diubah') }}</span><span></span></div>

      @if ($games->isNotEmpty())
        <ul class="fx-list">
          @foreach ($games as $game)
            @php($stat = $sizes[$game->id] ?? null)
            <li>
              <a href="{{ lroute('games.show', $game) }}" class="fx-row">
                <span class="fx-name">
                  @include('partials.fx-folder-icon')
                  <span class="fx-text">
                    <span class="fx-title">{{ $game->slug }}</span>
                    <span class="fx-desc">{{ $game->title }}@if ($game->description) — {{ \Illuminate\Support\Str::limit($game->description, 100) }}@endif</span>
                  </span>
                </span>
                <span class="fx-size">{{ __(':n file', ['n' => $game->files_count]) }}</span>
                <span class="fx-date">{{ ($stat?->changed ? \Illuminate\Support\Carbon::parse($stat->changed) : $game->updated_at)?->locale(\App\Support\Locale::carbon())->translatedFormat('d M Y') }}</span>
                <span class="fx-open" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 6 8.6 7.4 13.2 12l-4.6 4.6L10 18l6-6z"/></svg></span>
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <p class="fx-empty">{{ __('Folder ini masih kosong.') }}</p>
      @endif
    </div>
    <p class="fx-note">{{ __('Klik folder untuk melihat isinya.') }}</p>
  </div>
</section>

@endsection
