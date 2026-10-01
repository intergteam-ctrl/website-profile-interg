@extends('layout.app')

@section('title', 'Portfolio')
@section('description', 'Project yang telah dikerjakan Inter G Queen Bumindo — display solution, IoT, hingga software development untuk pemerintahan dan perusahaan.')

@section('content')

<section class="section section--page">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Portfolio</div>
      <h1 class="section-title">Semua <em>Project</em></h1>
      <p class="section-sub">Berbagai project yang telah kami kerjakan — dari display solution, IoT, hingga software development.</p>
    </div>

    @if ($portfolios->isNotEmpty())
      <div class="grid-3">
        @foreach ($portfolios as $item)
          @include('partials.portfolio-card', ['item' => $item])
        @endforeach
      </div>
      @include('partials.pager', ['paginator' => $portfolios])
    @else
      <p class="empty-state">Belum ada project yang ditampilkan.</p>
    @endif
  </div>
</section>

@endsection
