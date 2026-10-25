{{--
  Instagram side panel. Closed by default: only a slim tab on the right edge
  is visible until the visitor opens it. Posts are curated in Admin → Instagram.
  Expects: $instagramPosts (Collection<InstagramPost>).
--}}
@php
  // Links come from Admin → Media Sosial; an empty link hides its tab.
  $socialLink = fn (string $key): array => ['url' => \App\Support\Socials::url($key), 'handle' => ltrim((string) \App\Support\Socials::handle($key), '@')];
  $ig = $socialLink('instagram');
  $yt = $socialLink('youtube');
  $tt = $socialLink('tiktok');
  $ttIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.1v12.4a2.6 2.6 0 1 1-2.6-2.6c.3 0 .5 0 .8.1V9.7a5.7 5.7 0 1 0 4.9 5.7V9a7.4 7.4 0 0 0 4.3 1.4V7.3a4.3 4.3 0 0 1-3.2-1.5z"/></svg>';
  $ytIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12a31 31 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8zM9.7 15.1V8.9l5.8 3.1-5.8 3.1z"/></svg>';
  $igIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zM12 0C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.2 4.4 2.6 6.8 7 7 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.8a1.4 1.4 0 1 0 0 2.9 1.4 1.4 0 0 0 0-2.9z"/></svg>';
@endphp

@if ($ig['url'] || $yt['url'] || $tt['url'])
<div class="side-tabs">
  @if ($ig['url'])
  <button type="button" class="ig-tab" id="igTab" aria-controls="igDrawer" aria-expanded="false">
    {!! $igIcon !!}
    <span>Instagram</span>
  </button>
  @endif
  @if ($yt['url'] && ($youtubeVideos ?? collect())->isNotEmpty())
    <button type="button" class="ig-tab yt-tab" id="ytTab" aria-controls="ytDrawer" aria-expanded="false">
      {!! $ytIcon !!}
      <span>YouTube</span>
    </button>
  @elseif ($yt['url'])
    <a href="{{ $yt['url'] }}" class="ig-tab yt-tab" target="_blank" rel="noopener" aria-label="YouTube {{ '@'.$yt['handle'] }} ({{ __('buka di tab baru') }})">
      {!! $ytIcon !!}
      <span>YouTube</span>
    </a>
  @endif
  @if ($tt['url'])
  <button type="button" class="ig-tab tt-tab" id="ttTab" aria-controls="ttDrawer" aria-expanded="false">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.1v12.4a2.6 2.6 0 1 1-2.6-2.6c.3 0 .5 0 .8.1V9.7a5.7 5.7 0 1 0 4.9 5.7V9a7.4 7.4 0 0 0 4.3 1.4V7.3a4.3 4.3 0 0 1-3.2-1.5z"/></svg>
    <span>TikTok</span>
  </button>
  @endif
</div>
@endif

@if ($ig['url'])
<aside class="ig-drawer" id="igDrawer" aria-label="Instagram {{ '@'.$ig['handle'] }}" aria-hidden="true" inert>
  <div class="ig-head">
    <div class="ig-avatar" aria-hidden="true"><img src="{{ asset('images/logo-igb.png') }}" alt=""></div>
    <div class="ig-who">
      <a href="{{ $ig['url'] }}" target="_blank" rel="noopener" class="ig-handle">{{ '@'.$ig['handle'] }}</a>
      <span class="ig-name">{{ config('company.name') }}</span>
    </div>
    <button type="button" class="ig-close" data-ig-close aria-label="{{ __('Tutup panel Instagram') }}">✕</button>
  </div>

  <a href="{{ $ig['url'] }}" target="_blank" rel="noopener" class="btn btn-red ig-follow">{!! $igIcon !!} {{ __('Follow di Instagram') }}</a>

  @if ($instagramPosts->isNotEmpty())
    <ul class="ig-grid">
      @foreach ($instagramPosts as $post)
        <li>
          <a href="{{ $post->url }}" target="_blank" rel="noopener" class="ig-item" aria-label="{{ $post->caption ? \Illuminate\Support\Str::limit($post->caption, 80) : __('Lihat postingan di Instagram') }}">
            @if ($post->image_url)
              <img src="{{ $post->image_url }}" alt="" loading="lazy" decoding="async">
            @endif
            <span class="ig-overlay" aria-hidden="true">
              @if ($post->caption)<span class="ig-caption">{{ \Illuminate\Support\Str::limit($post->caption, 90) }}</span>@endif
              @if ($post->posted_at)<span class="ig-date">{{ $post->posted_at->locale(\App\Support\Locale::carbon())->translatedFormat('d M Y') }}</span>@endif
            </span>
          </a>
        </li>
      @endforeach
    </ul>
    <a href="{{ $ig['url'] }}" target="_blank" rel="noopener" class="link-arrow ig-more">{{ __('Lihat semua postingan') }}
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
    </a>
  @else
    <p class="ig-empty">{{ __('Ikuti kegiatan, proyek terbaru, dan dokumentasi pemasangan kami di Instagram.') }}</p>
  @endif
</aside>
@endif

@if ($yt['url'] && ($youtubeVideos ?? collect())->isNotEmpty())
  <aside class="ig-drawer yt-drawer" id="ytDrawer" aria-label="YouTube {{ '@'.$yt['handle'] }}" aria-hidden="true" inert>
    <div class="ig-head">
      <div class="ig-avatar" aria-hidden="true"><img src="{{ asset('images/logo-igb.png') }}" alt=""></div>
      <div class="ig-who">
        <a href="{{ $yt['url'] }}" target="_blank" rel="noopener" class="ig-handle">{{ '@'.$yt['handle'] }}</a>
        <span class="ig-name">{{ __('Video terbaru di YouTube') }}</span>
      </div>
      <button type="button" class="ig-close" data-yt-close aria-label="{{ __('Tutup panel YouTube') }}">✕</button>
    </div>

    <div class="yt-player" id="ytPlayer" hidden></div>

    <ul class="yt-list">
      @foreach (($youtubeVideos ?? collect()) as $video)
        <li>
          <a href="{{ $video->url }}" target="_blank" rel="noopener" class="yt-item" data-yt-id="{{ $video->id }}" data-yt-title="{{ $video->title }}">
            <span class="yt-thumb">
              <img src="{{ $video->thumb }}" alt="" loading="lazy" decoding="async">
              <span class="yt-play" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
            </span>
            <span class="yt-meta">
              <span class="yt-title">{{ $video->title }}</span>
              @if ($video->published_at)<span class="ig-date">{{ $video->published_at->locale(\App\Support\Locale::carbon())->translatedFormat('d M Y') }}</span>@endif
            </span>
          </a>
        </li>
      @endforeach
    </ul>

    <a href="{{ $yt['url'] }}" target="_blank" rel="noopener" class="btn btn-red ig-follow">{!! $ytIcon !!} {{ __('Subscribe di YouTube') }}</a>
  </aside>
@endif
@if ($tt['url'])
  {{-- TikTok's official creator embed. Loaded only when the panel is first
       opened (see site.js), so TikTok's scripts never slow down other pages. --}}
  <aside class="ig-drawer tt-drawer" id="ttDrawer" aria-label="TikTok {{ '@'.$tt['handle'] }}" aria-hidden="true" inert
         data-tt-user="{{ $tt['handle'] }}" data-tt-url="{{ $tt['url'] }}">
    <div class="ig-head">
      <div class="ig-avatar" aria-hidden="true"><img src="{{ asset('images/logo-igb.png') }}" alt=""></div>
      <div class="ig-who">
        <a href="{{ $tt['url'] }}" target="_blank" rel="noopener" class="ig-handle">{{ '@'.$tt['handle'] }}</a>
        <span class="ig-name">{{ __('Video terbaru di TikTok') }}</span>
      </div>
      <button type="button" class="ig-close" data-tt-close aria-label="{{ __('Tutup panel TikTok') }}">✕</button>
    </div>

    <div class="tt-embed" id="ttEmbed">
      <p class="tt-loading">{{ __('Memuat video TikTok…') }}</p>
    </div>
    <p class="tt-fallback" id="ttFallback" hidden>{{ __('Video belum bisa dimuat di sini (mungkin diblokir browser atau koneksi). Buka langsung di TikTok:') }}</p>

    <a href="{{ $tt['url'] }}" target="_blank" rel="noopener" class="btn btn-red ig-follow">{!! $ttIcon !!} {{ __('Follow di TikTok') }}</a>
  </aside>
@endif
<div class="ig-scrim" id="igScrim" hidden></div>
