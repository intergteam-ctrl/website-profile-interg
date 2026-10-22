@extends('layout.app')

@section('title', 'Games')
@section('description', 'Kumpulan game ringan berbasis browser dari Inter G Queen Bumindo — langsung main tanpa instal.')

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Games</div>
      <h1 class="section-title">Main <em>Sejenak</em></h1>
      <p class="section-sub">Game ringan berbasis browser — langsung main tanpa instal, di komputer maupun HP.</p>
    </div>

    @if ($games->isNotEmpty())
      <ul class="game-grid">
        @foreach ($games as $game)
          <li class="reveal">
            <a href="{{ route('games.show', $game) }}" class="game-card">
              <span class="game-cover">
                @if ($game->image_url)
                  <img src="{{ $game->image_url }}" alt="" loading="lazy" decoding="async">
                @else
                  <span class="game-cover-ph" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($game->title, 0, 1)) }}</span>
                @endif
                <span class="game-play" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
              </span>
              <span class="game-info">
                <span class="game-title">{{ $game->title }}</span>
                @if ($game->description)
                  <span class="game-desc">{{ \Illuminate\Support\Str::limit($game->description, 110) }}</span>
                @endif
              </span>
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <p class="empty-state">Belum ada game yang tersedia.</p>
    @endif
  </div>
</section>

@endsection
