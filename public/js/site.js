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

  /* ---------- Side menu (Marketplace, Download, Games) ---------- */
  var moreTab = document.getElementById('moreTab');
  var moreNav = document.getElementById('moreNav');
  var moreScrim = document.getElementById('moreScrim');
  function setMore(open) {
    if (!moreTab || !moreNav) return;
    moreNav.classList.toggle('open', open);
    moreNav.setAttribute('aria-hidden', String(!open));
    moreNav.inert = !open;
    moreTab.setAttribute('aria-expanded', String(open));
    if (moreScrim) moreScrim.hidden = !open;
    if (open) {
      requestAnimationFrame(function () { var f = moreNav.querySelector('.more-item'); if (f) f.focus({ preventScroll: true }); });
    } else {
      moreTab.focus({ preventScroll: true });
    }
  }
  if (moreTab) moreTab.addEventListener('click', function () { setMore(!moreNav.classList.contains('open')); });
  if (moreScrim) moreScrim.addEventListener('click', function () { setMore(false); });
  document.querySelectorAll('[data-more-close]').forEach(function (el) { el.addEventListener('click', function () { setMore(false); }); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && moreNav && moreNav.classList.contains('open')) setMore(false);
  });

  /* ---------- Instagram & YouTube side panels ---------- */
  var igScrim = document.getElementById('igScrim');
  var panels = [
    { tab: document.getElementById('igTab'), drawer: document.getElementById('igDrawer'), close: '[data-ig-close]' },
    { tab: document.getElementById('ytTab'), drawer: document.getElementById('ytDrawer'), close: '[data-yt-close]' },
    { tab: document.getElementById('ttTab'), drawer: document.getElementById('ttDrawer'), close: '[data-tt-close]', onOpen: loadTikTok }
  ].filter(function (p) { return p.tab && p.drawer; });

  // TikTok creator embed: injected on first open only.
  var ttLoaded = false;
  function loadTikTok(drawer) {
    if (ttLoaded) return;
    ttLoaded = true;
    var user = drawer.getAttribute('data-tt-user');
    var url = drawer.getAttribute('data-tt-url');
    var box = document.getElementById('ttEmbed');
    var fallback = document.getElementById('ttFallback');
    if (!user || !box) return;

    var quote = document.createElement('blockquote');
    quote.className = 'tiktok-embed';
    quote.setAttribute('cite', url);
    quote.setAttribute('data-unique-id', user);
    quote.setAttribute('data-embed-type', 'creator');
    quote.style.maxWidth = '780px';
    quote.style.minWidth = '288px';
    var section = document.createElement('section');
    var a = document.createElement('a');
    a.href = url; a.target = '_blank'; a.rel = 'noopener'; a.textContent = '@' + user;
    section.appendChild(a);
    quote.appendChild(section);
    box.innerHTML = '';
    box.appendChild(quote);

    var script = document.createElement('script');
    script.src = 'https://www.tiktok.com/embed.js';
    script.async = true;
    script.onerror = function () { if (fallback) fallback.hidden = false; };
    document.body.appendChild(script);

    // If TikTok has not replaced the placeholder with its player, offer the link.
    setTimeout(function () {
      if (!box.querySelector('iframe') && fallback) fallback.hidden = false;
    }, 10000);
  }
  var ytPlayer = document.getElementById('ytPlayer');

  function stopVideo() {
    if (!ytPlayer) return;
    ytPlayer.innerHTML = '';
    ytPlayer.hidden = true;
    document.querySelectorAll('.yt-item.is-playing').forEach(function (a) { a.classList.remove('is-playing'); });
  }

  function setPanel(panel, open) {
    panel.drawer.classList.toggle('open', open);
    panel.drawer.setAttribute('aria-hidden', String(!open));
    panel.drawer.inert = !open;
    panel.tab.setAttribute('aria-expanded', String(open));
    if (igScrim) igScrim.hidden = !open;
    if (open) {
      if (panel.onOpen) panel.onOpen(panel.drawer);
      // Wait a frame so the panel is no longer inert/hidden when focusing.
      requestAnimationFrame(function () {
        var close = panel.drawer.querySelector(panel.close);
        if (close) close.focus({ preventScroll: true });
      });
    } else {
      if (panel.drawer.id === 'ytDrawer') stopVideo();
      panel.tab.focus({ preventScroll: true });
    }
  }
  function openPanel() { return panels.filter(function (p) { return p.drawer.classList.contains('open'); })[0]; }

  panels.forEach(function (panel) {
    panel.tab.addEventListener('click', function () { setPanel(panel, true); });
    panel.drawer.querySelectorAll(panel.close).forEach(function (el) {
      el.addEventListener('click', function () { setPanel(panel, false); });
    });
  });
  if (igScrim) igScrim.addEventListener('click', function () { var p = openPanel(); if (p) setPanel(p, false); });
  document.addEventListener('keydown', function (e) {
    var p = openPanel();
    if (e.key === 'Escape' && p) setPanel(p, false);
  });

  // Play a video inside the panel instead of leaving the site.
  document.querySelectorAll('[data-yt-id]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      if (!ytPlayer || e.ctrlKey || e.metaKey || e.shiftKey) return;
      e.preventDefault();
      var id = link.getAttribute('data-yt-id');
      var frame = document.createElement('iframe');
      frame.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0';
      frame.title = link.getAttribute('data-yt-title') || 'Video YouTube';
      frame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
      frame.allowFullscreen = true;
      ytPlayer.innerHTML = '';
      ytPlayer.appendChild(frame);
      ytPlayer.hidden = false;
      document.querySelectorAll('.yt-item.is-playing').forEach(function (a) { a.classList.remove('is-playing'); });
      link.classList.add('is-playing');
      ytPlayer.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    });
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
})();
