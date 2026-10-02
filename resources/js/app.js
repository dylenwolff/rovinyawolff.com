import './bootstrap';
const root = document.documentElement;
const themeToggle = document.querySelector('[data-theme-toggle]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');

themeToggle?.addEventListener('click', () => {
    const dark = !root.classList.contains('dark');
    root.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
});

menuToggle?.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    mobileMenu?.classList.toggle('hidden', isOpen);
});

mobileMenu?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
        menuToggle?.setAttribute('aria-expanded', 'false');
    });
});

if (document.querySelector('[data-flipbook]')) import('./flipbook');

const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
const ambient = document.querySelector('[data-ambient]');
if (ambient && !reducedMotion) {
    window.addEventListener('pointermove', (event) => {
        ambient.style.setProperty('--pointer-x', `${event.clientX}px`);
        ambient.style.setProperty('--pointer-y', `${event.clientY}px`);
    }, { passive: true });
}

document.querySelectorAll('[data-reveal]').forEach((element) => element.classList.add('reveal-ready'));
if (!reducedMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            observer.unobserve(entry.target);
        }
    }), { threshold: .12 });
    document.querySelectorAll('[data-reveal]').forEach((element) => observer.observe(element));
} else {
    document.querySelectorAll('[data-reveal]').forEach((element) => element.classList.add('revealed'));
}

document.querySelectorAll('[data-tilt-stage]').forEach((stage) => {
    const subject = stage.querySelector('[data-tilt]');
    if (!subject || reducedMotion) return;
    stage.addEventListener('pointermove', (event) => {
        const bounds = stage.getBoundingClientRect();
        const x = (event.clientX - bounds.left) / bounds.width - .5;
        const y = (event.clientY - bounds.top) / bounds.height - .5;
        subject.style.setProperty('--tilt-x', `${y * -9}deg`);
        subject.style.setProperty('--tilt-y', `${x * 13}deg`);
    });
    stage.addEventListener('pointerleave', () => {
        subject.style.setProperty('--tilt-x', '0deg');
        subject.style.setProperty('--tilt-y', '0deg');
    });
});
