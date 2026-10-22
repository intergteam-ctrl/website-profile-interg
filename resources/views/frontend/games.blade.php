@extends('layout.app')

@section('title', 'Games')
@section('description', 'Kumpulan game ringan berbasis browser dari Inter G Queen Bumindo — klik folder untuk langsung main.')

@php
  $fmtSize = function (?int $bytes): string {
      $bytes = (int) $bytes;
      return $bytes >= 1048576 ? number_format($bytes / 1048576, 1, ',', '.').' MB' : max(1, (int) round($bytes / 1024)).' KB';
  };
@endphp

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="fx" role="region" aria-label="Folder Games">
      {{-- Address bar, like a file manager / FTP client --}}
      <div class="fx-bar">
        <span class="fx-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <div class="fx-path">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z"/></svg>
          <span>{{ preg_replace('#^https?://#', '', url('/')) }}</span><b>/</b><span class="fx-here">games</span><b>/</b>
        </div>
        <span class="fx-count">{{ $games->count() }} folder</span>
      </div>

      <div class="fx-head" aria-hidden="true">
        <span>Nama</span><span>Ukuran</span><span>Diubah</span>
      </div>

      @if ($games->isNotEmpty())
        <ul class="fx-list">
          @foreach ($games as $game)
            <li>
              <a href="{{ route('games.play', $game) }}" class="fx-row" target="_blank" rel="noopener"
                 title="Buka {{ $game->title }} di tab baru">
                <span class="fx-name">
                  <svg class="fx-folder" viewBox="0 0 48 40" aria-hidden="true">
                    <path class="fx-folder-back" d="M4 4h14l4 5h22a4 4 0 0 1 4 4v23a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4z"/>
                    <path class="fx-folder-front" d="M0 15a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v21a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"/>
                  </svg>
                  <span class="fx-text">
                    <span class="fx-title">{{ $game->title }}</span>
                    @if ($game->description)
                      <span class="fx-desc">{{ \Illuminate\Support\Str::limit($game->description, 120) }}</span>
                    @endif
                  </span>
                </span>
                <span class="fx-size">{{ $fmtSize($game->html_bytes) }}</span>
                <span class="fx-date">{{ $game->updated_at?->locale('id')->translatedFormat('d M Y') }}</span>
                <span class="fx-open" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 3v2h3.6l-9.8 9.8 1.4 1.4L19 6.4V10h2V3h-7zm5 16H5V5h7V3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7h-2v7z"/></svg></span>
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <p class="fx-empty">Folder ini masih kosong.</p>
      @endif
    </div>
    <p class="fx-note">Klik folder untuk membuka game di tab baru.</p>
  </div>
</section>

@endsection
