<article class="media-card reveal">
  <div class="media-thumb">
    @if ($item->image_url)
      <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy" decoding="async">
    @else
      <div class="media-thumb-inner">
        <div class="media-thumb-icon" aria-hidden="true">🖥️</div>
        <div class="media-thumb-label">{{ $item->category ?: 'Project' }}</div>
      </div>
    @endif
  </div>
  <div class="media-body">
    @if ($item->category)
      <div class="media-cat">{{ $item->category }}</div>
    @endif
    <h3 class="media-title">{{ $item->title }}</h3>
    <p class="media-desc">{{ \Illuminate\Support\Str::limit((string) $item->description, 140) }}</p>
  </div>
</article>
