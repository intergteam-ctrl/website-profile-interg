@extends('layout.app')

@section('title', 'Tech Marketplace')
@section('description', 'Jual-beli komputer, laptop, storage, software, dan aksesoris IT dari Inter G Queen Bumindo, Malang. Pesan langsung via WhatsApp atau email.')

@section('content')

<section id="marketplace" class="section section--light section--page">
  <div class="container">
    @include('partials.marketplace', ['heading' => 'h1'])
  </div>
</section>

<section id="testimonials" class="section">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="section-label">Testimoni</div>
      <h2 class="section-title">Apa Kata <span>Klien Kami</span></h2>
      <p class="section-sub">Kepercayaan klien adalah cerminan kualitas kerja kami.</p>
    </div>
    <div class="grid-3">
      @foreach ([
          ['D', 'Dinas Sumber Daya Air', 'Provinsi Jawa Timur', 'IGB berhasil membangun Control Room kami dari nol hingga beroperasi penuh. Sistem video wall dan integrasi datanya luar biasa — tim yang sangat profesional dan responsif.'],
          ['P', 'Dinas Perhubungan', 'Provinsi Jawa Timur', 'Aplikasi JTCC yang dikembangkan IGB telah merevolusi cara kami memantau lalu lintas. Fitur real-time dan analitik datanya sangat membantu pengambilan keputusan cepat.'],
          ['B', 'BNI Sudirman', 'Command Center Division', 'Command Center kami kini beroperasi 24/7 dengan sistem display terintegrasi dari IGB. Kualitas hardware dan after-sales support mereka melebihi ekspektasi kami.'],
      ] as [$initial, $name, $role, $quote])
        <figure class="testi-card reveal">
          <div class="testi-stars" aria-label="5 dari 5 bintang">★★★★★</div>
          <blockquote class="testi-text">“{{ $quote }}”</blockquote>
          <figcaption class="testi-author">
            <div class="testi-avatar" aria-hidden="true">{{ $initial }}</div>
            <div>
              <div class="testi-name">{{ $name }}</div>
              <div class="testi-role">{{ $role }}</div>
            </div>
          </figcaption>
        </figure>
      @endforeach
    </div>
  </div>
</section>

@endsection
