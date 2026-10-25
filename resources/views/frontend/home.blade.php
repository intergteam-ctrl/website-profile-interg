@extends('layout.app')

@php
  $c = config('company');
  $img = fn (string $file) => asset('images/profile/'.$file);
  $arrow = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>';

  // Text edited in Pengaturan Situs is Indonesian. On /en and /zh it is used
  // only if a translation exists; otherwise the translated default is shown.
  $isId = \App\Support\Locale::current() === 'id';
  $tr = function (?string $admin, string $default) use ($isId): string {
      if ($isId) {
          return $admin ?: $default;
      }

      return ($admin && __($admin) !== $admin) ? __($admin) : __($default);
  };
  $heroLines = ($isId && $setting?->hero_title)
      ? array_values(array_filter(array_map('trim', preg_split('/\R/', $setting->hero_title))))
      : array_map(fn ($l) => __($l), ['Akselerasi', 'Digital 5.0', 'Tanpa Batas']);
@endphp

@section('content')

{{-- ============================== HERO ============================== --}}
<section id="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <div class="hero-tag">{{ $tr($setting?->hero_tag, 'Total IT Solution Provider') }}</div>
      <h1 class="hero-headline">
        @foreach ($heroLines as $line)
          <span class="line">{{ $line }}</span>
        @endforeach
      </h1>
      <p class="hero-sub">
        {{ $tr($setting?->hero_subtitle, 'Membantu perusahaan dan pemerintahan bertransformasi melalui solusi teknologi modern — dari system integration, IoT, software development, hingga display solution profesional.') }}
      </p>
      <div class="hero-ctas">
        <a href="#contact" class="btn btn-red">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
          {{ __('Konsultasi Sekarang') }}
        </a>
        <a href="#services" class="btn btn-on-dark">{{ __('Lihat Layanan') }}</a>
      </div>
      <dl class="hero-facts">
        @foreach ($facts as $fact)
          <div>
            <dt class="sr-only">{{ __($fact['label']) }}</dt>
            <dd>
              <span class="hero-fact-num">{{ $fact['value'] }}</span>
              <span class="hero-fact-label" aria-hidden="true">{{ __($fact['label']) }}</span>
            </dd>
          </div>
        @endforeach
      </dl>
    </div>

    {{-- A video wall of real installations: the company's own craft. --}}
    <div class="wall" aria-label="{{ __('Contoh proyek terpasang') }}">
      <figure class="wall-tile wall-tile--a">
        <img src="{{ $img('cr-dishub.jpg') }}" alt="{{ __('Control room Dinas Perhubungan Provinsi Jawa Timur') }}" fetchpriority="high">
        <span class="wall-live" aria-hidden="true">{{ __('Live') }}</span>
        <figcaption>{{ __('Control Room Dishub Prov. Jawa Timur') }}</figcaption>
      </figure>
      <figure class="wall-tile wall-tile--b">
        <img src="{{ $img('iot-awlr-site.jpg') }}" alt="{{ __('Stasiun telemetri AWLR di lapangan') }}" loading="lazy">
        <figcaption>{{ __('Telemetri AWLR') }}</figcaption>
      </figure>
      <figure class="wall-tile wall-tile--c">
        <img src="{{ $img('cctv-ai-counting.jpg') }}" alt="{{ __('Kamera AI menghitung kendaraan') }}" loading="lazy">
        <figcaption>{{ __('CCTV AI counting') }}</figcaption>
      </figure>
      <figure class="wall-tile wall-tile--d">
        <img src="{{ $img('cr-sda.jpg') }}" alt="{{ __('Control room Dinas Sumber Daya Air Provinsi Jawa Timur') }}" loading="lazy">
        <figcaption>{{ __('Control Room SDA Jatim') }}</figcaption>
      </figure>
      <figure class="wall-tile wall-tile--e">
        <img src="{{ $img('iot-vms-gantry.jpg') }}" alt="{{ __('Variable message sign di ruas jalan') }}" loading="lazy">
        <figcaption>{{ __('VMS ruas jalan') }}</figcaption>
      </figure>
      <figure class="wall-tile wall-tile--f">
        <img src="{{ $img('proj-pupr.jpg') }}" alt="{{ __('Video wall Kementerian PUPR') }}" loading="lazy">
        <figcaption>{{ __('Kementerian PUPR') }}</figcaption>
      </figure>
    </div>
  </div>
</section>

{{-- ============================== CLIENTS ============================== --}}
<section class="clients" aria-label="{{ __('Klien') }}">
  <div class="container clients-inner">
    <div class="clients-label">{{ __('Dipercaya oleh') }}</div>
    <ul class="clients-list">
      @foreach ($c['clients'] as $client)
        <li>{{ __($client) }}</li>
      @endforeach
    </ul>
  </div>
</section>

{{-- ============================== ABOUT ============================== --}}
<section id="about" class="section">
  <div class="container about-grid">
    <div class="about-visual reveal">
      <span class="slash slash--red" aria-hidden="true"></span>
      <span class="slash slash--blue" aria-hidden="true"></span>
      <div class="about-photo">
        <img src="{{ $img('proj-dishub-monitoring.jpg') }}" alt="{{ __('Monitoring room Dinas Perhubungan Provinsi Jawa Timur') }}" loading="lazy">
      </div>
      <p class="about-quote">
        <strong>Total IT Solution</strong>
        <span>{{ __('Satu mitra untuk integrasi sistem, software, IoT, dan display.') }}</span>
      </p>
    </div>

    <div class="reveal">
      <h2 class="hi-there">{{ __('Satu mitra untuk seluruh kebutuhan teknologi Anda') }}</h2>
      <div class="about-body">
        @if ($setting?->about_text && ($isId || __($setting->about_text) !== $setting->about_text))
          <p>{!! nl2br(e(__($setting->about_text))) !!}</p>
        @else
          @foreach ($c['intro'] as $paragraph)
            <p>{{ __($paragraph) }}</p>
          @endforeach
        @endif
      </div>
      <p class="about-lead">{{ __('Membangun solusi teknologi punya banyak tantangan:') }}</p>
      <ul class="challenge-list">
        @foreach ($c['challenges'] as $challenge)
          <li>{{ __($challenge) }}</li>
        @endforeach
      </ul>
      <div class="solution-box">
        {!! __('<strong>Inter G Technology</strong> hadir menjawab tantangan tersebut dengan konsep <strong>Total IT Solution</strong> — berbagai solusi untuk memenuhi seluruh kebutuhan teknologi, baik pemerintahan maupun perusahaan.') !!}
      </div>
    </div>
  </div>
</section>

{{-- ============================== SERVICES ============================== --}}
<section id="services" class="section section--tint">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Layanan Kami</div>
      <h2 class="section-title">{{ __('Layanan kami') }}</h2>
      <p class="section-sub">{{ $tr($setting?->services_intro, 'Layanan yang saling melengkapi — dari perencanaan, implementasi, hingga pemeliharaan.') }}</p>
    </div>
    <div class="services-grid">
      @foreach ($c['services'] as $i => $service)
        <article class="service-card reveal">
          <span class="service-num">0{{ $i + 1 }}</span>
          <div class="service-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="{{ $service['icon'] }}"/></svg></div>
          <h3 class="service-title">{{ __($service['title']) }}</h3>
          <p class="service-desc">{{ __($service['desc']) }}</p>
          @if ($service['anchor'] === 'contact')
            <a href="#contact" class="link-arrow" data-service="{{ $service['title'] }}">{{ __('Konsultasikan') }} {!! $arrow !!}</a>
          @else
            <a href="#{{ $service['anchor'] }}" class="link-arrow">{{ __('Lihat solusi & proyek') }} {!! $arrow !!}</a>
          @endif
        </article>
      @endforeach
    </div>
  </div>
</section>

{{-- ============================== DISPLAY SOLUTION ============================== --}}
<section id="display" class="section">
  <div class="container">
    <div class="feature">
      <div class="feature-media reveal">
        <span class="slash slash--red" aria-hidden="true"></span>
        <span class="slash slash--blue" aria-hidden="true"></span>
        <img src="{{ $img('display-videowall.jpg') }}" alt="{{ __('Ilustrasi video wall multi-panel') }}" loading="lazy">
      </div>
      <div class="feature-text reveal">
        <div class="eyebrow">Solusi</div>
        <h2 class="section-title">{{ __('Professional Integrated Display Solution') }}</h2>
        <p class="section-sub">{{ __('Solusi display profesional untuk aplikasi bisnis dan pemerintahan. Setiap produk berbasis teknologi yang telah terbukti untuk operasi tanpa henti (24×7), multi-sumber, resolusi besar, dan arsitektur yang sepenuhnya redundan.') }}</p>
        <ul class="chip-list">
          @foreach ($c['display']['applications'] as $app)
            <li>{{ __($app) }}</li>
          @endforeach
        </ul>
      </div>
    </div>

    <div class="solution-columns">
      <div class="solution-col reveal">
        <h3>{{ __('Product Solution') }}</h3>
        <ul class="check-list">
          @foreach ($c['display']['products'] as $item)<li>{{ __($item) }}</li>@endforeach
        </ul>
      </div>
      <div class="solution-col reveal">
        <h3>{{ __('Service Solution') }}</h3>
        <ul class="check-list">
          @foreach ($c['display']['services'] as $item)<li>{{ __($item) }}</li>@endforeach
        </ul>
      </div>
    </div>

    <div class="realisation">
      <h3 class="realisation-title reveal">{{ __('Dari desain menjadi ruang kendali nyata') }}</h3>
      <div class="realisation-grid">
        @foreach ($c['display']['realisations'] as $r)
          <div class="realisation-card reveal">
            <div class="realisation-pair">
              <figure><img src="{{ $img($r['design']) }}" alt="{{ __('Desain') }} {{ __($r['title']) }}" loading="lazy"><figcaption>{{ __('Desain') }}</figcaption></figure>
              <figure><img src="{{ $img($r['built']) }}" alt="{{ __('Realisasi') }} {{ __($r['title']) }}" loading="lazy"><figcaption>{{ __('Realisasi') }}</figcaption></figure>
            </div>
            <p>{{ __($r['title']) }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<section id="projects" class="section section--cream">
  <div class="container">
    <div class="section-head section-head--split">
      <div class="reveal">
        <div class="eyebrow">Portfolio</div>
        <h2 class="section-title">{{ __('Proyek display terpasang') }}</h2>
      </div>
      <a href="{{ lroute('portfolio') }}" class="btn btn-ghost reveal">{{ __('Semua portfolio') }}</a>
    </div>
    <div class="gallery">
      @foreach ($displayProjects as $p)
        <figure class="gallery-item reveal">
          @if ($p['image'])
            <img src="{{ $p['image'] }}" alt="{{ $p['category'] }} {{ $p['title'] }}" loading="lazy">
          @endif
          <figcaption>
            @if ($p['category'])<div class="g-type">{{ __($p['category']) }}</div>@endif
            <div class="g-client">{{ __($p['title']) }}</div>
            @if ($p['subtitle'])<div class="g-tech">{{ $p['subtitle'] }}</div>@endif
          </figcaption>
        </figure>
      @endforeach
    </div>
    <figure class="wide-photo reveal">
      <img src="{{ $img('vms-collage.jpg') }}" alt="{{ __('Pemasangan videotron dan VMS di jalan raya') }}" loading="lazy">
      <figcaption><strong>Videotron &amp; VMS</strong><span>{{ __('Informasi lalu lintas dan waktu tempuh di ruas jalan') }}</span></figcaption>
    </figure>
  </div>
</section>

{{-- ============================== SOFTWARE ============================== --}}
<section id="software" class="section">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Solusi</div>
      <h2 class="section-title">{{ __('Software & App Development') }}</h2>
      <p class="section-sub">{{ __('Aplikasi yang telah kami bangun untuk instansi pemerintah dan sektor publik — dari transportasi hingga sumber daya air.') }}</p>
    </div>
    <div class="apps-grid">
      @foreach ($apps as $i => $app)
        <article class="app-card reveal">
          <div class="app-shot">
            @if ($app['image'])
              <img src="{{ $app['image'] }}" alt="{{ __('Tampilan aplikasi') }} {{ $app['title'] }}" loading="lazy">
            @endif
            <span class="app-num">{{ $i + 1 }}</span>
          </div>
          <div class="app-body">
            <h3 class="app-name">{{ __($app['title']) }}</h3>
            @if ($app['subtitle'])<div class="app-client">{{ __($app['subtitle']) }}</div>@endif
            <p class="app-desc">{{ \Illuminate\Support\Str::limit(__((string) $app['desc']), 220) }}</p>
            @if (! empty($app['url']))
              <a href="{{ $app['url'] }}" class="link-arrow app-link" target="_blank" rel="noopener">{{ __('Kunjungi aplikasi') }} {!! $arrow !!}</a>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>

{{-- ============================== IOT ============================== --}}
<section id="iot" class="section section--tint">
  <div class="container">
    <div class="feature">
      <div class="feature-text reveal">
        <div class="eyebrow">Solusi</div>
        <h2 class="section-title">{{ __('IoT Solution') }}</h2>
        <p class="section-sub">{{ __($c['iot']['intro']) }}</p>
        <p class="iot-lead">{{ __('Arsitektur sistem telemetri kami:') }}</p>
        <ol class="layer-stack">
          @foreach ($c['iot']['layers'] as $layer)<li>{{ __($layer) }}</li>@endforeach
        </ol>
      </div>
      <div class="feature-media reveal">
        <span class="slash slash--red" aria-hidden="true"></span>
        <span class="slash slash--yellow" aria-hidden="true"></span>
        <img src="{{ $img('iot-awlr-install.jpg') }}" alt="{{ __('Teknisi IGB memasang perangkat telemetri di lapangan') }}" loading="lazy">
      </div>
    </div>

    <div class="iot-projects">
      @foreach ($iotProjects as $p)
        <article class="iot-card reveal">
          @if ($p['image'])
            <img src="{{ $p['image'] }}" alt="{{ __($p['title']) }}" loading="lazy">
          @endif
          <div class="iot-card-body">
            <h3>{{ __($p['title']) }}</h3>
            <p>{{ \Illuminate\Support\Str::limit(__((string) $p['desc']), 220) }}</p>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>

{{-- ============================== SURVEILLANCE ============================== --}}
<section id="surveillance" class="section">
  <div class="container">
    <div class="feature feature--flip">
      <div class="feature-media reveal">
        <span class="slash slash--red" aria-hidden="true"></span>
        <span class="slash slash--blue" aria-hidden="true"></span>
        <img src="{{ $img('cctv-ai-counting.jpg') }}" alt="{{ __('Kamera AI menghitung dan mengklasifikasi kendaraan') }}" loading="lazy">
      </div>
      <div class="feature-text reveal">
        <div class="eyebrow">Solusi</div>
        <h2 class="section-title">{{ __('Surveillance Camera') }}</h2>
        <p class="section-sub">{{ __($c['surveillance']['intro']) }} {{ __('Sistem visi kamera berbasis AI kami dapat diterapkan untuk:') }}</p>
        <ul class="ai-grid">
          @foreach ($c['surveillance']['ai'] as $cap)<li>{{ __($cap) }}</li>@endforeach
        </ul>
      </div>
    </div>
    <div class="feature">
      <div class="feature-media reveal">
        <span class="slash slash--yellow" aria-hidden="true"></span>
        <div class="media-frame"><img src="{{ $img('cctv-central.jpg') }}" alt="{{ __('Dashboard monitoring kamera terpusat') }}" loading="lazy"></div>
      </div>
      <div class="feature-text reveal">
        <h3 class="section-title" style="font-size:clamp(24px,2.6vw,32px)">{{ __('Monitoring terpusat, multi-vendor') }}</h3>
        <p class="section-sub">{{ __($c['surveillance']['central']) }}</p>
        <a href="#contact" class="btn btn-red" style="margin-top:24px" data-service="IoT Solution &amp; Surveillance Camera">{{ __('Diskusikan kebutuhan Anda') }}</a>
      </div>
    </div>
  </div>
</section>

{{-- ============================== BLOG ============================== --}}
@if ($posts->isNotEmpty())
<section id="blog" class="section section--tint">
  <div class="container">
    <div class="section-head section-head--split">
      <div class="reveal">
        <div class="eyebrow">Blog &amp; Insight</div>
        <h2 class="section-title">{{ __('Artikel terbaru') }}</h2>
      </div>
      <a href="{{ lroute('blog') }}" class="btn btn-ghost reveal">{{ __('Semua artikel') }}</a>
    </div>
    <div class="grid-3">
      @foreach ($posts as $post)
        @include('partials.post-card', ['post' => $post])
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ============================== CONTACT ============================== --}}
<section id="contact" class="section">
  <span class="slash slash--red" aria-hidden="true"></span>
  <span class="slash slash--yellow" aria-hidden="true"></span>
  <div class="container contact-grid">
    <div class="reveal">
      <h2 class="get-in-touch">{{ __('Diskusikan kebutuhan Anda') }}</h2>
      <p class="contact-intro">{{ __('Untuk menjalin kemitraan, mengajukan pertanyaan, atau mendapatkan informasi lebih lanjut tentang layanan kami, jangan ragu menghubungi tim kami. Kami siap memberikan solusi yang sesuai dengan kebutuhan bisnis Anda.') }}</p>

      <div class="contact-card">
        <h3>{{ __('Hubungi kami') }}</h3>
        <div class="contact-info-list">
          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg></div>
            <div>
              <div class="contact-info-label">{{ __('Telepon') }}</div>
              <div class="contact-info-val"><a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}">{{ $site['phone'] }}</a></div>
            </div>
          </div>
          @if ($site['whatsapp_link'])
            <div class="contact-info-item">
              <div class="contact-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.4c1.4.8 3.1 1.2 4.8 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2zm5.5 12.4c-.2.7-1.1 1.2-1.8 1.3-.5.1-1.1 0-1.6-.2-2.5-.8-4.4-2.3-6-4.5-.8-1.1-1.3-2.3-1.4-3.5 0-1 .3-1.9 1-2.6.2-.2.5-.3.7-.3h.5c.2 0 .4 0 .6.4.2.5.8 1.9.9 2.1.1.2.1.4 0 .6-.2.3-.5.6-.8.9-.1.1-.2.3-.1.5.4.7.9 1.4 1.5 2 .6.6 1.3 1 2 1.3.2.1.4.1.5-.1.2-.2.6-.9.8-1.1.2-.2.4-.2.6-.1.2.1 1.5.8 1.8.9.2.1.4.2.5.3.1.1.1.7-.2 1.3z"/></svg></div>
              <div>
                <div class="contact-info-label">WhatsApp</div>
                <div class="contact-info-val"><a href="{{ $site['whatsapp_link'] }}" target="_blank" rel="noopener">{{ $site['whatsapp'] }}</a></div>
              </div>
            </div>
          @endif
          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg></div>
            <div>
              <div class="contact-info-label">{{ __('Email') }}</div>
              <div class="contact-info-val"><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></div>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg></div>
            <div>
              <div class="contact-info-label">{{ __('Alamat') }}</div>
              <div class="contact-info-val">{!! nl2br(e($site['address'])) !!}</div>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm6.9 6h-2.9a15.7 15.7 0 0 0-1.4-3.6A8 8 0 0 1 18.9 8zM12 4c.8 1.2 1.5 2.5 1.9 4h-3.8c.4-1.5 1.1-2.8 1.9-4zM4.3 14a8.2 8.2 0 0 1 0-4h3.4a16.5 16.5 0 0 0 0 4H4.3zm.8 2h2.9c.3 1.3.8 2.5 1.4 3.6A8 8 0 0 1 5.1 16zM8 8H5.1a8 8 0 0 1 4.3-3.6C8.8 5.5 8.3 6.7 8 8zm4 12c-.8-1.2-1.5-2.5-1.9-4h3.8c-.4 1.5-1.1 2.8-1.9 4zm2.3-6H9.7a14.7 14.7 0 0 1 0-4h4.6a14.7 14.7 0 0 1 0 4zm.3 5.6c.6-1.1 1.1-2.3 1.4-3.6h2.9a8 8 0 0 1-4.3 3.6zm1.8-5.6a16.5 16.5 0 0 0 0-4h3.4a8.2 8.2 0 0 1 0 4h-3.4z"/></svg></div>
            <div>
              <div class="contact-info-label">{{ __('Website') }}</div>
              <div class="contact-info-val"><a href="https://{{ preg_replace('#^https?://#', '', $site['website']) }}" target="_blank" rel="noopener">{{ $site['website'] }}</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <form class="contact-form reveal" method="POST" action="{{ route('contact.store') }}" novalidate data-contact-form>
      @csrf
      <input type="hidden" name="locale" value="{{ \App\Support\Locale::current() }}">
      <h3 class="contact-form-title">{{ __('Kirim Pesan') }}</h3>
      <p class="contact-form-sub">{{ __('Ceritakan kebutuhan Anda, tim kami akan segera menghubungi.') }}</p>

      @if (session('contact_success'))
        <div class="alert alert-success" role="status">{{ session('contact_success') }}</div>
      @elseif ($errors->any())
        <div class="alert alert-error" role="alert">{{ __('Mohon periksa kembali isian yang ditandai.') }}</div>
      @endif

      <div class="form-hp" aria-hidden="true">
        <label for="website_url">{{ __('Jangan diisi') }}</label>
        <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="cf-name">{{ __('Nama Lengkap') }} <span class="req">*</span></label>
          <input class="form-input" id="cf-name" name="name" type="text" placeholder="{{ __('Nama Anda') }}" value="{{ old('name') }}" required maxlength="120" autocomplete="name" @error('name') aria-invalid="true" aria-describedby="cf-name-error" @enderror>
          @error('name')<span class="form-error" id="cf-name-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="cf-email">Email <span class="req">*</span></label>
          <input class="form-input" id="cf-email" name="email" type="email" placeholder="{{ __('email@perusahaan.com') }}" value="{{ old('email') }}" required maxlength="190" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="cf-email-error" @enderror>
          @error('email')<span class="form-error" id="cf-email-error">{{ $message }}</span>@enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="cf-company">{{ __('Perusahaan / Instansi') }}</label>
        <input class="form-input" id="cf-company" name="company" type="text" placeholder="{{ __('Nama perusahaan atau instansi') }}" value="{{ old('company') }}" maxlength="190" autocomplete="organization">
        @error('company')<span class="form-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="cf-service">{{ __('Layanan yang Dibutuhkan') }}</label>
        <select class="form-input" id="cf-service" name="service">
          <option value="">{{ __('Pilih layanan...') }}</option>
          @foreach (\App\Models\ContactMessage::SERVICES as $service)
            <option value="{{ $service }}" @selected(old('service') === $service)>{{ __($service) }}</option>
          @endforeach
        </select>
        @error('service')<span class="form-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="cf-message">{{ __('Pesan') }} <span class="req">*</span></label>
        <textarea class="form-textarea" id="cf-message" name="message" placeholder="{{ __('Ceritakan kebutuhan atau pertanyaan Anda...') }}" required minlength="10" maxlength="5000" @error('message') aria-invalid="true" aria-describedby="cf-message-error" @enderror>{{ old('message') }}</textarea>
        @error('message')<span class="form-error" id="cf-message-error">{{ $message }}</span>@enderror
      </div>

      <button type="submit" class="btn btn-red form-submit">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        {{ __('Kirim Pesan') }}
      </button>
      <p class="form-note">{{ __('Data Anda hanya digunakan untuk menindaklanjuti pesan ini.') }}</p>
    </form>
  </div>

  @php
    $geo = config('company.geo');
    $ll = $geo['lat'].','.$geo['lng'];
    $maps = [
        'google' => 'https://www.google.com/maps/search/?api=1&query='.$ll,
        'route' => 'https://www.google.com/maps/dir/?api=1&destination='.$ll,
        'waze' => 'https://waze.com/ul?ll='.$ll.'&navigate=yes',
        'apple' => 'https://maps.apple.com/?ll='.$ll.'&q='.rawurlencode(config('company.name')),
        'osm' => 'https://www.openstreetmap.org/?mlat='.$geo['lat'].'&mlon='.$geo['lng'].'#map='.$geo['zoom'].'/'.$ll,
    ];
  @endphp
  <div class="container">
    <div class="map-card reveal" id="lokasi">
      <div class="map-frame">
        <iframe
          src="https://maps.google.com/maps?q={{ $ll }}&z={{ $geo['zoom'] }}&hl={{ \App\Support\Locale::current() === 'zh' ? 'zh-CN' : \App\Support\Locale::current() }}&output=embed"
          title="{{ __('Peta lokasi kantor') }} {{ config('company.name') }}"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen></iframe>
      </div>
      <div class="map-info">
        <div class="eyebrow">{{ __('Lokasi Kantor') }}</div>
        <h3 class="map-title">{{ config('company.name') }}</h3>
        <p class="map-address">{!! nl2br(e($site['address'])) !!}</p>
        <p class="map-coords">{{ number_format($geo['lat'], 6) }}, {{ number_format($geo['lng'], 6) }}</p>
        <div class="map-actions">
          <a href="{{ $maps['route'] }}" class="btn btn-red" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.7 11.3l-9-9a1 1 0 0 0-1.4 0l-9 9a1 1 0 0 0 0 1.4l9 9a1 1 0 0 0 1.4 0l9-9a1 1 0 0 0 0-1.4zM14 14.5V12h-4v3H8v-4a1 1 0 0 1 1-1h5V7.5l3.5 3.5-3.5 3.5z"/></svg>
            {{ __('Petunjuk Arah') }}
          </a>
          <div class="map-alt">
            <a href="{{ $maps['google'] }}" target="_blank" rel="noopener">Google Maps</a>
            <a href="{{ $maps['waze'] }}" target="_blank" rel="noopener">Waze</a>
            <a href="{{ $maps['apple'] }}" target="_blank" rel="noopener">Apple Maps</a>
            <a href="{{ $maps['osm'] }}" target="_blank" rel="noopener">OpenStreetMap</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @php($localBusiness = [
      '@context' => 'https://schema.org',
      '@type' => 'LocalBusiness',
      'name' => config('company.name'),
      'alternateName' => 'IG&B',
      'url' => url('/'),
      'logo' => asset('images/logo-igb.png'),
      'image' => asset('images/profile/hero-building.jpg'),
      'telephone' => $site['phone'],
      'email' => $site['email'],
      'address' => [
          '@type' => 'PostalAddress',
          'streetAddress' => 'Perum Permata Jingga Blok AA No. 27, Tunggulwulung, Lowokwaru',
          'addressLocality' => 'Kota Malang',
          'addressRegion' => 'Jawa Timur',
          'postalCode' => '65143',
          'addressCountry' => 'ID',
      ],
      'geo' => ['@type' => 'GeoCoordinates', 'latitude' => $geo['lat'], 'longitude' => $geo['lng']],
      'hasMap' => $maps['google'],
      'sameAs' => array_values(array_merge(\App\Support\Socials::links(), array_column(\App\Support\Socials::others(), 'url'))),
  ])
  {{-- JSON_HEX_TAG keeps "</script>" sequences from breaking out of the tag. --}}
  <script type="application/ld+json">{!! json_encode($localBusiness, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
</section>


@endsection
