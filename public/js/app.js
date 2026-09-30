/* Divine Hospital – Divine ENT Centre · progressive enhancement (site works without JS) */
(() => {
  'use strict';

  window.__divineReady = true;

  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* ---------- Scroll: header state, progress bar, back-to-top ring ---------- */
  const header = $('[data-header]');
  const progressBar = $('[data-scroll-progress]');
  const toTop = $('[data-to-top]');
  const toTopRing = toTop && $('circle', toTop);
  {
    let ticking = false;
    const update = () => {
      const y = window.scrollY;
      const max = document.documentElement.scrollHeight - window.innerHeight;
      const progress = max > 0 ? Math.min(1, y / max) : 0;
      header?.classList.toggle('is-scrolled', y > 8);
      if (progressBar) progressBar.style.transform = `scaleX(${progress})`;
      if (toTop) {
        toTop.classList.toggle('is-visible', y > 600);
        toTopRing.style.strokeDashoffset = String(100 - progress * 100);
      }
      ticking = false;
    };
    const schedule = () => {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(update);
      }
    };
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    update();
    toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' }));
  }

  /* ---------- Sliding highlight behind the desktop nav ---------- */
  const nav = $('[data-nav]');
  if (nav) {
    const indicator = $('.nav__indicator', nav);
    const links = $$('.nav__link', nav);
    const current = links.find((a) => a.getAttribute('aria-current') === 'page');
    const moveTo = (link, instant = false) => {
      if (!link || !link.offsetParent) {
        indicator.classList.remove('is-visible');
        return;
      }
      const navBox = nav.getBoundingClientRect();
      const box = link.getBoundingClientRect();
      indicator.classList.toggle('no-anim', instant || !indicator.classList.contains('is-visible'));
      indicator.style.width = `${box.width}px`;
      indicator.style.translate = `${box.left - navBox.left}px -50%`;
      indicator.classList.add('is-visible');
      requestAnimationFrame(() => indicator.classList.remove('no-anim'));
    };
    nav.classList.add('has-indicator');
    links.forEach((a) => {
      a.addEventListener('pointerenter', () => moveTo(a));
      a.addEventListener('focus', () => moveTo(a));
    });
    nav.addEventListener('pointerleave', () => moveTo(current));
    nav.addEventListener('focusout', (event) => { if (!nav.contains(event.relatedTarget)) moveTo(current); });
    const reset = () => moveTo(current, true);
    window.addEventListener('resize', reset, { passive: true });
    document.fonts?.ready.then(reset);
    reset();
  }

  /* ---------- Mobile menu ---------- */
  const toggle = $('[data-menu-toggle]');
  const menu = $('[data-mobile-menu]');
  if (toggle && menu) {
    const label = $('[data-menu-label]', toggle);

    const setOpen = (open) => {
      toggle.setAttribute('aria-expanded', String(open));
      if (label) label.textContent = open ? 'Close menu' : 'Open menu';
      document.documentElement.classList.toggle('menu-open', open);

      if (open) {
        menu.style.setProperty('--menu-top', `${header.getBoundingClientRect().bottom}px`);
        menu.hidden = false;
        requestAnimationFrame(() => menu.classList.add('is-open'));
        $('a', menu)?.focus({ preventScroll: true });
      } else {
        menu.classList.remove('is-open');
        const hide = () => { if (!menu.classList.contains('is-open')) menu.hidden = true; };
        reducedMotion ? hide() : setTimeout(hide, 260);
      }
    };

    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    menu.addEventListener('click', (event) => { if (event.target.closest('a')) setOpen(false); });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        setOpen(false);
        toggle.focus();
      }
    });
    window.matchMedia('(min-width: 1100px)').addEventListener('change', (event) => { if (event.matches) setOpen(false); });
  }

  /* ---------- Scroll reveal ---------- */
  const revealables = $$('[data-reveal]');
  if ('IntersectionObserver' in window && !reducedMotion) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          observer.unobserve(entry.target);
        }
      });
    }, {
      // The huge top margin also reveals anything already scrolled past (e.g. after jumping to #location).
      rootMargin: '99999px 0px -8% 0px',
      threshold: 0.12,
    });
    revealables.forEach((el) => observer.observe(el));
  } else {
    revealables.forEach((el) => el.classList.add('is-in'));
  }

  /* ---------- "Open now" status (clinic timezone) ---------- */
  const hoursEl = $('#clinic-hours');
  const statusEls = $$('[data-open-status]');
  if (hoursEl && (statusEls.length || $('[data-hours-row]'))) {
    try {
      const hours = JSON.parse(hoursEl.textContent);
      const toMinutes = (hhmm) => { const [h, m] = hhmm.split(':').map(Number); return h * 60 + m; };
      const fmt = (mins) => {
        const h = Math.floor(mins / 60) % 24;
        const m = mins % 60;
        const suffix = h >= 12 ? 'PM' : 'AM';
        return `${h % 12 || 12}${m ? `:${String(m).padStart(2, '0')}` : ''} ${suffix}`;
      };
      const dayNames = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

      const now = () => {
        const parts = new Intl.DateTimeFormat('en-GB', {
          timeZone: hours.tz, weekday: 'short', hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
        }).formatToParts(new Date());
        const get = (type) => parts.find((p) => p.type === type)?.value;
        const day = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 7 }[get('weekday')];
        return { day, minutes: Number(get('hour')) * 60 + Number(get('minute')) };
      };

      const compute = () => {
        const { day, minutes } = now();
        const sessions = hours.sessions.map((s) => ({ from: toMinutes(s.from), to: toMinutes(s.to) }));
        const openToday = hours.days.includes(day);

        if (openToday) {
          const current = sessions.find((s) => minutes >= s.from && minutes < s.to);
          if (current) return { open: true, openToday, label: 'Open now', detail: `until ${fmt(current.to)}` };
          const later = sessions.find((s) => s.from > minutes);
          if (later) return { open: false, openToday, label: 'Closed now', detail: `opens today at ${fmt(later.from)}` };
        }
        for (let i = 1; i <= 7; i += 1) {
          const next = ((day - 1 + i) % 7) + 1;
          if (hours.days.includes(next)) {
            const when = i === 1 ? 'tomorrow' : dayNames[next];
            return { open: false, openToday, label: 'Closed now', detail: `opens ${when} at ${fmt(sessions[0].from)}` };
          }
        }
        return null;
      };

      const render = () => {
        const state = compute();
        if (!state) return;
        statusEls.forEach((el) => {
          el.classList.toggle('is-open', state.open);
          el.classList.toggle('is-closed', !state.open);
          const label = $('[data-open-label]', el);
          const detail = $('[data-open-detail]', el);
          if (label) label.textContent = state.label;
          if (detail) detail.textContent = state.detail;
        });
        $$('[data-hours-row]').forEach((row) => {
          row.classList.toggle('is-today', (row.dataset.hoursRow === 'open') === state.openToday);
        });
      };

      render();
      setInterval(render, 60 * 1000);
    } catch (error) {
      /* keep the server-rendered timings */
    }
  }

  /* ---------- Services sub-navigation active state ---------- */
  const specNav = $('[data-spec-nav]');
  if (specNav && 'IntersectionObserver' in window) {
    const links = $$('a', specNav);
    const blocks = links.map((a) => $(a.getAttribute('href'))).filter(Boolean);
    const visible = new Set();
    const spy = new IntersectionObserver((entries) => {
      entries.forEach((entry) => (entry.isIntersecting ? visible.add(entry.target) : visible.delete(entry.target)));
      const current = blocks.find((block) => visible.has(block));
      links.forEach((a) => a.classList.toggle('is-active', !!current && a.getAttribute('href') === `#${current.id}`));
    }, { rootMargin: '-45% 0px -50% 0px' });
    blocks.forEach((block) => spy.observe(block));
  }

  /* ---------- Gallery filter ---------- */
  const items = $$('[data-gallery-item]');
  const filter = $('[data-gallery-filter]');
  if (filter) {
    const buttons = $$('[data-filter]', filter);
    buttons.forEach((button) => button.addEventListener('click', () => {
      const group = button.dataset.filter;
      buttons.forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
      items.forEach((item) => { item.hidden = group !== '' && item.dataset.group !== group; });
    }));
  }

  /* ---------- Gallery lightbox ---------- */
  const lightbox = $('[data-lightbox]');
  if (lightbox && items.length && typeof lightbox.showModal === 'function') {
    const img = $('[data-lightbox-img]', lightbox);
    const caption = $('[data-lightbox-caption]', lightbox);
    let shown = items;
    let index = 0;
    let opener = null;

    const show = (i) => {
      index = (i + shown.length) % shown.length;
      const item = shown[index];
      img.src = item.dataset.full;
      img.alt = item.dataset.caption;
      caption.textContent = `${item.dataset.caption} · ${index + 1} / ${shown.length}`;
      img.classList.remove('is-anim');
      void img.offsetWidth; // restart the zoom-in animation for each photo
      img.classList.add('is-anim');
    };

    items.forEach((item) => item.addEventListener('click', () => {
      opener = item;
      shown = items.filter((photo) => !photo.hidden);
      show(shown.indexOf(item));
      lightbox.showModal();
    }));
    $('[data-lightbox-close]', lightbox).addEventListener('click', () => lightbox.close());
    $('[data-lightbox-prev]', lightbox).addEventListener('click', () => show(index - 1));
    $('[data-lightbox-next]', lightbox).addEventListener('click', () => show(index + 1));
    lightbox.addEventListener('click', (event) => { if (event.target === lightbox) lightbox.close(); });
    lightbox.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft') show(index - 1);
      if (event.key === 'ArrowRight') show(index + 1);
    });
    lightbox.addEventListener('close', () => opener?.focus());
  }

  if (reducedMotion) return;

  /* ---------- Tap ripple on buttons ---------- */
  document.addEventListener('pointerdown', (event) => {
    const host = event.target.closest?.('.btn, .action-bar a');
    if (!host) return;
    const box = host.getBoundingClientRect();
    const size = Math.max(box.width, box.height) * 2.2;
    const dot = document.createElement('span');
    dot.className = 'ripple';
    dot.style.cssText = `width:${size}px;height:${size}px;left:${event.clientX - box.left}px;top:${event.clientY - box.top}px`;
    host.append(dot);
    dot.addEventListener('animationend', () => dot.remove());
  }, { passive: true });

  if (!finePointer) return;

  /* ---------- Hero mouse parallax (smoothed) ---------- */
  const hero = $('[data-parallax]');
  if (hero) {
    let targetX = 0;
    let targetY = 0;
    let x = 0;
    let y = 0;
    let frame = 0;
    const tick = () => {
      x += (targetX - x) * 0.08;
      y += (targetY - y) * 0.08;
      hero.style.setProperty('--mx', x.toFixed(3));
      hero.style.setProperty('--my', y.toFixed(3));
      frame = Math.abs(targetX - x) + Math.abs(targetY - y) > 0.002 ? requestAnimationFrame(tick) : 0;
    };
    const start = () => { if (!frame) frame = requestAnimationFrame(tick); };
    hero.addEventListener('pointermove', (event) => {
      const box = hero.getBoundingClientRect();
      targetX = ((event.clientX - box.left) / box.width - 0.5) * 2;
      targetY = ((event.clientY - box.top) / box.height - 0.5) * 2;
      start();
    });
    hero.addEventListener('pointerleave', () => { targetX = 0; targetY = 0; start(); });
  }

  /* ---------- 3D tilt ---------- */
  $$('[data-tilt]').forEach((el) => {
    el.addEventListener('pointermove', (event) => {
      const box = el.getBoundingClientRect();
      el.classList.add('is-tilting');
      el.style.setProperty('--ry', `${((event.clientX - box.left) / box.width - 0.5) * 9}deg`);
      el.style.setProperty('--rx', `${((event.clientY - box.top) / box.height - 0.5) * -9}deg`);
    });
    el.addEventListener('pointerleave', () => {
      el.classList.remove('is-tilting');
      el.style.setProperty('--rx', '0deg');
      el.style.setProperty('--ry', '0deg');
    });
  });

  /* ---------- Magnetic call-to-action buttons ---------- */
  $$('[data-magnetic]').forEach((el) => {
    el.addEventListener('pointermove', (event) => {
      const box = el.getBoundingClientRect();
      const dx = (event.clientX - (box.left + box.width / 2)) * 0.22;
      const dy = (event.clientY - (box.top + box.height / 2)) * 0.35;
      el.style.translate = `${dx.toFixed(1)}px ${dy.toFixed(1)}px`;
    });
    el.addEventListener('pointerleave', () => { el.style.translate = ''; });
  });

  /* ---------- Cursor spotlight on cards ---------- */
  let spot = null;
  let spotX = 0;
  let spotY = 0;
  let spotFrame = 0;
  document.addEventListener('pointermove', (event) => {
    const el = event.target.closest?.('.spotlight');
    if (!el) return;
    spot = el;
    spotX = event.clientX;
    spotY = event.clientY;
    if (!spotFrame) {
      spotFrame = requestAnimationFrame(() => {
        spotFrame = 0;
        const box = spot.getBoundingClientRect();
        spot.style.setProperty('--sx', `${spotX - box.left}px`);
        spot.style.setProperty('--sy', `${spotY - box.top}px`);
      });
    }
  }, { passive: true });
})();
