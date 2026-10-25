@extends('layout.app')

@section('title', __('Download'))
@section('description', __('Unduh company profile, brosur, panduan, dan dokumen lain dari Inter G Queen Bumindo (IGB).'))

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Download</div>
      <h1 class="section-title">{{ __('Pusat Unduhan') }}</h1>
      <p class="section-sub">{{ __('Company profile, brosur, panduan, dan dokumen pendukung lainnya dari :name.', ['name' => config('company.short_name')]) }}</p>
    </div>

    @if ($total > 0)
      @if ($groups->count() > 1)
        <nav class="dl-cats" aria-label="{{ __('Kategori unduhan') }}">
          @foreach ($groups as $category => $items)
            <a href="#dl-{{ \Illuminate\Support\Str::slug($category ?: 'lainnya') }}">{{ $category ?: __('Lainnya') }} <span>{{ $items->count() }}</span></a>
          @endforeach
        </nav>
      @endif

      @foreach ($groups as $category => $items)
        <div class="dl-group" id="dl-{{ \Illuminate\Support\Str::slug($category ?: 'lainnya') }}">
          @if ($groups->count() > 1)
            <h2 class="dl-group-title">{{ $category ?: __('Lainnya') }}</h2>
          @endif
          <ul class="dl-list">
            @foreach ($items as $item)
              <li class="dl-item reveal">
                <span class="dl-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11zm-6-2 4-4h-2.5v-4h-3v4H8l4 4z"/></svg>
                </span>
                <div class="dl-body">
                  <h3 class="dl-title">{{ $item->title }}</h3>
                  @if ($item->description)
                    <p class="dl-desc">{{ $item->description }}</p>
                  @endif
                  <div class="dl-meta">
                    <span class="dl-kind">{{ __($item->kind) }}</span>
                    @if ($item->host)<span>{{ $item->host }}</span>@endif
                  </div>
                </div>
                <a href="{{ $item->safe_url }}" class="btn btn-red dl-btn" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Download') }} {{ $item->title }} ({{ __('buka di tab baru') }})">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z"/></svg>
                  {{ __('Download') }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endforeach
    @else
      <p class="empty-state">{{ __('Belum ada file yang bisa diunduh.') }}</p>
    @endif
  </div>
</section>

@endsection
