/* IGB Marketplace — filters, product dialog, cart, WhatsApp checkout.
   All product text is written with textContent; prices always come from the
   server payload (the cart in localStorage stores only ids and quantities). */
(function () {
  'use strict';

  var dataEl = document.getElementById('mpData');
  var grid = document.getElementById('mpGrid');

  /* ---------- Greeting bubble (shown once per browser session) ---------- */
  var bubble = document.getElementById('waBubble');
  var bubbleClose = document.getElementById('waBubbleClose');
  var store = {
    get: function (k, s) { try { return (s ? sessionStorage : localStorage).getItem(k); } catch (e) { return null; } },
    set: function (k, v, s) { try { (s ? sessionStorage : localStorage).setItem(k, v); } catch (e) { /* storage blocked */ } }
  };
  if (bubble && !store.get('igb_mp_greeted', true)) {
    setTimeout(function () { bubble.hidden = false; }, 2500);
  }
  if (bubbleClose) {
    bubbleClose.addEventListener('click', function () {
      bubble.hidden = true;
      store.set('igb_mp_greeted', '1', true);
    });
  }

  if (!dataEl || !grid) return;

  var data;
  try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }

  var products = {};
  data.products.forEach(function (p) { products[p.id] = p; });

  var rupiah = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };
  var maxQty = function (p) { return Math.max(0, Math.min(p.stock, 99)); };

  /* ---------- Toast ---------- */
  var toastEl = document.getElementById('mpToast');
  var toastTimer;
  function toast(msg) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove('show'); }, 2200);
  }

  /* ---------- Filters ---------- */
  var cards = Array.prototype.slice.call(grid.querySelectorAll('.product-card'));
  var searchInput = document.getElementById('mpSearch');
  var categorySelect = document.getElementById('mpCategory');
  var sortSelect = document.getElementById('mpSort');
  var countEl = document.getElementById('mpCount');
  var emptyEl = document.getElementById('mpEmpty');

  function applyFilters() {
    var q = (searchInput.value || '').trim().toLowerCase();
    var cat = categorySelect.value;
    var sort = sortSelect.value;
    var sorted = cards.slice().sort(function (a, b) {
      if (sort === 'cheapest') return a.dataset.price - b.dataset.price;
      if (sort === 'priciest') return b.dataset.price - a.dataset.price;
      return a.dataset.order - b.dataset.order;
    });
    var visible = 0;
    sorted.forEach(function (card) {
      var match = (cat === 'all' || card.dataset.cat === cat) && (q === '' || card.dataset.search.indexOf(q) !== -1);
      card.hidden = !match;
      if (match) visible++;
      grid.appendChild(card);
    });
    countEl.textContent = visible;
    emptyEl.hidden = visible !== 0;
  }
  var debounce;
  searchInput.addEventListener('input', function () { clearTimeout(debounce); debounce = setTimeout(applyFilters, 120); });
  categorySelect.addEventListener('change', applyFilters);
  sortSelect.addEventListener('change', applyFilters);

  /* ---------- Cart state ---------- */
  var CART_KEY = 'igb_cart_v1';
  var cart = []; // [{id, qty}]

  (function loadCart() {
    var raw = store.get(CART_KEY);
    var saved;
    try { saved = JSON.parse(raw || '[]'); } catch (e) { saved = []; }
    if (!Array.isArray(saved)) saved = [];
    saved.forEach(function (it) {
      var p = products[it && it.id];
      var limit = p ? maxQty(p) : 0;
      if (limit > 0) cart.push({ id: p.id, qty: Math.max(1, Math.min(parseInt(it.qty, 10) || 1, limit)) });
    });
  })();

  function saveCart() { store.set(CART_KEY, JSON.stringify(cart)); }
  function findItem(id) { for (var i = 0; i < cart.length; i++) if (cart[i].id === id) return cart[i]; return null; }

  function addToCart(id, qty) {
    var p = products[id];
    if (!p) return;
    var limit = maxQty(p);
    if (limit < 1) { toast('Maaf, stok ' + p.name + ' sedang habis'); return; }
    var item = findItem(id);
    var current = item ? item.qty : 0;
    var next = Math.min(limit, current + (qty || 1));
    if (next === current) { toast('Jumlah maksimal sesuai stok tersedia (' + limit + ')'); return; }
    if (item) item.qty = next; else cart.push({ id: id, qty: next });
    saveCart();
    renderCart();
    toast('✓ ' + p.name + ' ditambahkan ke keranjang');
    bumpCount();
  }

  function setQty(id, qty) {
    var p = products[id];
    var item = findItem(id);
    if (!p || !item) return;
    if (qty < 1) { removeItem(id); return; }
    item.qty = Math.min(qty, maxQty(p));
    saveCart();
    renderCart();
  }

  function removeItem(id) {
    cart = cart.filter(function (i) { return i.id !== id; });
    saveCart();
    renderCart();
  }

  /* ---------- Cart UI ---------- */
  var cartBtn = document.getElementById('cartOpen');
  var cartCount = document.getElementById('cartCount');
  var drawer = document.getElementById('cartDrawer');
  var scrim = document.getElementById('cartScrim');
  var list = document.getElementById('cartList');
  var emptyCart = document.getElementById('cartEmpty');
  var foot = document.getElementById('cartFoot');
  var totalEl = document.getElementById('cartTotal');
  var headCount = document.getElementById('cartHeadCount');
  var checkout = document.getElementById('cartCheckout');
  var nameInput = document.getElementById('cartName');
  var noteInput = document.getElementById('cartNote');

  function totals() {
    var qty = 0, sum = 0;
    cart.forEach(function (i) { var p = products[i.id]; qty += i.qty; sum += i.qty * p.price; });
    return { qty: qty, sum: sum };
  }

  function bumpCount() {
    cartBtn.classList.remove('bump');
    void cartBtn.offsetWidth;
    cartBtn.classList.add('bump');
  }

  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text != null) n.textContent = text;
    return n;
  }

  function renderCart() {
    var t = totals();
    cartCount.textContent = t.qty;
    cartCount.setAttribute('aria-label', t.qty + ' item');
    cartCount.hidden = t.qty === 0;
    headCount.textContent = t.qty ? '(' + t.qty + ')' : '';
    emptyCart.hidden = cart.length > 0;
    foot.hidden = cart.length === 0;
    totalEl.textContent = rupiah(t.sum);

    list.replaceChildren();
    cart.forEach(function (item) {
      var p = products[item.id];
      var li = el('li', 'cart-item');

      var thumb = el('div', 'cart-thumb');
      if (p.image) { var img = el('img'); img.src = p.image; img.alt = ''; thumb.appendChild(img); }
      li.appendChild(thumb);

      var info = el('div', 'cart-info');
      info.appendChild(el('div', 'cart-name', p.name));
      info.appendChild(el('div', 'cart-price', rupiah(p.price)));

      var row = el('div', 'cart-row');
      var qty = el('div', 'qty qty--sm');
      qty.setAttribute('role', 'group');
      qty.setAttribute('aria-label', 'Jumlah ' + p.name);
      var minus = el('button', null, '−'); minus.type = 'button'; minus.setAttribute('aria-label', 'Kurangi');
      var input = el('input'); input.type = 'number'; input.min = '1'; input.max = String(maxQty(p)); input.value = item.qty; input.setAttribute('aria-label', 'Jumlah');
      var plus = el('button', null, '+'); plus.type = 'button'; plus.setAttribute('aria-label', 'Tambah');
      plus.disabled = item.qty >= maxQty(p);
      minus.addEventListener('click', function () { setQty(p.id, item.qty - 1); });
      plus.addEventListener('click', function () { setQty(p.id, item.qty + 1); });
      input.addEventListener('change', function () { setQty(p.id, parseInt(input.value, 10) || 0); });
      qty.append(minus, input, plus);
      row.appendChild(qty);
      row.appendChild(el('div', 'cart-sub', rupiah(p.price * item.qty)));
      info.appendChild(row);
      li.appendChild(info);

      var del = el('button', 'cart-del', '✕');
      del.type = 'button';
      del.setAttribute('aria-label', 'Hapus ' + p.name);
      del.addEventListener('click', function () { removeItem(p.id); });
      li.appendChild(del);

      list.appendChild(li);
    });

    updateCheckoutLink();
  }

  function orderMessage() {
    var lines = ['Halo IG&B, saya ingin memesan produk berikut:', ''];
    cart.forEach(function (item, i) {
      var p = products[item.id];
      lines.push((i + 1) + '. ' + p.name);
      lines.push('   ' + item.qty + ' x ' + rupiah(p.price) + ' = ' + rupiah(item.qty * p.price));
    });
    lines.push('', 'Total: ' + rupiah(totals().sum));
    var name = (nameInput.value || '').trim();
    var note = (noteInput.value || '').trim();
    if (name) lines.push('Nama: ' + name);
    if (note) lines.push('Catatan: ' + note);
    lines.push('', 'Mohon info ketersediaan stok, ongkos kirim, dan cara pembayarannya. Terima kasih.');
    return lines.join('\n');
  }

  function updateCheckoutLink() {
    if (!cart.length) return;
    var msg = orderMessage();
    checkout.href = data.whatsapp
      ? 'https://wa.me/' + data.whatsapp + '?text=' + encodeURIComponent(msg)
      : 'mailto:' + data.email + '?subject=' + encodeURIComponent('Pesanan Marketplace') + '&body=' + encodeURIComponent(msg);
  }
  nameInput.addEventListener('input', updateCheckoutLink);
  noteInput.addEventListener('input', updateCheckoutLink);

  document.getElementById('cartClear').addEventListener('click', function () {
    cart = [];
    saveCart();
    renderCart();
  });

  function setDrawer(open) {
    drawer.classList.toggle('open', open);
    drawer.setAttribute('aria-hidden', String(!open));
    drawer.inert = !open;
    cartBtn.setAttribute('aria-expanded', String(open));
    scrim.hidden = !open;
    document.body.classList.toggle('modal-open', open);
    if (open) {
      requestAnimationFrame(function () {
        var c = drawer.querySelector('[data-cart-close]');
        if (c) c.focus({ preventScroll: true });
      });
    } else {
      cartBtn.focus({ preventScroll: true });
    }
  }
  cartBtn.addEventListener('click', function () { setDrawer(true); });
  scrim.addEventListener('click', function () { setDrawer(false); });
  drawer.querySelector('[data-cart-close]').addEventListener('click', function () { setDrawer(false); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && drawer.classList.contains('open')) setDrawer(false);
  });

  /* ---------- Product dialog ---------- */
  var modal = document.getElementById('mpModal');
  var modalQty = document.getElementById('mpModalQty');
  var modalAdd = document.getElementById('mpModalAdd');
  var openId = null;

  function stockLabel(p) {
    if (p.stock < 1) return { text: 'Stok habis', cls: 'is-out' };
    if (p.stock <= 2) return { text: 'Sisa ' + p.stock, cls: 'is-low' };
    return { text: 'Stok tersedia', cls: '' };
  }

  function openProduct(id) {
    var p = products[id];
    if (!p || !modal) return;
    openId = id;

    var visual = document.getElementById('mpModalVisual');
    visual.replaceChildren();
    if (p.image) {
      var img = el('img'); img.src = p.image; img.alt = p.name; visual.appendChild(img);
    } else {
      var ph = grid.querySelector('[data-product-id="' + p.id + '"] .product-thumb-empty');
      if (ph) visual.appendChild(ph.cloneNode(true));
    }

    var catEl = document.getElementById('mpModalCat');
    catEl.textContent = p.catLabel; catEl.className = 'badge-cat cat-' + p.cat;
    var statusEl = document.getElementById('mpModalStatus');
    statusEl.textContent = data.statusLabels[p.status] || p.status; statusEl.className = 'badge-status status-' + p.status;

    document.getElementById('mpModalTitle').textContent = p.name;
    document.getElementById('mpModalPrice').textContent = rupiah(p.price);
    var s = stockLabel(p);
    var stockEl = document.getElementById('mpModalStock');
    stockEl.textContent = s.text; stockEl.className = 'product-stock ' + s.cls;
    document.getElementById('mpModalDesc').textContent = p.desc || '';

    var specsEl = document.getElementById('mpModalSpecs');
    specsEl.replaceChildren();
    var specs = Object.assign({}, p.brand ? { Brand: p.brand } : {}, p.specs || {});
    Object.keys(specs).forEach(function (key) {
      var item = el('div', 'spec-item');
      item.append(el('div', 'spec-label', key), el('div', 'spec-value', specs[key]));
      specsEl.appendChild(item);
    });
    specsEl.hidden = !specsEl.children.length;
    document.getElementById('mpModalNote').hidden = p.status !== 'digital';

    var limit = maxQty(p);
    modalQty.max = String(Math.max(1, limit));
    modalQty.value = '1';
    modalQty.disabled = limit < 1;
    modalAdd.disabled = limit < 1;
    modalAdd.lastChild.textContent = limit < 1 ? ' Stok habis' : ' Tambah ke Keranjang';

    if (typeof modal.showModal === 'function') {
      modal.showModal();
      document.body.classList.add('modal-open');
    }
  }

  modal.querySelectorAll('[data-qty-step]').forEach(function (b) {
    b.addEventListener('click', function () {
      var p = products[openId];
      if (!p) return;
      var v = (parseInt(modalQty.value, 10) || 1) + Number(b.getAttribute('data-qty-step'));
      modalQty.value = String(Math.max(1, Math.min(v, Math.max(1, maxQty(p)))));
    });
  });
  modalAdd.addEventListener('click', function () {
    addToCart(openId, Math.max(1, parseInt(modalQty.value, 10) || 1));
    modal.close();
  });
  modal.addEventListener('close', function () { document.body.classList.remove('modal-open'); });
  modal.querySelector('[data-close-modal]').addEventListener('click', function () { modal.close(); });
  modal.addEventListener('click', function (e) { if (e.target === modal) modal.close(); });

  grid.addEventListener('click', function (e) {
    var add = e.target.closest('[data-add-to-cart]');
    if (add) { addToCart(Number(add.getAttribute('data-add-to-cart')), 1); return; }
    var open = e.target.closest('[data-open-product]');
    if (open) openProduct(Number(open.getAttribute('data-open-product')));
  });

  renderCart();
})();
