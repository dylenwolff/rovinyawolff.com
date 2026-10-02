import './bootstrap';

const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');

menuToggle?.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    mobileMenu?.classList.toggle('hidden', isOpen);
});

mobileMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    mobileMenu.classList.add('hidden');
    menuToggle?.setAttribute('aria-expanded', 'false');
}));

if (document.querySelector('[data-flipbook]')) import('./flipbook');

const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
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

const stage = document.querySelector('[data-cover-stage]');
if (stage && !reducedMotion) {
    window.addEventListener('pointermove', (event) => {
        const x = (event.clientX / window.innerWidth - .5) * 12;
        const y = (event.clientY / window.innerHeight - .5) * 8;
        stage.style.setProperty('--mx', `${x}px`);
        stage.style.setProperty('--my', `${y}px`);
    }, { passive: true });
}
