import Swiper from 'swiper';
import { Navigation, EffectFade } from 'swiper/modules';

export default function heroSlider() {
    document.querySelectorAll('[data-hero-slider]').forEach(element => {
        if (element.swiper) return;
        const cards = [...element.querySelectorAll('[data-hero-slide]')];
        const pause = element.querySelector('[data-hero-pause]');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const footer = element.querySelector('.m-hero__footer');
        const reserveFooterSpace = () => element.style.setProperty('--hero-footer-height', `${Math.ceil(footer.getBoundingClientRect().height)}px`);
        reserveFooterSpace();
        if (window.ResizeObserver) new ResizeObserver(reserveFooterSpace).observe(footer);
        else window.addEventListener('resize', reserveFooterSpace);

        const duration = 8000;
        let elapsed = 0;
        let previousTime = null;
        let paused = reducedMotion || cards.length < 2;
        let frame;
        const sync = swiper => {
            elapsed = 0;
            previousTime = null;
            element.querySelector('[data-hero-current]').textContent = String(swiper.realIndex + 1).padStart(2, '0');
            cards.forEach((link, index) => {
                link.parentElement.classList.toggle('is-active', index === swiper.realIndex);
                link.parentElement.style.setProperty('--hero-progress', '0');
                if (index === swiper.realIndex) link.setAttribute('aria-current', 'true');
                else link.removeAttribute('aria-current');
            });
        };
        const swiper = new Swiper(element, {
            modules: [Navigation, EffectFade],
            slidesPerView: 1,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: reducedMotion ? 0 : 700,
            rewind: true,
            navigation: { prevEl: element.querySelector('[data-hero-prev]'), nextEl: element.querySelector('[data-hero-next]'), addIcons: false },
            autoplay: false,
            on: { init: sync, slideChange: sync, destroy() { cancelAnimationFrame(frame); } },
        });
        const tick = time => {
            if (swiper.destroyed) return;
            if (!paused && !document.hidden) {
                if (previousTime !== null) elapsed += time - previousTime;
                previousTime = time;
                cards[swiper.realIndex]?.parentElement.style.setProperty('--hero-progress', String(Math.min(1, elapsed / duration)));
                if (elapsed >= duration) swiper.slideNext();
            } else previousTime = null;
            frame = requestAnimationFrame(tick);
        };
        const setPaused = value => {
            paused = value;
            previousTime = null;
            pause.setAttribute('aria-pressed', String(paused));
            pause.setAttribute('aria-label', paused ? 'Play slideshow' : 'Pause slideshow');
            element.classList.toggle('is-paused', paused);
        };
        setPaused(paused);
        pause.setAttribute('aria-disabled', String(cards.length < 2));
        pause.addEventListener('click', event => {
            event.preventDefault();
            if (cards.length > 1) setPaused(!paused);
        });
        element.querySelectorAll('.m-hero__controls > a').forEach(link => {
            link.addEventListener('click', event => event.preventDefault());
            link.addEventListener('keydown', event => {
                if (event.code === 'Space') { event.preventDefault(); link.click(); }
            });
        });
        frame = requestAnimationFrame(tick);
    });
}
