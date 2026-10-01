@extends('layout.app')

@php
  $stats = config('company.stats');
  $arrow = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>';

  $services = [
      ['title' => 'System Integrator', 'desc' => 'Solusi terpadu yang mengintegrasikan berbagai komponen sistem untuk meningkatkan efisiensi operasional dan kinerja bisnis Anda.',
       'icon' => 'M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z'],
      ['title' => 'Software & App Development', 'desc' => 'Tim ahli kami merancang solusi perangkat lunak inovatif — mulai dari aplikasi web, desktop, hingga mobile yang sesuai kebutuhan unik Anda.',
       'icon' => 'M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z'],
      ['title' => 'IoT Solution & Surveillance', 'desc' => 'Solusi IoT menghubungkan perangkat pintar dan mengoptimalkan proses bisnis. Dari kamera pengawas hingga sensor IoT terintegrasi.',
       'icon' => 'M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z'],
      ['title' => 'Display Solution', 'desc' => 'Videotron, VMS, LED Display untuk Control Room, Command Center, NOC, Traffic Control, Digital Signage, dan aplikasi display profesional lainnya.',
       'icon' => 'M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 1.99-.9 1.99-2L23 5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z'],
      ['title' => 'Hardware Service & Maintenance', 'desc' => 'Layanan perawatan dan pemeliharaan perangkat keras memastikan ketersediaan optimal dan kinerja stabil dari sistem IT perusahaan Anda.',
       'icon' => 'M22 9V7h-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-2h2v-2h-2v-2h2v-2h-2V9h2zm-4 10H4V5h14v14zM6 13h5v4H6zm6-6h4v3h-4zM6 7h5v5H6zm6 4h4v6h-4z'],
      ['title' => 'Digital Transformation Consulting', 'desc' => 'Konsultasi transformasi digital end-to-end yang membantu organisasi Anda beradaptasi dan unggul dalam era revolusi industri 5.0.',
       'icon' => 'M13 2.05v2.02c3.95.49 7 3.85 7 7.93 0 3.21-1.81 6-4.72 7.72L13 17v5h5l-1.22-1.22C19.91 19.07 22 15.76 22 12c0-5.18-3.95-9.45-9-9.95zM11 2.05C5.95 2.55 2 6.82 2 12c0 3.76 2.09 7.07 5.22 8.78L6 22h5V2.05z'],
  ];

  $reasons = [
      ['👥', 'Tim Profesional', 'Didukung tenaga ahli berpengalaman di bidang IT, system integration, dan transformasi digital.'],
      ['⚡', 'Teknologi Terkini', 'Selalu mengadopsi teknologi terdepan — AI, IoT, Big Data — untuk memberikan solusi masa depan hari ini.'],
      ['🚀', 'Implementasi Cepat', 'Metodologi delivery efisien memastikan project selesai tepat waktu tanpa mengorbankan kualitas.'],
      ['🛡️', 'Keamanan Data', 'Standar keamanan enterprise-grade melindungi data dan sistem Anda dari ancaman siber.'],
      ['🔧', 'Dukungan 24/7', 'Tim support siap membantu kapan saja — monitoring proaktif dan respons cepat untuk operasional tanpa gangguan.'],
      ['🎯', 'Solusi Sesuai Kebutuhan', 'Setiap solusi dirancang khusus — bukan off-the-shelf — untuk memenuhi kebutuhan spesifik bisnis dan regulasi klien.'],
  ];

  // Shown only while no portfolio has been added in the admin panel.
  $fallbackPortfolios = [
      ['🖥️', 'Display Solution', 'Control Room Dinas SDA Jatim', 'Video wall system terintegrasi untuk monitoring sumber daya air Provinsi Jawa Timur secara real-time.'],
      ['🚦', 'Software Development', 'JT Command Center (JTCC)', 'Platform monitoring lalu lintas real-time Dishub Jatim — analisis data, pengelolaan kejadian, modernisasi transportasi.'],
      ['💧', 'IoT Solution', 'Telemetri AWLR', 'Sistem pemantauan ketinggian air jarak jauh berbasis telemetri dengan akses data real-time untuk Dinas SDA.'],
      ['🛣️', 'Display Solution', 'VMS Traffic Info Kota Malang', 'Variable Message Sign outdoor terintegrasi sistem lalu lintas — estimasi waktu tempuh dan informasi kemacetan.'],
      ['🗄️', 'Software Development', 'SINTA — Sistem Interoperabilitas JT & UPKB', 'Platform web terintegrasi untuk pertukaran data Jembatan Timbang dan UPKB di seluruh Jawa Timur.'],
      ['📡', 'IoT Solution', 'Radar Traffic Counting', 'Sensor radar presisi tinggi untuk analisis kendaraan dan pola lalu lintas — mendukung keputusan berbasis data.'],
  ];
@endphp

@section('content')

{{-- ============================== HERO ============================== --}}
<section id="hero">
  <div class="hero-bg" aria-hidden="true">
    <div class="hero-grid"></div>
    <div class="hero-shape hero-shape-1"></div>
    <div class="hero-shape hero-shape-2"></div>
  </div>
  <div class="hero-content">
    <div class="container hero-layout">
      <div>
        <div class="hero-tag">{{ $setting?->hero_tag ?: 'Total IT Solution Provider' }}</div>
        <h1 class="hero-headline">
          @if ($setting?->hero_title)
            {!! nl2br(e($setting->hero_title)) !!}
          @else
            Akselerasi<br>
            <span class="red-word">Digital 5.0</span><br>
            <span class="accent-word">Tanpa Batas</span>
          @endif
        </h1>
        <p class="hero-sub">
          {{ $setting?->hero_subtitle ?: 'Membantu perusahaan dan pemerintahan bertransformasi melalui solusi teknologi modern — dari system integration, IoT, software development, hingga display solution profesional.' }}
        </p>
        <div class="hero-ctas">
          <a href="#contact" class="btn btn-red">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            Konsultasi Sekarang
          </a>
          <a href="#services" class="btn btn-outline">Lihat Layanan {!! $arrow !!}</a>
        </div>
        <dl class="hero-stats">
          @foreach ($stats as $stat)
            <div>
              <dt class="sr-only">{{ $stat['label'] }}</dt>
              <dd>
                <div class="hero-stat-num">{{ $stat['value'] }}<span>{{ $stat['suffix'] }}</span></div>
                <div class="hero-stat-label" aria-hidden="true">{{ $stat['label'] }}</div>
              </dd>
            </div>
          @endforeach
        </dl>
      </div>

      <div class="hero-visual" aria-hidden="true">
        <div class="hero-card-main">
          <div class="hero-card-header">
            <span class="hero-card-dot"></span><span class="hero-card-dot"></span><span class="hero-card-dot"></span>
            <span class="hero-card-title">IGB Dashboard</span>
          </div>
          <div class="hero-card-screen">
            <div class="hero-screen-bar"></div>
            <div class="hero-screen-bar"></div>
            <div class="hero-screen-bar"></div>
            <div class="hero-screen-grid">
              <div class="hero-screen-cell"><div class="hero-screen-cell-num">{{ $stats['projects']['value'].$stats['projects']['suffix'] }}</div><div class="hero-screen-cell-lbl">Projects</div></div>
              <div class="hero-screen-cell"><div class="hero-screen-cell-num">{{ $stats['clients']['value'].$stats['clients']['suffix'] }}</div><div class="hero-screen-cell-lbl">Clients</div></div>
              <div class="hero-screen-cell"><div class="hero-screen-cell-num">{{ $stats['years']['value'].$stats['years']['suffix'] }}</div><div class="hero-screen-cell-lbl">Years</div></div>
              <div class="hero-screen-cell"><div class="hero-screen-cell-num">{{ $stats['support']['value'].$stats['support']['suffix'] }}</div><div class="hero-screen-cell-lbl">Support</div></div>
            </div>
          </div>
          <div class="hero-progress"><span></span><span></span><span></span></div>
        </div>
        <div class="hero-float-card fc-1">
          <div class="hero-float-card-icon">🔌</div>
          <div>
            <div class="hero-float-card-title">IoT Connected</div>
            <div class="hero-float-card-val">Real-time monitoring aktif</div>
          </div>
        </div>
        <div class="hero-float-card fc-2">
          <div class="hero-float-card-icon">✅</div>
          <div>
            <div class="hero-float-card-title">Project Terkirim</div>
            <div class="hero-float-card-val">Control Room Dishub Jatim</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ============================== ABOUT ============================== --}}
<section id="about" class="section">
  <div class="container about-grid">
    <div class="about-visual reveal">
      <div class="about-stripe" aria-hidden="true"></div>
      <div class="about-img-wrap" aria-hidden="true">
        <svg width="180" height="180" viewBox="0 0 180 180" fill="none">
          <rect x="10" y="10" width="50" height="50" rx="8" fill="rgba(217,4,41,0.25)" stroke="rgba(217,4,41,0.4)" stroke-width="1.5"/>
          <rect x="70" y="10" width="50" height="50" rx="8" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.1)" stroke-width="1.5"/>
          <rect x="130" y="10" width="40" height="50" rx="8" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.1)" stroke-width="1.5"/>
          <rect x="10" y="70" width="50" height="50" rx="8" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.1)" stroke-width="1.5"/>
          <rect x="70" y="70" width="100" height="50" rx="8" fill="rgba(217,4,41,0.15)" stroke="rgba(217,4,41,0.3)" stroke-width="1.5"/>
          <rect x="10" y="130" width="110" height="40" rx="8" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.1)" stroke-width="1.5"/>
          <rect x="130" y="130" width="40" height="40" rx="8" fill="rgba(244,180,0,0.15)" stroke="rgba(244,180,0,0.3)" stroke-width="1.5"/>
          <circle cx="90" cy="95" r="18" fill="rgba(217,4,41,0.3)" stroke="rgba(217,4,41,0.6)" stroke-width="1.5"/>
          <path d="M93 84l-9 13h6l-1 9 9-13h-6l1-9z" fill="#fff"/>
        </svg>
        <div class="about-img-caption">TOTAL IT SOLUTION</div>
      </div>
      <div class="about-badge">
        <div class="about-badge-num">{{ $stats['years']['value'].$stats['years']['suffix'] }}</div>
        <div class="about-badge-lbl">{{ $stats['years']['label'] }}</div>
      </div>
    </div>

    <div class="reveal">
      <div class="section-label">Tentang Kami</div>
      <h2 class="section-title">Inter G Queen<br><span>Bumindo</span></h2>
      <p class="about-body">
        @if ($setting?->about_text)
          {!! nl2br(e($setting->about_text)) !!}
        @else
          Seiring perkembangan revolusi teknologi 5.0, Inter G hadir menjawab tantangan dengan konsep <strong>Total IT Solution</strong>.
          Kami menyediakan berbagai solusi untuk memenuhi kebutuhan teknologi — baik pemerintahan maupun perusahaan — mulai dari otomasi, big data, IoT,
          hingga Artificial Intelligence.
        @endif
      </p>
      <div class="about-stats">
        @foreach (['projects', 'clients', 'support'] as $key)
          <div class="about-stat">
            <div class="about-stat-num">{{ $stats[$key]['value'].$stats[$key]['suffix'] }}</div>
            <div class="about-stat-lbl">{{ $stats[$key]['label'] }}</div>
          </div>
        @endforeach
      </div>
      <div class="vm-row">
        <div class="vm-item">
          <div class="vm-icon" aria-hidden="true">🎯</div>
          <div>
            <div class="vm-label">Visi</div>
            <div class="vm-text">Menjadi mitra transformasi digital terpercaya yang mengakselerasi inovasi dan teknologi di Indonesia.</div>
          </div>
        </div>
        <div class="vm-item">
          <div class="vm-icon" aria-hidden="true">🚀</div>
          <div>
            <div class="vm-label">Misi</div>
            <div class="vm-text">Menghadirkan solusi teknologi terpadu yang komprehensif, handal, dan berkelanjutan untuk meningkatkan daya saing klien.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ============================== SERVICES ============================== --}}
<section id="services" class="section section--light">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="section-label">Layanan Kami</div>
      <h2 class="section-title">General <span>Services</span></h2>
      <p class="section-sub">{{ $setting?->services_intro ?: 'Solusi teknologi komprehensif yang dirancang khusus untuk kebutuhan pemerintahan dan perusahaan modern.' }}</p>
    </div>
    <div class="grid-3">
      @foreach ($services as $service)
        <article class="service-card reveal">
          <div class="service-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="{{ $service['icon'] }}"/></svg>
          </div>
          <h3 class="service-title">{{ $service['title'] }}</h3>
          <p class="service-desc">{{ $service['desc'] }}</p>
          <a href="#contact" class="service-link" data-service="{{ $service['title'] }}">
            Konsultasikan {!! $arrow !!}
          </a>
        </article>
      @endforeach
    </div>
  </div>
</section>

{{-- ============================== WHY US ============================== --}}
<section id="why" class="section section--dark">
  <div class="container">
    <div class="section-head section-head--center reveal">
      <div class="section-label">Keunggulan Kami</div>
      <h2 class="section-title">Mengapa Memilih <span>IGB?</span></h2>
      <p class="section-sub">Kami menghadirkan kombinasi teknologi mutakhir, tim berpengalaman, dan komitmen penuh terhadap kesuksesan klien.</p>
    </div>
    <div class="grid-3">
      @foreach ($reasons as [$icon, $title, $desc])
        <div class="why-card reveal">
          <div class="why-icon" aria-hidden="true">{{ $icon }}</div>
          <h3 class="why-title">{{ $title }}</h3>
          <p class="why-desc">{{ $desc }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============================== PORTFOLIO ============================== --}}
<section id="portfolio" class="section">
  <div class="container">
    <div class="section-head section-head--split">
      <div class="reveal">
        <div class="section-label">Portfolio</div>
        <h2 class="section-title">Project <span>Unggulan</span></h2>
      </div>
      @if ($portfolios->isNotEmpty())
        <a href="{{ route('portfolio') }}" class="btn btn-navy reveal">Lihat Semua Project →</a>
      @endif
    </div>
    <div class="grid-3">
      @forelse ($portfolios as $item)
        @include('partials.portfolio-card', ['item' => $item])
      @empty
        @foreach ($fallbackPortfolios as [$icon, $category, $title, $desc])
          <article class="media-card reveal">
            <div class="media-thumb">
              <div class="media-thumb-inner">
                <div class="media-thumb-icon" aria-hidden="true">{{ $icon }}</div>
                <div class="media-thumb-label">{{ $category }}</div>
              </div>
            </div>
            <div class="media-body">
              <div class="media-cat">{{ $category }}</div>
              <h3 class="media-title">{{ $title }}</h3>
              <p class="media-desc">{{ $desc }}</p>
            </div>
          </article>
        @endforeach
      @endforelse
    </div>
  </div>
</section>

{{-- ============================== BLOG ============================== --}}
@if ($posts->isNotEmpty())
<section id="blog" class="section section--light">
  <div class="container">
    <div class="section-head section-head--split">
      <div class="reveal">
        <div class="section-label">Blog &amp; Insight</div>
        <h2 class="section-title">Artikel <span>Terkini</span></h2>
      </div>
      <a href="{{ route('blog') }}" class="btn btn-navy reveal">Semua Artikel →</a>
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
<section id="contact" class="section section--dark">
  <div class="container contact-grid">
    <div>
      <div class="section-label">Hubungi Kami</div>
      <h2 class="section-title">Mari Mulai<br><span>Bersama Kami</span></h2>
      <p class="section-sub">Siap membantu Anda bertransformasi digital. Ceritakan kebutuhan Anda dan kami siapkan solusi terbaik.</p>

      <div class="contact-info-list">
        <div class="contact-info-item">
          <div class="contact-info-icon" aria-hidden="true">📞</div>
          <div>
            <div class="contact-info-label">Telepon</div>
            <div class="contact-info-val"><a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}">{{ $site['phone'] }}</a></div>
          </div>
        </div>
        @if ($site['whatsapp_link'])
          <div class="contact-info-item">
            <div class="contact-info-icon" aria-hidden="true">💬</div>
            <div>
              <div class="contact-info-label">WhatsApp</div>
              <div class="contact-info-val"><a href="{{ $site['whatsapp_link'] }}" target="_blank" rel="noopener">{{ $site['whatsapp'] }}</a></div>
            </div>
          </div>
        @endif
        <div class="contact-info-item">
          <div class="contact-info-icon" aria-hidden="true">✉️</div>
          <div>
            <div class="contact-info-label">Email</div>
            <div class="contact-info-val"><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></div>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="contact-info-icon" aria-hidden="true">📍</div>
          <div>
            <div class="contact-info-label">Alamat</div>
            <div class="contact-info-val">{!! nl2br(e($site['address'])) !!}</div>
          </div>
        </div>
      </div>
    </div>

    <form class="contact-form" method="POST" action="{{ route('contact.store') }}" novalidate data-contact-form>
      @csrf
      <h3 class="contact-form-title">Kirim Pesan</h3>

      @if (session('contact_success'))
        <div class="alert alert-success" role="status">{{ session('contact_success') }}</div>
      @elseif ($errors->any())
        <div class="alert alert-error" role="alert">Mohon periksa kembali isian yang ditandai.</div>
      @endif

      {{-- Honeypot (hidden from humans) --}}
      <div class="form-hp" aria-hidden="true">
        <label for="website_url">Jangan diisi</label>
        <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="cf-name">Nama Lengkap <span class="req">*</span></label>
          <input class="form-input" id="cf-name" name="name" type="text" placeholder="Nama Anda" value="{{ old('name') }}" required maxlength="120" autocomplete="name" @error('name') aria-invalid="true" aria-describedby="cf-name-error" @enderror>
          @error('name')<span class="form-error" id="cf-name-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="cf-email">Email <span class="req">*</span></label>
          <input class="form-input" id="cf-email" name="email" type="email" placeholder="email@perusahaan.com" value="{{ old('email') }}" required maxlength="190" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="cf-email-error" @enderror>
          @error('email')<span class="form-error" id="cf-email-error">{{ $message }}</span>@enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="cf-company">Perusahaan / Instansi</label>
        <input class="form-input" id="cf-company" name="company" type="text" placeholder="Nama perusahaan Anda" value="{{ old('company') }}" maxlength="190" autocomplete="organization">
        @error('company')<span class="form-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="cf-service">Layanan yang Dibutuhkan</label>
        <select class="form-input" id="cf-service" name="service">
          <option value="">Pilih layanan...</option>
          @foreach (\App\Models\ContactMessage::SERVICES as $service)
            <option value="{{ $service }}" @selected(old('service') === $service)>{{ $service }}</option>
          @endforeach
        </select>
        @error('service')<span class="form-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="cf-message">Pesan <span class="req">*</span></label>
        <textarea class="form-textarea" id="cf-message" name="message" placeholder="Ceritakan kebutuhan atau pertanyaan Anda..." required minlength="10" maxlength="5000" @error('message') aria-invalid="true" aria-describedby="cf-message-error" @enderror>{{ old('message') }}</textarea>
        @error('message')<span class="form-error" id="cf-message-error">{{ $message }}</span>@enderror
      </div>

      <button type="submit" class="btn btn-red form-submit">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        Kirim Pesan
      </button>
      <p class="form-note">Data Anda hanya digunakan untuk menindaklanjuti pesan ini.</p>
    </form>
  </div>
</section>

{{-- ============================== MARKETPLACE ============================== --}}
<section id="marketplace" class="section section--light">
  <div class="container">
    @include('partials.marketplace', ['heading' => 'h2'])
  </div>
</section>

@endsection
