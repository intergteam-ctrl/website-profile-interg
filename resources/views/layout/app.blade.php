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
      ['label' => 'Marketplace', 'href' => route('marketplace'), 'active' => request()->routeIs('marketplace')],
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

  <link rel="icon" href="{{ $assetVersion('favicon.ico') }}" sizes="any">
  <link rel="icon" type="image/png" href="{{ $assetVersion('favicon-32.png') }}" sizes="32x32">
  <link rel="apple-touch-icon" href="{{ $assetVersion('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,600;1,800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ $assetVersion('css/site.css') }}">
  <script>document.documentElement.classList.add('js');</script>
</head>
<body class="@yield('body_class')">
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
          @php($socialIcons = [
              'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zM17.3 5.5a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4zM21.9 7.3c-.1-1.6-.4-3-1.6-4.2S17.6 1.6 16 1.5C14.4 1.4 9.6 1.4 8 1.5c-1.6.1-3 .4-4.2 1.6S2.2 5.7 2.1 7.3C2 8.9 2 13.7 2.1 15.3c.1 1.6.4 3 1.6 4.2s2.6 1.5 4.2 1.6c1.6.1 6.4.1 8 0 1.6-.1 3-.4 4.2-1.6s1.5-2.6 1.6-4.2c.1-1.6.1-6.4.2-8zM19.8 17.3a3.3 3.3 0 0 1-1.8 1.8c-1.3.5-4.3.4-5.7.4s-4.4.1-5.7-.4a3.3 3.3 0 0 1-1.8-1.8c-.5-1.3-.4-4.3-.4-5.7s-.1-4.4.4-5.7A3.3 3.3 0 0 1 6.6 4.1c1.3-.5 4.3-.4 5.7-.4s4.4-.1 5.7.4a3.3 3.3 0 0 1 1.8 1.8c.5 1.3.4 4.3.4 5.7s.1 4.4-.4 5.7z"/></svg>',
              'linkedin' => 'in',
              'facebook' => 'f',
              'youtube' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12a31 31 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8zM9.7 15.1V8.9l5.8 3.1-5.8 3.1z"/></svg>',
          ])
          @if ($socials)
            <div class="footer-socials">
              @foreach ($socials as $network => $url)
                <a href="{{ $url }}" class="footer-social" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}">{!! $socialIcons[$network] ?? e(strtoupper(substr($network, 0, 2))) !!}</a>
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
