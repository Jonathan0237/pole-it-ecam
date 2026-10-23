'use strict';

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ---------- Navigation ---------- */
function initNav() {
    const toggle = $('[data-nav-toggle]');
    const menu = $('#menu');
    const header = $('[data-header]');

    toggle?.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!open));
        menu.classList.toggle('is-open', !open);
        document.body.classList.toggle('nav-open', !open);
    });

    $$('[data-submenu-toggle]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', String(!open));
            btn.parentElement.classList.toggle('is-open', !open);
        });
    });
    document.addEventListener('click', () => {
        $$('[data-submenu-toggle][aria-expanded="true"]').forEach((btn) => {
            btn.setAttribute('aria-expanded', 'false');
            btn.parentElement.classList.remove('is-open');
        });
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') document.dispatchEvent(new Event('click'));
    });

    const onScroll = () => header?.classList.toggle('is-scrolled', window.scrollY > 40);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

/* ---------- Thème clair / sombre ---------- */
function initTheme() {
    const btn = $('[data-theme-toggle]');
    if (!btn) return;
    const root = document.documentElement;
    const current = () => root.getAttribute('data-theme')
        || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    const paint = () => {
        btn.innerHTML = current() === 'dark'
            ? '<i class="fa-solid fa-sun" aria-hidden="true"></i>'
            : '<i class="fa-solid fa-moon" aria-hidden="true"></i>';
    };
    paint();
    btn.addEventListener('click', () => {
        const next = current() === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) { /* ignoré */ }
        paint();
    });
}

/* ---------- Carrousel ---------- */
function initSlider() {
    $$('[data-slider]').forEach((slider) => {
        const slides = $$('.slide', slider);
        const dots = $('[data-dots]', slider);
        let index = 0;
        let timer = null;

        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Afficher la photo ${i + 1}`);
            dot.addEventListener('click', () => { go(i); restart(); });
            dots.appendChild(dot);
        });

        function go(i) {
            index = (i + slides.length) % slides.length;
            slides.forEach((s, n) => s.classList.toggle('is-active', n === index));
            $$('button', dots).forEach((d, n) => d.setAttribute('aria-current', String(n === index)));
        }
        function start() { if (!reduceMotion) timer = setInterval(() => go(index + 1), 5000); }
        function stop() { clearInterval(timer); }
        function restart() { stop(); start(); }

        $('[data-next]', slider).addEventListener('click', () => { go(index + 1); restart(); });
        $('[data-prev]', slider).addEventListener('click', () => { go(index - 1); restart(); });
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        slider.addEventListener('focusin', stop);

        let startX = null;
        slider.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
        slider.addEventListener('touchend', (e) => {
            if (startX === null) return;
            const dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 40) { go(index + (dx < 0 ? 1 : -1)); restart(); }
            startX = null;
        });

        go(0);
        start();
    });
}

/* ---------- Validation côté navigateur (confort ; le serveur fait foi) ---------- */
const messages = {
    valueMissing: 'Ce champ est obligatoire.',
    typeMismatch: 'Le format n\'est pas valide.',
    patternMismatch: 'Le format n\'est pas valide.',
    tooShort: (el) => `Au moins ${el.minLength} caractères.`,
    tooLong: (el) => `Au plus ${el.maxLength} caractères.`,
    rangeUnderflow: (el) => `Valeur minimale : ${el.min}.`,
    rangeOverflow: (el) => `Valeur maximale : ${el.max}.`,
};

function showError(el) {
    const field = el.closest('.field');
    if (!field) return true;
    let err = $('.error', field);
    const key = Object.keys(messages).find((k) => el.validity[k]);
    if (!key) {
        field.classList.remove('has-error');
        el.removeAttribute('aria-invalid');
        if (err?.dataset.client) err.remove();
        return true;
    }
    const text = typeof messages[key] === 'function' ? messages[key](el) : messages[key];
    if (!err) {
        err = document.createElement('p');
        err.className = 'error';
        err.id = `${el.id || el.name}-err`;
        err.dataset.client = '1';
        field.appendChild(err);
    }
    err.textContent = text;
    field.classList.add('has-error');
    el.setAttribute('aria-invalid', 'true');
    el.setAttribute('aria-describedby', err.id);
    return false;
}

function validateWithin(root) {
    const fields = $$('input, select, textarea', root).filter((el) => el.type !== 'hidden' && !el.closest('.hp'));
    const results = fields.map(showError);
    const firstInvalid = fields[results.indexOf(false)];
    firstInvalid?.focus();
    return !firstInvalid;
}

function initValidation() {
    $$('form[data-validate], form[data-stepper]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!validateWithin(form)) e.preventDefault();
        });
        $$('input, select, textarea', form).forEach((el) => {
            el.addEventListener('blur', () => { if (el.value !== '') showError(el); });
            el.addEventListener('input', () => { if (el.closest('.has-error')) showError(el); });
        });
    });
}

/* ---------- Formulaire multi-étapes (admission) ---------- */
function initStepper() {
    const form = $('[data-stepper]');
    if (!form) return;
    const steps = $$('[data-step]', form);
    const indicators = $$('[data-step-indicator]', form);
    let current = Number(form.dataset.startStep) || 1;

    form.classList.add('js-stepper');

    function show(n, focus = true) {
        current = n;
        steps.forEach((s) => { s.hidden = Number(s.dataset.step) !== n; });
        indicators.forEach((li) => {
            const i = Number(li.dataset.stepIndicator);
            li.classList.toggle('is-active', i === n);
            li.classList.toggle('is-done', i < n);
            if (i === n) li.setAttribute('aria-current', 'step'); else li.removeAttribute('aria-current');
        });
        if (focus) $('input, select, textarea', steps[n - 1])?.focus({ preventScroll: true });
    }

    $$('[data-next-step]', form).forEach((btn) => btn.addEventListener('click', () => {
        if (validateWithin(steps[current - 1])) show(current + 1);
    }));
    $$('[data-prev-step]', form).forEach((btn) => btn.addEventListener('click', () => show(current - 1)));

    show(current, false);
}

/* ---------- Compteurs animés ---------- */
function initCounters() {
    const els = $$('[data-count]');
    if (!els.length || reduceMotion || !('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach(({ isIntersecting, target }) => {
            if (!isIntersecting) return;
            io.unobserve(target);
            const end = Number(target.dataset.count);
            const suffix = target.dataset.suffix || '';
            const from = end > 1000 ? Math.round(end * 0.9) : 0;
            const t0 = performance.now();
            const tick = (t) => {
                const p = Math.min(1, (t - t0) / 1200);
                const eased = 1 - Math.pow(1 - p, 3);
                target.textContent = Math.round(from + (end - from) * eased) + suffix;
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        });
    }, { threshold: 0.5 });
    els.forEach((el) => io.observe(el));
}

/* ---------- Apparition au défilement ---------- */
function initReveal() {
    const els = $$('.reveal');
    if (reduceMotion || !('IntersectionObserver' in window)) return;
    document.documentElement.classList.add('js-reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(({ isIntersecting, target }) => {
            if (isIntersecting) { target.classList.add('is-visible'); io.unobserve(target); }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    els.forEach((el) => io.observe(el));
}

/* ---------- Divers ---------- */
function initMisc() {
    $$('[data-print]').forEach((btn) => btn.addEventListener('click', () => window.print()));
    $$('form[data-confirm]').forEach((form) => form.addEventListener('submit', (e) => {
        if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    }));
}

initNav();
initTheme();
initSlider();
initValidation();
initStepper();
initCounters();
initReveal();
initMisc();
