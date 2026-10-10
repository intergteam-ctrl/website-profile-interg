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
})();
