{{--
  Marketplace block: toolbar, server-rendered product grid, detail dialog.
  Cards are rendered in Blade (escaped, crawlable, works without JS);
  public/js/site.js only filters/sorts them and fills the dialog using textContent.

  Expects: $products (Collection of arrays), $categories (Collection of Category), $site.
  Optional: $heading ('h1' | 'h2').
--}}
@php
  $heading = $heading ?? 'h2';
  $statusLabels = ['baru' => 'Baru', 'bekas' => 'Bekas', 'digital' => 'Digital License'];
  $waIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.6-.8-1.8-.9-.2-.1-.4-.1-.6.1-.2.2-.6.9-.8 1.1-.1.2-.3.2-.5.1-.7-.3-1.4-.7-2-1.3-.6-.6-1.1-1.3-1.5-2-.1-.2 0-.4.1-.5.3-.3.6-.6.8-.9.1-.2.1-.4 0-.6-.1-.2-.7-1.6-.9-2.1-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.7.7-1 1.6-1 2.6.1 1.2.6 2.4 1.4 3.5 1.6 2.2 3.5 3.7 6 4.5.6.2 1.1.3 1.6.2.7-.1 1.6-.6 1.8-1.3.3-.6.3-1.2.2-1.3-.1-.1-.3-.2-.5-.3z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.4c1.4.8 3.1 1.2 4.8 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.3c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.2.8.9-3.1-.2-.3C4 14.9 3.6 13.5 3.6 12c0-4.6 3.8-8.4 8.4-8.4s8.4 3.8 8.4 8.4-3.8 8.3-8.4 8.3z"/></svg>';
  $mailIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>';
  $waLink = fn (string $name) => $site['whatsapp_number']
      ? 'https://wa.me/'.$site['whatsapp_number'].'?text='.rawurlencode('Halo, saya tertarik dengan produk: '.$name)
      : null;
  $mailLink = fn (string $name) => 'mailto:'.$site['email'].'?subject='.rawurlencode('Pesanan '.$name).'&body='.rawurlencode('Halo, saya ingin memesan produk: '.$name);
@endphp

<div class="section-head section-head--center reveal">
  <div class="eyebrow">Tech Marketplace</div>
  <{{ $heading }} class="section-title">Tech <em>Marketplace</em></{{ $heading }}>
  <p class="section-sub">Jual-beli komputer, hardware, dan software — produk teknologi terpercaya untuk kebutuhan personal maupun bisnis Anda.</p>
</div>

@if ($products->isEmpty())
  <p class="empty-state">Belum ada produk yang ditampilkan. Hubungi kami untuk kebutuhan perangkat Anda.</p>
@else
  <div class="mp-toolbar" role="search">
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
  </div>

  <p class="mp-results-count" aria-live="polite"><strong id="mpCount">{{ $products->count() }}</strong> produk ditemukan</p>

  <div class="product-grid" id="mpGrid">
    @foreach ($products as $index => $p)
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
          <div class="product-cta">
            <button type="button" class="btn-detail" data-open-product="{{ $p['id'] }}">Lihat Detail</button>
            <div class="mp-cta-row">
              @if ($wa = $waLink($p['name']))
                <a class="btn-wa" href="{{ $wa }}" target="_blank" rel="noopener">{!! $waIcon !!} WhatsApp</a>
              @endif
              <a class="btn-email" href="{{ $mailLink($p['name']) }}">{!! $mailIcon !!} Email</a>
            </div>
          </div>
        </div>
      </article>
    @endforeach
  </div>

  <p class="empty-state" id="mpEmpty" hidden>
    <strong>Produk tidak ditemukan.</strong><br>Coba kata kunci atau kategori lain.
  </p>

  {{-- Detail dialog (filled by site.js) --}}
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
        <p class="mp-modal-desc" id="mpModalDesc"></p>
        <div class="spec-table" id="mpModalSpecs"></div>
        <p class="mp-note" id="mpModalNote" hidden>🔑 Lisensi dikirim melalui email setelah pembayaran dikonfirmasi oleh tim kami.</p>
        <div class="mp-cta-row">
          <a class="btn-wa" id="mpModalWa" href="#" target="_blank" rel="noopener">{!! $waIcon !!} WhatsApp</a>
          <a class="btn-email" id="mpModalMail" href="#">{!! $mailIcon !!} Email</a>
        </div>
      </div>
    </div>
  </dialog>

  @php($mpData = ['products' => $products, 'statusLabels' => $statusLabels, 'whatsapp' => $site['whatsapp_number'], 'email' => $site['email']])
  {{-- @json escapes <, >, &, ' and " so the payload cannot break out of the script tag. --}}
  <script type="application/json" id="mpData">@json($mpData)</script>
@endif
