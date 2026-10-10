@extends('layout.app')

@section('title', 'Tech Marketplace')
@section('description', 'Belanja komputer, laptop, storage, software, dan aksesoris IT dari Inter G Queen Bumindo, Malang. Masukkan ke keranjang dan pesan langsung via WhatsApp.')
@section('body_class', 'page-marketplace')

@php
  $statusLabels = ['baru' => 'Baru', 'bekas' => 'Bekas', 'digital' => 'Digital License'];
  $waNumber = $site['mp_whatsapp_number'];
  $greeting = config('company.marketplace_greeting');
  $waIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.6-.8-1.8-.9-.2-.1-.4-.1-.6.1-.2.2-.6.9-.8 1.1-.1.2-.3.2-.5.1-.7-.3-1.4-.7-2-1.3-.6-.6-1.1-1.3-1.5-2-.1-.2 0-.4.1-.5.3-.3.6-.6.8-.9.1-.2.1-.4 0-.6-.1-.2-.7-1.6-.9-2.1-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.7.7-1 1.6-1 2.6.1 1.2.6 2.4 1.4 3.5 1.6 2.2 3.5 3.7 6 4.5.6.2 1.1.3 1.6.2.7-.1 1.6-.6 1.8-1.3.3-.6.3-1.2.2-1.3-.1-.1-.3-.2-.5-.3z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.4c1.4.8 3.1 1.2 4.8 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.3c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.2.8.9-3.1-.2-.3C4 14.9 3.6 13.5 3.6 12c0-4.6 3.8-8.4 8.4-8.4s8.4 3.8 8.4 8.4-3.8 8.3-8.4 8.3z"/></svg>';
  $cartIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7.2 14.6l.1-.1L8.1 13h7.4c.8 0 1.4-.4 1.7-1l3.6-6.5A1 1 0 0 0 20 4H5.2l-.9-2H1v2h2l3.6 7.6-1.4 2.4A2 2 0 0 0 7 17h12v-2H7.4c-.1 0-.2-.1-.2-.2z"/></svg>';
  $chatText = 'Halo IG&B, saya ingin bertanya tentang produk di marketplace.';
  $chatHref = $waNumber
      ? 'https://wa.me/'.$waNumber.'?text='.rawurlencode($chatText)
      : 'mailto:'.$site['email'].'?subject='.rawurlencode('Pertanyaan Marketplace');
@endphp

@section('content')

<section id="marketplace" class="section section--tint section--page">
  <div class="container">

    <div class="section-head section-head--center reveal">
      <div class="eyebrow">Tech Marketplace</div>
      <h1 class="section-title">Tech <em>Marketplace</em></h1>
      <p class="section-sub">Komputer, hardware, software, dan aksesoris IT pilihan untuk kebutuhan personal maupun bisnis. Pilih produk, masukkan ke keranjang, lalu kirim pesanan langsung ke tim kami melalui WhatsApp.</p>
    </div>

    <ol class="mp-steps reveal" aria-label="Cara memesan">
      <li><span>1</span><div><strong>Pilih produk</strong>Cari dan lihat detail spesifikasi.</div></li>
      <li><span>2</span><div><strong>Masukkan keranjang</strong>Atur jumlah setiap produk.</div></li>
      <li><span>3</span><div><strong>Pesan via {{ $waNumber ? 'WhatsApp' : 'Email' }}</strong>Admin mengonfirmasi stok, ongkir &amp; pembayaran.</div></li>
    </ol>

    @if ($products->isEmpty())
      <p class="empty-state">Belum ada produk yang ditampilkan. <a href="{{ $chatHref }}" target="_blank" rel="noopener" class="link-arrow" style="display:inline">Tanyakan kebutuhan perangkat Anda</a></p>
    @else
      <div class="mp-toolbar mp-toolbar--sticky" role="search">
        <div class="mp-search">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14z"/></svg>
          <label for="mpSearch" class="sr-only">Cari produk</label>
          <input type="search" id="mpSearch" placeholder="Cari produk: laptop, SSD, lisensi Windows..." autocomplete="off">
        </div>
        <label for="mpCategory" class="sr-only">Kategori</label>
        <select class="mp-select" id="mpCategory">
          <option value="all">Semua Kategori</option>
          @foreach ($categories as $category)
            <option value="{{ $category->slug }}">{{ $category->name }}</option>
          @endforeach
        </select>
        <label for="mpSort" class="sr-only">Urutkan</label>
        <select class="mp-select" id="mpSort">
          <option value="newest">Terbaru</option>
          <option value="cheapest">Harga Termurah</option>
          <option value="priciest">Harga Tertinggi</option>
        </select>
        <button type="button" class="mp-cart-btn" id="cartOpen" aria-controls="cartDrawer" aria-expanded="false">
          {!! $cartIcon !!}
          <span>Keranjang</span>
          <span class="mp-cart-count" id="cartCount" aria-label="0 item">0</span>
        </button>
      </div>

      <p class="mp-results-count" aria-live="polite"><strong id="mpCount">{{ $products->count() }}</strong> produk ditemukan</p>

      <div class="product-grid" id="mpGrid">
        @foreach ($products as $index => $p)
          @php($soldOut = $p['stock'] < 1)
          <article class="product-card"
                   data-product-id="{{ $p['id'] }}"
                   data-cat="{{ $p['cat'] }}"
                   data-price="{{ $p['price'] }}"
                   data-order="{{ $index }}"
                   data-search="{{ \Illuminate\Support\Str::lower($p['name'].' '.$p['catLabel'].' '.$p['brand']) }}">
            <button type="button" class="product-thumb" data-open-product="{{ $p['id'] }}" aria-label="Lihat detail {{ $p['name'] }}">
              @if ($p['image'])
                <img src="{{ $p['image'] }}" alt="" loading="lazy" decoding="async">
              @else
                <span class="product-thumb-empty"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M2 20h20" stroke-linecap="round"/></svg></span>
              @endif
              <span class="badge-status status-{{ $p['status'] }}">{{ $statusLabels[$p['status']] ?? ucfirst($p['status']) }}</span>
            </button>
            <div class="product-body">
              <span class="badge-cat cat-{{ $p['cat'] }}">{{ $p['catLabel'] }}</span>
              <h3><button type="button" class="product-name" data-open-product="{{ $p['id'] }}">{{ $p['name'] }}</button></h3>
              @if ($p['brand'])
                <div class="product-meta">{{ $p['brand'] }}</div>
              @endif
              <div class="product-price">Rp {{ number_format($p['price'], 0, ',', '.') }}</div>
              <div class="product-stock {{ $soldOut ? 'is-out' : ($p['stock'] <= 2 ? 'is-low' : '') }}">
                {{ $soldOut ? 'Stok habis' : ($p['stock'] <= 2 ? 'Sisa '.$p['stock'] : 'Stok tersedia') }}
              </div>
              <div class="product-cta">
                <button type="button" class="btn-add" data-add-to-cart="{{ $p['id'] }}" @disabled($soldOut)>
                  {!! $cartIcon !!} {{ $soldOut ? 'Stok habis' : '+ Keranjang' }}
                </button>
                <button type="button" class="btn-detail" data-open-product="{{ $p['id'] }}">Detail</button>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      <p class="empty-state" id="mpEmpty" hidden>
        <strong>Produk tidak ditemukan.</strong><br>Coba kata kunci atau kategori lain.
      </p>

      <p class="mp-disclaimer">Harga dapat berubah sewaktu-waktu. Ketersediaan stok, ongkos kirim, dan metode pembayaran dikonfirmasi oleh admin saat memproses pesanan Anda.</p>

      {{-- Product detail dialog (filled by marketplace.js using textContent) --}}
      <dialog class="mp-modal" id="mpModal" aria-labelledby="mpModalTitle">
        <button type="button" class="mp-modal-close" data-close-modal aria-label="Tutup">✕</button>
        <div class="mp-modal-inner">
          <div class="mp-modal-visual" id="mpModalVisual"></div>
          <div class="mp-modal-content">
            <div class="mp-modal-badges">
              <span class="badge-cat" id="mpModalCat"></span>
              <span class="badge-status" id="mpModalStatus"></span>
            </div>
            <h3 class="mp-modal-title" id="mpModalTitle"></h3>
            <div class="mp-modal-price" id="mpModalPrice"></div>
            <div class="product-stock" id="mpModalStock"></div>
            <p class="mp-modal-desc" id="mpModalDesc"></p>
            <div class="spec-table" id="mpModalSpecs"></div>
            <p class="mp-note" id="mpModalNote" hidden>🔑 Lisensi dikirim melalui email setelah pembayaran dikonfirmasi oleh tim kami.</p>
            <div class="mp-modal-buy">
              <div class="qty" role="group" aria-label="Jumlah">
                <button type="button" data-qty-step="-1" aria-label="Kurangi">−</button>
                <input type="number" id="mpModalQty" value="1" min="1" inputmode="numeric" aria-label="Jumlah">
                <button type="button" data-qty-step="1" aria-label="Tambah">+</button>
              </div>
              <button type="button" class="btn-add" id="mpModalAdd">{!! $cartIcon !!} Tambah ke Keranjang</button>
            </div>
          </div>
        </div>
      </dialog>

      {{-- Cart drawer --}}
      <aside class="cart-drawer" id="cartDrawer" aria-label="Keranjang belanja" aria-hidden="true" inert>
        <div class="cart-head">
          <h2>Keranjang <span id="cartHeadCount"></span></h2>
          <button type="button" class="ig-close" data-cart-close aria-label="Tutup keranjang">✕</button>
        </div>
        <div class="cart-body">
          <p class="cart-empty" id="cartEmpty">Keranjang masih kosong.<br>Tambahkan produk untuk mulai memesan.</p>
          <ul class="cart-list" id="cartList"></ul>
        </div>
        <div class="cart-foot" id="cartFoot" hidden>
          <div class="cart-total"><span>Total</span><strong id="cartTotal">Rp 0</strong></div>
          <label class="cart-field"><span class="cart-label">Nama <em>(opsional)</em></span>
            <input type="text" id="cartName" maxlength="80" autocomplete="name" placeholder="Nama Anda / instansi">
          </label>
          <label class="cart-field"><span class="cart-label">Catatan <em>(opsional)</em></span>
            <textarea id="cartNote" rows="2" maxlength="300" placeholder="Kota pengiriman, kebutuhan faktur, dsb."></textarea>
          </label>
          <a href="#" class="btn-wa cart-checkout" id="cartCheckout" target="_blank" rel="noopener">
            {!! $waNumber ? $waIcon.' Pesan via WhatsApp' : 'Pesan via Email' !!}
          </a>
          <button type="button" class="cart-clear" id="cartClear">Kosongkan keranjang</button>
        </div>
      </aside>
      <div class="ig-scrim" id="cartScrim" hidden></div>

      <div class="mp-toast" id="mpToast" role="status" aria-live="polite"></div>

      @php($mpData = ['products' => $products, 'statusLabels' => $statusLabels, 'whatsapp' => $waNumber, 'email' => $site['email']])
      {{-- @json escapes <, >, &, ' and " so the payload cannot break out of the script tag. --}}
      <script type="application/json" id="mpData">@json($mpData)</script>
    @endif
  </div>
</section>

{{-- Floating chat with greeting --}}
<div class="wa-float" id="waFloat">
  <div class="wa-bubble" id="waBubble" hidden>
    <button type="button" class="wa-bubble-close" id="waBubbleClose" aria-label="Tutup sapaan">✕</button>
    <div class="wa-bubble-who"><img src="{{ asset('images/logo-igb.png') }}" alt="" width="70" height="16"> <span>Tim Marketplace</span></div>
    <p class="wa-bubble-text">{{ $greeting }}</p>
    <a href="{{ $chatHref }}" target="_blank" rel="noopener" class="wa-bubble-cta">Chat {{ $waNumber ? 'via WhatsApp' : 'via Email' }} →</a>
  </div>
  <a href="{{ $chatHref }}" target="_blank" rel="noopener" class="wa-fab" aria-label="{{ $greeting }} Chat {{ $waNumber ? 'WhatsApp' : 'email' }} dengan tim marketplace">
    {!! $waNumber ? $waIcon : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>' !!}
  </a>
</div>

@endsection

@push('scripts')
  <script src="{{ asset('js/marketplace.js') }}?v={{ @filemtime(public_path('js/marketplace.js')) ?: '1' }}" defer></script>
@endpush
