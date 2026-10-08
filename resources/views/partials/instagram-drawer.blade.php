{{--
  Instagram side panel. Closed by default: only a slim tab on the right edge
  is visible until the visitor opens it. Posts are curated in Admin → Instagram.
  Expects: $instagramPosts (Collection<InstagramPost>).
--}}
@php
  $ig = config('company.instagram');
  $igIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zM12 0C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.2 4.4 2.6 6.8 7 7 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.8a1.4 1.4 0 1 0 0 2.9 1.4 1.4 0 0 0 0-2.9z"/></svg>';
@endphp

<button type="button" class="ig-tab" id="igTab" aria-controls="igDrawer" aria-expanded="false">
  {!! $igIcon !!}
  <span>Instagram</span>
</button>

<aside class="ig-drawer" id="igDrawer" aria-label="Instagram {{ '@'.$ig['handle'] }}" aria-hidden="true" inert>
  <div class="ig-head">
    <div class="ig-avatar" aria-hidden="true"><img src="{{ asset('images/logo-igb.png') }}" alt=""></div>
    <div class="ig-who">
      <a href="{{ $ig['url'] }}" target="_blank" rel="noopener" class="ig-handle">{{ '@'.$ig['handle'] }}</a>
      <span class="ig-name">{{ config('company.name') }}</span>
    </div>
    <button type="button" class="ig-close" data-ig-close aria-label="Tutup panel Instagram">✕</button>
  </div>

  <a href="{{ $ig['url'] }}" target="_blank" rel="noopener" class="btn btn-red ig-follow">{!! $igIcon !!} Follow di Instagram</a>

  @if ($instagramPosts->isNotEmpty())
    <ul class="ig-grid">
      @foreach ($instagramPosts as $post)
        <li>
          <a href="{{ $post->url }}" target="_blank" rel="noopener" class="ig-item" aria-label="{{ $post->caption ? \Illuminate\Support\Str::limit($post->caption, 80) : 'Lihat postingan di Instagram' }}">
            @if ($post->image_url)
              <img src="{{ $post->image_url }}" alt="" loading="lazy" decoding="async">
            @endif
            <span class="ig-overlay" aria-hidden="true">
              @if ($post->caption)<span class="ig-caption">{{ \Illuminate\Support\Str::limit($post->caption, 90) }}</span>@endif
              @if ($post->posted_at)<span class="ig-date">{{ $post->posted_at->locale('id')->translatedFormat('d M Y') }}</span>@endif
            </span>
          </a>
        </li>
      @endforeach
    </ul>
    <a href="{{ $ig['url'] }}" target="_blank" rel="noopener" class="link-arrow ig-more">Lihat semua postingan
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
    </a>
  @else
    <p class="ig-empty">Ikuti kegiatan, proyek terbaru, dan dokumentasi pemasangan kami di Instagram.</p>
  @endif
</aside>
<div class="ig-scrim" id="igScrim" hidden></div>
