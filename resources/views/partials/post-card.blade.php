@php($url = route('blog.show', $post->slug))
<article class="media-card reveal">
  <a href="{{ $url }}" class="media-thumb" tabindex="-1" aria-hidden="true">
    @if ($post->image_url)
      <img src="{{ $post->image_url }}" alt="" loading="lazy" decoding="async">
    @else
      <div class="media-thumb-inner">
        <div class="media-thumb-icon">📰</div>
        @if ($post->category)
          <div class="media-thumb-label">{{ $post->category }}</div>
        @endif
      </div>
    @endif
  </a>
  <div class="media-body">
    @if ($post->category)
      <div class="media-cat">{{ $post->category }}</div>
    @endif
    @if ($post->published_at)
      <time class="media-date" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->locale('id')->translatedFormat('d F Y') }}</time>
    @endif
    <h3 class="media-title"><a href="{{ $url }}">{{ $post->title }}</a></h3>
    <p class="media-desc">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 140) }}</p>
    <a href="{{ $url }}" class="media-link">
      Baca Selengkapnya
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
    </a>
  </div>
</article>
