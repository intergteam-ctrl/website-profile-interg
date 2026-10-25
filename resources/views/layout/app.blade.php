@php
  $L = \App\Support\Locale::class;
  $onHome = $L::is('home');
  $section = fn (string $id) => $onHome ? '#'.$id : lroute('home').'#'.$id;

  $navItems = [
      ['label' => __('Beranda'),     'href' => $section('hero')],
      ['label' => __('Tentang'),     'href' => $section('about')],
      ['label' => __('Layanan'),     'href' => $section('services')],
      ['label' => __('Proyek'),      'href' => $section('display')],
      ['label' => __('Blog'),        'href' => $onHome ? '#blog' : lroute('blog'), 'active' => $L::is('blog', 'blog.show')],
      ['label' => __('Kontak'),      'href' => $section('contact')],
      ['label' => __('Marketplace'), 'href' => lroute('marketplace'), 'active' => $L::is('marketplace'), 'side' => true,
       'desc' => __('Laptop, hardware, software & aksesoris IT'), 'icon' => 'M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7.2 14.6l.1-.1L8.1 13h7.4c.8 0 1.4-.4 1.7-1l3.6-6.5A1 1 0 0 0 20 4H5.2l-.9-2H1v2h2l3.6 7.6-1.4 2.4A2 2 0 0 0 7 17h12v-2H7.4c-.1 0-.2-.1-.2-.2z'],
      ['label' => __('Download'),    'href' => lroute('downloads'), 'active' => $L::is('downloads'), 'side' => true,
       'desc' => __('Company profile, brosur & panduan'), 'icon' => 'M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z'],
      ['label' => 'InterG Studio', 'image' => asset('images/interg-studio.png'),       'href' => lroute('games'), 'active' => $L::is('games', 'games.show', 'games.file'), 'side' => true,
       'desc' => __('Game & aplikasi mobile kami di Google Play'), 'icon' => 'M21 6H3a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm4-3a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z'],
  ];
  // Top bar keeps the page sections; extra pages live in the side menu.
  $topItems = array_filter($navItems, fn ($i) => empty($i['side']));
  $sideItems = array_filter($navItems, fn ($i) => ! empty($i['side']));
  $sideActive = (bool) array_filter($sideItems, fn ($i) => $i['active'] ?? false);

  $assetVersion = fn (string $path) => asset($path).'?v='.(@filemtime(public_path($path)) ?: '1');
  $pageTitle = trim($__env->yieldContent('title'));
  $fullTitle = $pageTitle
      ? $pageTitle.' — '.config('company.short_name').' '.config('company.name')
      : config('company.short_name').' — '.config('company.name').' | '.config('company.tagline');
  $metaDescription = trim($__env->yieldContent('description')) ?: __('Inter G Queen Bumindo (IGB) — Total IT Solution Provider di Malang: system integrator, software development, IoT, display solution, dan IT consulting untuk pemerintahan dan perusahaan.');
@endphp
<!DOCTYPE html>
<html lang="{{ $L::htmlLang() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $fullTitle }}</title>
  <meta name="description" content="{{ $metaDescription }}">
  <meta name="theme-color" content="#071528">
  <link rel="canonical" href="{{ url()->current() }}">
  @foreach (array_keys($L::SUPPORTED) as $code)
    <link rel="alternate" hreflang="{{ $L::SUPPORTED[$code]['html'] }}" href="{{ $L::switchUrl($code) }}">
  @endforeach
  <link rel="alternate" hreflang="x-default" href="{{ $L::switchUrl($L::DEFAULT) }}">

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
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ $assetVersion('css/site.css') }}">
  <script>document.documentElement.classList.add('js');</script>
</head>
<body class="@yield('body_class')">
  <a href="#main" class="skip-link">{{ __('Lewati ke konten') }}</a>

  {{-- Mobile navigation --}}
  <nav class="mobile-nav" id="mobileNav" aria-label="{{ __('Menu utama') }}" aria-hidden="true">
    <button type="button" class="mobile-close" data-mobile-nav-close aria-label="{{ __('Tutup menu') }}">✕</button>
    @foreach ($navItems as $item)
      <a href="{{ $item['href'] }}" data-mobile-nav-close>{{ $item['label'] }}</a>
    @endforeach
    @include('partials.lang-switch', ['class' => 'lang-switch lang-switch--mobile'])
    <a href="{{ $section('contact') }}" class="btn btn-red" data-mobile-nav-close>{{ __('Konsultasi Gratis') }}</a>
  </nav>

  {{-- Navbar --}}
  <header id="navbar" class="{{ $onHome ? '' : 'solid' }}">
    <div class="container nav-inner">
      <a href="{{ $section('hero') }}" class="brand" aria-label="{{ config('company.name') }} — beranda">
        <img src="{{ asset('images/logo-igb.png') }}" alt="IG&amp;B — Accelerating Innovation and Technology" width="176" height="40">
      </a>

      <ul class="nav-links">
        @foreach ($topItems as $item)
          <li><a href="{{ $item['href'] }}" @class(['active' => $item['active'] ?? false])>{{ $item['label'] }}</a></li>
        @endforeach
        <li>
          <button type="button" @class(['nav-more', 'active' => $sideActive]) id="moreTab" aria-controls="moreNav" aria-expanded="false">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z"/></svg>
            {{ __('Menu: Lainnya') }}
          </button>
        </li>
      </ul>

      @include('partials.lang-switch', ['class' => 'lang-switch'])

      <a href="{{ $section('contact') }}" class="btn btn-red nav-cta">{{ __('Konsultasi Gratis') }}</a>

      <button type="button" class="hamburger" id="hamburger" aria-label="{{ __('Buka menu') }}" aria-controls="mobileNav" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <main id="main">
    @yield('content')
  </main>

  {{-- Side menu: Marketplace, Download, Games --}}
  <aside class="more-drawer" id="moreNav" aria-label="{{ __('Menu lainnya') }}" aria-hidden="true" inert>
    <div class="more-head">
      <h2>{{ __('Menu lainnya') }}</h2>
      <button type="button" class="ig-close" data-more-close aria-label="{{ __('Tutup menu lainnya') }}">✕</button>
    </div>
    <ul class="more-list">
      @foreach ($sideItems as $item)
        <li>
          <a href="{{ $item['href'] }}" @class(['more-item', 'active' => $item['active'] ?? false])>
            @if (! empty($item['image']))
              <span class="more-icon more-icon--img" aria-hidden="true"><img src="{{ $item['image'] }}" alt="" width="42" height="42"></span>
            @else
              <span class="more-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg></span>
            @endif
            <span class="more-text"><strong>{{ $item['label'] }}</strong><span>{{ $item['desc'] }}</span></span>
          </a>
        </li>
      @endforeach
    </ul>
    <a href="{{ $section('contact') }}" class="btn btn-red more-cta" data-more-close>{{ __('Konsultasi Gratis') }}</a>
  </aside>
  <div class="more-scrim" id="moreScrim" hidden></div>

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
            {{ config('company.name') }} — {{ __('Total IT Solution untuk pemerintahan dan perusahaan: system integrator, software, IoT, surveillance, dan display solution.') }}
          </p>
          @php($socials = \App\Support\Socials::links())
          @php($otherLinks = \App\Support\Socials::others())
          @if ($socials || $otherLinks)
            <div class="footer-socials">
              @foreach ($socials as $network => $url)
                <a href="{{ $url }}" class="footer-social" target="_blank" rel="noopener" aria-label="{{ \App\Support\Socials::name($network) }}" title="{{ \App\Support\Socials::name($network) }}">{!! \App\Support\Socials::icon($network) !!}</a>
              @endforeach
              @foreach ($otherLinks as $link)
                <a href="{{ $link['url'] }}" class="footer-social" target="_blank" rel="noopener" aria-label="{{ $link['label'] }}" title="{{ $link['label'] }}">{!! $link['icon'] !!}</a>
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <div class="footer-col-title">{{ __('Tautan') }}</div>
          <ul class="footer-links">
            @foreach ($navItems as $item)
              <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
            @endforeach
          </ul>
        </div>

        <div>
          <div class="footer-col-title">{{ __('Layanan') }}</div>
          <ul class="footer-links">
            @foreach (config('company.services') as $service)
              <li><a href="{{ $section($service['anchor'] === 'contact' ? 'services' : $service['anchor']) }}">{{ __($service['title']) }}</a></li>
            @endforeach
          </ul>
        </div>

        <div>
          <div class="footer-col-title">{{ __('Kontak') }}</div>
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
        <div>© {{ now()->year }} <span>{{ config('company.name') }}</span>. {{ __('Hak cipta dilindungi.') }}</div>
        <div class="footer-tagline">{{ config('company.tagline') }}</div>
      </div>
    </div>
  </footer>

  {{-- Strings used by site.js / marketplace.js, in the page language. --}}
  @php($jsStrings = collect(['Mengirim...', 'Maaf, stok :name sedang habis', 'Jumlah maksimal sesuai stok tersedia (:n)', ':name ditambahkan ke keranjang', 'Jumlah', 'Kurangi', 'Tambah', 'Hapus', 'Stok habis', 'Sisa :n', 'Stok tersedia', 'Tambah ke Keranjang', 'Video belum bisa dimuat di sini (mungkin diblokir browser atau koneksi). Buka langsung di TikTok:'])->mapWithKeys(fn ($k) => [$k => __($k)]))
  <script>window.IGB_T = @json($jsStrings);</script>
  <script src="{{ $assetVersion('js/site.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
