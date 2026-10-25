@extends('layout.app')

@section('title', $game->title.' — '.__('Games'))
@section('description', \Illuminate\Support\Str::limit($game->description ?: $game->title.' — '.__('folder game Inter G Queen Bumindo.'), 155))

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="fx" role="region" aria-label="Folder {{ $game->slug }}">
      @include('partials.fx-bar', ['segments' => [['games', lroute('games')], [$game->slug, null]], 'count' => __(':n file', ['n' => $files->count()])])

      @if ($game->description)
        <p class="fx-info"><strong>{{ $game->title }}</strong> — {{ $game->description }}</p>
      @endif

      <div class="fx-head" aria-hidden="true"><span>{{ __('Nama') }}</span><span>{{ __('Ukuran') }}</span><span>{{ __('Diubah') }}</span><span></span></div>

      <ul class="fx-list">
        <li>
          <a href="{{ lroute('games') }}" class="fx-row fx-row--up">
            <span class="fx-name">
              <svg class="fx-up" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 9.8V20h2V9.8l4.6 4.6L19 13l-7-7-7 7 1.4 1.4z"/></svg>
              <span class="fx-text"><span class="fx-title">..</span><span class="fx-desc">{{ __('Kembali ke folder games') }}</span></span>
            </span>
            <span></span><span></span><span></span>
          </a>
        </li>
        @forelse ($files as $file)
          <li>
            <a href="{{ lroute('games.file', [$game, $file->name]) }}" class="fx-row" target="_blank" rel="noopener" title="{{ __('Buka di tab baru') }}: {{ $file->name }}.html">
              <span class="fx-name">
                <svg class="fx-file" viewBox="0 0 40 48" aria-hidden="true">
                  <path class="fx-file-body" d="M4 0h22l14 14v30a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z"/>
                  <path class="fx-file-fold" d="M26 0l14 14H30a4 4 0 0 1-4-4z"/>
                  <text x="20" y="38" text-anchor="middle">HTML</text>
                </svg>
                <span class="fx-text">
                  <span class="fx-title">{{ $file->name }}.html</span>
                  <span class="fx-desc">{{ preg_replace('#^https?://#', '', lroute('games.file', [$game, $file->name])) }}</span>
                </span>
              </span>
              <span class="fx-size">{{ \App\Models\GameFile::formatBytes((int) $file->bytes) }}</span>
              <span class="fx-date">{{ $file->updated_at?->locale(\App\Support\Locale::carbon())->translatedFormat('d M Y') }}</span>
              <span class="fx-open" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 3v2h3.6l-9.8 9.8 1.4 1.4L19 6.4V10h2V3h-7zm5 16H5V5h7V3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7h-2v7z"/></svg></span>
            </a>
          </li>
        @empty
          <li><p class="fx-empty">{{ __('Folder ini masih kosong.') }}</p></li>
        @endforelse
      </ul>
    </div>
    <p class="fx-note">{{ __('Klik file untuk membukanya di tab baru.') }}</p>
  </div>
</section>

@endsection
