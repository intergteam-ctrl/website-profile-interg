@extends('layout.app')

@section('title', $post->title)
@section('description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 155))
@if ($post->image_url)
  @section('og_image', $post->image_url)
@endif

@section('content')

<article class="section section--page">
  <div class="container article">
    <a href="{{ lroute('blog') }}" class="article-back">← {{ __('Semua Artikel') }}</a>

    @if ($post->category)
      <div class="eyebrow">{{ $post->category }}</div>
    @endif
    <h1 class="section-title">{{ $post->title }}</h1>

    <div class="article-meta">
      @if ($post->published_at)
        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->locale(\App\Support\Locale::carbon())->translatedFormat('d F Y') }}</time>
      @endif
      <span aria-hidden="true">·</span>
      <span>{{ max(1, (int) ceil(str_word_count(strip_tags((string) $post->content)) / 200)) }} {{ __('menit baca') }}</span>
    </div>

    @if ($post->image_url)
      <div class="article-cover">
        <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
      </div>
    @endif

    <div class="prose">
      {!! $content !!}
    </div>
  </div>
</article>

@endsection
