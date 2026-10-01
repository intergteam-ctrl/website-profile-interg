@extends('layout.app')

@section('title', 'Blog & Insight')
@section('description', 'Artikel, wawasan, dan update teknologi terbaru dari tim Inter G Queen Bumindo.')

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Blog &amp; Insight</div>
      <h1 class="section-title">Semua <em>Artikel</em></h1>
      <p class="section-sub">Kumpulan artikel, wawasan, dan update teknologi terbaru dari tim kami.</p>
    </div>

    @if ($posts->isNotEmpty())
      <div class="grid-3">
        @foreach ($posts as $post)
          @include('partials.post-card', ['post' => $post])
        @endforeach
      </div>
      @include('partials.pager', ['paginator' => $posts])
    @else
      <p class="empty-state">Belum ada artikel yang dipublikasikan.</p>
    @endif
  </div>
</section>

@endsection
