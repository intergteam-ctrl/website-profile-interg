/* IGB — frontend behaviour. Plain JS, no dependencies. */
(function () {
  'use strict';

  /* ---------- Navbar: solid background after scrolling ---------- */
  var navbar = document.getElementById('navbar');
  function onScroll() {
    if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 40);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Mobile navigation ---------- */
  var mobileNav = document.getElementById('mobileNav');
  var hamburger = document.getElementById('hamburger');

  function setMobileNav(open) {
    if (!mobileNav || !hamburger) return;
    mobileNav.classList.toggle('open', open);
    mobileNav.setAttribute('aria-hidden', String(!open));
    hamburger.setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('modal-open', open);
    if (open) {
      var first = mobileNav.querySelector('a, button');
      if (first) first.focus();
    } else {
      hamburger.focus({ preventScroll: true });
    }
  }

  if (hamburger) hamburger.addEventListener('click', function () { setMobileNav(true); });
  document.querySelectorAll('[data-mobile-nav-close]').forEach(function (el) {
    el.addEventListener('click', function () { setMobileNav(false); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileNav && mobileNav.classList.contains('open')) setMobileNav(false);
  });

  /* ---------- Instagram side panel ---------- */
  var igTab = document.getElementById('igTab');
  var igDrawer = document.getElementById('igDrawer');
  var igScrim = document.getElementById('igScrim');

  function setInstagram(open) {
    if (!igTab || !igDrawer) return;
    igDrawer.classList.toggle('open', open);
    igDrawer.setAttribute('aria-hidden', String(!open));
    igDrawer.inert = !open;
    igTab.setAttribute('aria-expanded', String(open));
    if (igScrim) igScrim.hidden = !open;
    if (open) {
      // Wait a frame so the panel is no longer inert/hidden when focusing.
      requestAnimationFrame(function () {
        var close = igDrawer.querySelector('[data-ig-close]');
        if (close) close.focus({ preventScroll: true });
      });
    } else {
      igTab.focus({ preventScroll: true });
    }
  }

  if (igTab) igTab.addEventListener('click', function () { setInstagram(true); });
  if (igScrim) igScrim.addEventListener('click', function () { setInstagram(false); });
  document.querySelectorAll('[data-ig-close]').forEach(function (el) {
    el.addEventListener('click', function () { setInstagram(false); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && igDrawer && igDrawer.classList.contains('open')) setInstagram(false);
  });

  /* ---------- Reveal on scroll ---------- */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var siblings = Array.prototype.slice.call(entry.target.parentElement.children);
        var delay = Math.min(siblings.indexOf(entry.target), 5) * 70;
        setTimeout(function () { entry.target.classList.add('visible'); }, delay);
        revealObserver.unobserve(entry.target);
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(function (el) { revealObserver.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('visible'); });
  }

  /* ---------- Scrollspy (home page only) ---------- */
  var navLinks = document.querySelectorAll('.nav-links a[href^="#"]');
  if (navLinks.length && 'IntersectionObserver' in window) {
    var sections = document.querySelectorAll('main section[id]');
    var setActive = function (id) {
      navLinks.forEach(function (a) { a.classList.toggle('active', a.getAttribute('href') === '#' + id); });
    };
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) { if (entry.isIntersecting) setActive(entry.target.id); });
    }, { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach(function (s) { spy.observe(s); });
  }

  /* ---------- Contact form ---------- */
  var contactForm = document.querySelector('[data-contact-form]');
  if (contactForm) {
    // After a server round-trip (success or validation error) bring the form into view.
    if (contactForm.querySelector('.alert') && !window.location.hash) {
      document.getElementById('contact').scrollIntoView();
    }

    // "Konsultasikan" links on service cards preselect the service.
    document.querySelectorAll('[data-service]').forEach(function (link) {
      link.addEventListener('click', function () {
        var select = contactForm.querySelector('select[name="service"]');
        if (select) select.value = link.getAttribute('data-service');
      });
    });

    // Prevent double submits.
    contactForm.addEventListener('submit', function () {
      var btn = contactForm.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.lastChild.textContent = ' Mengirim...';
      }
    });
  }

  /* ---------- Marketplace ---------- */
  var dataEl = document.getElementById('mpData');
  var grid = document.getElementById('mpGrid');
  if (!dataEl || !grid) return;

  var data;
  try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }

  var productsById = {};
  data.products.forEach(function (p) { productsById[p.id] = p; });

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
      var match = (cat === 'all' || card.dataset.cat === cat) &&
                  (q === '' || card.dataset.search.indexOf(q) !== -1);
      card.hidden = !match;
      if (match) visible++;
      grid.appendChild(card);
    });

    countEl.textContent = visible;
    emptyEl.hidden = visible !== 0;
  }

  var debounce;
  searchInput.addEventListener('input', function () {
    clearTimeout(debounce);
    debounce = setTimeout(applyFilters, 120);
  });
  categorySelect.addEventListener('change', applyFilters);
  sortSelect.addEventListener('change', applyFilters);

  /* Detail dialog — every value is written with textContent / attributes, never innerHTML. */
  var modal = document.getElementById('mpModal');
  var formatPrice = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };
  var waLink = function (name) {
    return data.whatsapp ? 'https://wa.me/' + data.whatsapp + '?text=' + encodeURIComponent('Halo, saya tertarik dengan produk: ' + name) : null;
  };
  var mailLink = function (name) {
    return 'mailto:' + data.email + '?subject=' + encodeURIComponent('Pesanan ' + name) +
      '&body=' + encodeURIComponent('Halo, saya ingin memesan produk: ' + name);
  };

  function openProduct(id) {
    var p = productsById[id];
    if (!p || !modal) return;

    var visual = document.getElementById('mpModalVisual');
    visual.replaceChildren();
    if (p.image) {
      var img = document.createElement('img');
      img.src = p.image;
      img.alt = p.name;
      visual.appendChild(img);
    } else {
      var placeholder = grid.querySelector('[data-product-id="' + p.id + '"] .product-thumb-empty');
      if (placeholder) visual.appendChild(placeholder.cloneNode(true));
    }

    var catEl = document.getElementById('mpModalCat');
    catEl.textContent = p.catLabel;
    catEl.className = 'badge-cat cat-' + p.cat;

    var statusEl = document.getElementById('mpModalStatus');
    statusEl.textContent = data.statusLabels[p.status] || p.status;
    statusEl.className = 'badge-status status-' + p.status;

    document.getElementById('mpModalTitle').textContent = p.name;
    document.getElementById('mpModalPrice').textContent = formatPrice(p.price);
    document.getElementById('mpModalDesc').textContent = p.desc || '';

    var specsEl = document.getElementById('mpModalSpecs');
    specsEl.replaceChildren();
    var specs = Object.assign({}, p.brand ? { Brand: p.brand } : {}, p.specs || {});
    Object.keys(specs).forEach(function (key) {
      var item = document.createElement('div');
      item.className = 'spec-item';
      var label = document.createElement('div');
      label.className = 'spec-label';
      label.textContent = key;
      var value = document.createElement('div');
      value.className = 'spec-value';
      value.textContent = specs[key];
      item.append(label, value);
      specsEl.appendChild(item);
    });
    specsEl.hidden = !specsEl.children.length;

    document.getElementById('mpModalNote').hidden = p.status !== 'digital';

    var wa = document.getElementById('mpModalWa');
    var waHref = waLink(p.name);
    wa.hidden = !waHref;
    if (waHref) wa.href = waHref;
    document.getElementById('mpModalMail').href = mailLink(p.name);

    if (typeof modal.showModal === 'function') {
      modal.showModal();
      document.body.classList.add('modal-open');
    }
  }

  grid.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-open-product]');
    if (trigger) openProduct(Number(trigger.getAttribute('data-open-product')));
  });

  if (modal) {
    modal.addEventListener('close', function () { document.body.classList.remove('modal-open'); });
    modal.querySelector('[data-close-modal]').addEventListener('click', function () { modal.close(); });
    // Click on the backdrop closes the dialog.
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.close(); });
  }
})();
