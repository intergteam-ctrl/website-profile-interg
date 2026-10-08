@php
  $onHome = request()->routeIs('home');
  $section = fn (string $id) => $onHome ? '#'.$id : route('home').'#'.$id;

  $navItems = [
      ['label' => 'Beranda',     'href' => $section('hero')],
      ['label' => 'Tentang',     'href' => $section('about')],
      ['label' => 'Layanan',     'href' => $section('services')],
      ['label' => 'Proyek',      'href' => $section('display')],
      ['label' => 'Blog',        'href' => $onHome ? '#blog' : route('blog'), 'active' => request()->routeIs('blog', 'blog.show')],
      ['label' => 'Kontak',      'href' => $section('contact')],
      ['label' => 'Marketplace', 'href' => $onHome ? '#marketplace' : route('marketplace'), 'active' => request()->routeIs('marketplace')],
  ];

  $assetVersion = fn (string $path) => asset($path).'?v='.(@filemtime(public_path($path)) ?: '1');
  $pageTitle = trim($__env->yieldContent('title'));
  $fullTitle = $pageTitle
      ? $pageTitle.' — '.config('company.short_name').' '.config('company.name')
      : config('company.short_name').' — '.config('company.name').' | '.config('company.tagline');
  $metaDescription = trim($__env->yieldContent('description')) ?: 'Inter G Queen Bumindo (IGB) — Total IT Solution Provider di Malang: system integrator, software development, IoT, display solution, dan IT consulting untuk pemerintahan dan perusahaan.';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $fullTitle }}</title>
  <meta name="description" content="{{ $metaDescription }}">
  <meta name="theme-color" content="#FFFFFF">
  <link rel="canonical" href="{{ url()->current() }}">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="{{ config('company.name') }}">
  <meta property="og:title" content="{{ $fullTitle }}">
  <meta property="og:description" content="{{ $metaDescription }}">
  <meta property="og:url" content="{{ url()->current() }}">
  @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
  @endif

  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,600;1,800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ $assetVersion('css/site.css') }}">
  <script>document.documentElement.classList.add('js');</script>
</head>
<body>
  <a href="#main" class="skip-link">Lewati ke konten</a>

  {{-- Mobile navigation --}}
  <nav class="mobile-nav" id="mobileNav" aria-label="Menu utama (mobile)" aria-hidden="true">
    <button type="button" class="mobile-close" data-mobile-nav-close aria-label="Tutup menu">✕</button>
    @foreach ($navItems as $item)
      <a href="{{ $item['href'] }}" data-mobile-nav-close>{{ $item['label'] }}</a>
    @endforeach
    <a href="{{ $section('contact') }}" class="btn btn-red" data-mobile-nav-close>Konsultasi Gratis</a>
  </nav>

  {{-- Navbar --}}
  <header id="navbar" class="{{ $onHome ? '' : 'solid' }}">
    <div class="container nav-inner">
      <a href="{{ $section('hero') }}" class="brand" aria-label="{{ config('company.name') }} — beranda">
        <img src="{{ asset('images/logo-igb.png') }}" alt="IG&amp;B — Accelerating Innovation and Technology" width="176" height="40">
      </a>

      <ul class="nav-links">
        @foreach ($navItems as $item)
          <li><a href="{{ $item['href'] }}" @class(['active' => $item['active'] ?? false])>{{ $item['label'] }}</a></li>
        @endforeach
      </ul>

      <a href="{{ $section('contact') }}" class="btn btn-red nav-cta">Konsultasi Gratis</a>

      <button type="button" class="hamburger" id="hamburger" aria-label="Buka menu" aria-controls="mobileNav" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <main id="main">
    @yield('content')
  </main>

  @include('partials.instagram-drawer')

  {{-- Footer --}}
  <footer id="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <a href="{{ $section('hero') }}" class="brand">
            <img src="{{ asset('images/logo-igb.png') }}" alt="IG&amp;B — Accelerating Innovation and Technology" width="176" height="40">
          </a>
          <p class="footer-desc">
            {{ config('company.name') }} — Total IT Solution untuk pemerintahan dan perusahaan: system integrator, software, IoT, surveillance, dan display solution.
          </p>
          @php($socials = array_filter(config('company.socials', [])))
          @if ($socials)
            <div class="footer-socials">
              @foreach ($socials as $network => $url)
                <a href="{{ $url }}" class="footer-social" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}">{{ substr($network, 0, 2) }}</a>
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <div class="footer-col-title">Quick Links</div>
          <ul class="footer-links">
            @foreach ($navItems as $item)
              <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
            @endforeach
          </ul>
        </div>

        <div>
          <div class="footer-col-title">Layanan</div>
          <ul class="footer-links">
            @foreach (config('company.services') as $service)
              <li><a href="{{ $section($service['anchor'] === 'contact' ? 'services' : $service['anchor']) }}">{{ $service['title'] }}</a></li>
            @endforeach
          </ul>
        </div>

        <div>
          <div class="footer-col-title">Kontak</div>
          <ul class="footer-links">
            <li><a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}">{{ $site['phone'] }}</a></li>
            @if ($site['whatsapp_link'])
              <li><a href="{{ $site['whatsapp_link'] }}" target="_blank" rel="noopener">WhatsApp {{ $site['whatsapp'] }}</a></li>
            @endif
            <li><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li>
            <li><a href="https://{{ preg_replace('#^https?://#', '', $site['website']) }}" target="_blank" rel="noopener">{{ $site['website'] }}</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <div>© {{ now()->year }} <span>{{ config('company.name') }}</span>. All rights reserved.</div>
        <div class="footer-tagline">{{ config('company.tagline') }}</div>
      </div>
    </div>
  </footer>

  <script src="{{ $assetVersion('js/site.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
