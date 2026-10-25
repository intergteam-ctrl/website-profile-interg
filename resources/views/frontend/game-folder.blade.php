@extends('layout.app')

@php
  $play = $game->listing();
  $name = $play['name'];
  $icon = $play['icon'];
  $about = trim($play['description']);
@endphp

@section('title', $name.' — InterG Studio')
@section('description', \Illuminate\Support\Str::limit(strtok($about, "\n") ?: $name.' — InterG Studio', 155))
@if ($icon)
  @section('og_image', $icon)
@endif

@section('content')

<section class="section section--page studio">
  <div class="container">
    <nav class="studio-crumbs" aria-label="Breadcrumb"><a href="{{ lroute('games') }}">InterG Studio</a> <span aria-hidden="true">/</span> <span>{{ $name }}</span></nav>

    <header class="app-hero">
      <span class="app-hero-icon">
        @if ($icon)
          <img src="{{ $icon }}" alt="" width="112" height="112">
        @else
          <span aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($name, 0, 1)) }}</span>
        @endif
      </span>
      <div class="app-hero-text">
        <h1 class="section-title">{{ $name }}</h1>
        <p class="app-hero-meta">
          InterG Studio
          @if (! empty($play['category']))<span>{{ __($play['category']) }}</span>@endif
          @if (! empty($play['rating']))<span>★ {{ number_format($play['rating'], 1) }}@if (! empty($play['ratings'])) ({{ number_format($play['ratings'], 0, ',', '.') }})@endif</span>@endif
          @if (! empty($play['content_rating']))<span>{{ $play['content_rating'] }}</span>@endif
        </p>
        <div class="app-hero-actions">
          @if ($game->play_package)
            <a href="{{ $game->play_url }}" class="btn btn-red app-hero-play" target="_blank" rel="noopener">@include('partials.play-icon') {{ __('Download di Google Play') }}</a>
          @endif
          @if ($game->app_store_id)
            <a href="{{ $game->app_store_url }}" class="btn btn-dark app-hero-play" target="_blank" rel="noopener">@include('partials.apple-icon') {{ __('Download di App Store') }}</a>
          @endif
        </div>
      </div>
    </header>

    @if (! empty($play['screenshots']))
      <section class="app-shots" aria-label="{{ __('Screenshot') }}">
        <ul class="app-shots-strip">
          @foreach ($play['screenshots'] as $i => $shot)
            <li><a href="{{ $shot }}" target="_blank" rel="noopener"><img src="{{ $shot }}" alt="{{ __('Screenshot') }} {{ $i + 1 }} — {{ $name }}" loading="lazy"></a></li>
          @endforeach
        </ul>
      </section>
    @endif

    <div class="app-detail-grid">
      <div>
        <h2 class="app-h2">{{ __('Tentang aplikasi ini') }}</h2>
        @if ($about !== '')
          @php([$lead, $rest] = array_pad(preg_split('/\R\s*\R/', $about, 2), 2, ''))
          <div class="app-about">{{ $lead }}</div>
          @if (trim($rest) !== '')
            <details class="app-more">
              <summary>{{ __('Baca selengkapnya') }}</summary>
              <div class="app-about">{{ trim($rest) }}</div>
            </details>
          @endif
        @else
          <p class="empty-state">{{ __('Deskripsi belum tersedia.') }}</p>
        @endif
      </div>

      @if ($files->isNotEmpty())
        <aside>
          <h2 class="app-h2">{{ __('Halaman & file') }}</h2>
          <div class="fx" role="region" aria-label="{{ __('Halaman & file') }}">
            @include('partials.fx-bar', ['segments' => [['studio', lroute('games')], [$game->slug, null]], 'count' => __(':n file', ['n' => $files->count()])])
            <ul class="fx-list">
              @foreach ($files as $file)
                <li>
                  <a href="{{ lroute('games.file', [$game, $file->name]) }}" class="fx-row fx-row--compact" target="_blank" rel="noopener" title="{{ __('Buka di tab baru') }}: {{ $file->name }}.html">
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
                    <span class="fx-open" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 3v2h3.6l-9.8 9.8 1.4 1.4L19 6.4V10h2V3h-7zm5 16H5V5h7V3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7h-2v7z"/></svg></span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </aside>
      @endif
    </div>
  </div>
</section>

@endsection
