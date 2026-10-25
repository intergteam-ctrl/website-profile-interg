@extends('layout.app')

@section('title', __('Blog & Insight'))
@section('description', __('Artikel, wawasan, dan update teknologi terbaru dari tim Inter G Queen Bumindo.'))

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Blog &amp; Insight</div>
      <h1 class="section-title">{{ __('Semua Artikel') }}</h1>
      <p class="section-sub">{{ __('Kumpulan artikel, wawasan, dan update teknologi terbaru dari tim kami.') }}</p>
    </div>

    @if ($posts->isNotEmpty())
      <div class="grid-3">
        @foreach ($posts as $post)
          @include('partials.post-card', ['post' => $post])
        @endforeach
      </div>
      @include('partials.pager', ['paginator' => $posts])
    @else
      <p class="empty-state">{{ __('Belum ada artikel yang dipublikasikan.') }}</p>
    @endif
  </div>
</section>

@endsection
