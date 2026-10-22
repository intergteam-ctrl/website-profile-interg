@extends('layout.app')

@section('title', 'Games')
@section('description', 'Kumpulan game ringan berbasis browser dari Inter G Queen Bumindo — buka folder lalu klik file untuk main.')

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="fx" role="region" aria-label="Folder Games">
      @include('partials.fx-bar', ['segments' => [['games', null]], 'count' => $games->count().' folder'])

      <div class="fx-head" aria-hidden="true"><span>Nama</span><span>Ukuran</span><span>Diubah</span><span></span></div>

      @if ($games->isNotEmpty())
        <ul class="fx-list">
          @foreach ($games as $game)
            @php($stat = $sizes[$game->id] ?? null)
            <li>
              <a href="{{ route('games.show', $game) }}" class="fx-row">
                <span class="fx-name">
                  @include('partials.fx-folder-icon')
                  <span class="fx-text">
                    <span class="fx-title">{{ $game->slug }}</span>
                    <span class="fx-desc">{{ $game->title }}@if ($game->description) — {{ \Illuminate\Support\Str::limit($game->description, 100) }}@endif</span>
                  </span>
                </span>
                <span class="fx-size">{{ $game->files_count }} file</span>
                <span class="fx-date">{{ ($stat?->changed ? \Illuminate\Support\Carbon::parse($stat->changed) : $game->updated_at)?->locale('id')->translatedFormat('d M Y') }}</span>
                <span class="fx-open" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 6 8.6 7.4 13.2 12l-4.6 4.6L10 18l6-6z"/></svg></span>
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <p class="fx-empty">Folder ini masih kosong.</p>
      @endif
    </div>
    <p class="fx-note">Klik folder untuk melihat isinya.</p>
  </div>
</section>

@endsection
