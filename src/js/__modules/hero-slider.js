import Swiper from 'swiper';
import { Navigation, EffectFade } from 'swiper/modules';
import gsap from 'gsap';

const zoomIn = image => {
    gsap.fromTo(image, { scale: 1.12, transition: 'none' }, {
        scale: 1,
        duration: 2.2,
        ease: 'expo.out',
        clearProps: 'scale,transition',
    });
};

export default function heroSlider() {
    if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('.m-hero-about__image img, .m-small-hero__image img, .m-projects-hero__image img, [data-hero-slider] .swiper-slide:first-child .m-hero__image img').forEach(zoomIn);
    }

    document.querySelectorAll('[data-hero-slider]').forEach(element => {
        if (element.swiper) return;
        const cards = [...element.querySelectorAll('[data-hero-slide]')];
        const pause = element.querySelector('[data-hero-pause]');
        const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
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
        // First slide is revealed by e-reveal on load; this only runs on slide changes.
        const animateSlide = swiper => {
            if (reducedMotion) return;
            const slide = swiper.slides[swiper.activeIndex];
            const copy = slide?.querySelectorAll('.m-hero__copy > *');
            const image = slide?.querySelector('.m-hero__image img');
            if (copy?.length) {
                gsap.fromTo(copy, { opacity: 0, y: 15 }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    delay: 0.25,
                    stagger: 0.12,
                    ease: 'power3.out',
                    clearProps: 'opacity,transform',
                });
            }
            if (image) zoomIn(image);
        };
        const cardsRoot = element.querySelector('[data-hero-cards]');
        let cardsSwiper = null;
        let syncingCards = false;

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
            if (cardsSwiper && !cardsSwiper.destroyed && cardsSwiper.activeIndex !== swiper.realIndex) {
                syncingCards = true;
                cardsSwiper.slideTo(swiper.realIndex);
                syncingCards = false;
            }
        };

        const swiper = new Swiper(element, {
            modules: [Navigation, EffectFade],
            slidesPerView: 1,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: reducedMotion ? 0 : 900,
            rewind: true,
            navigation: { prevEl: element.querySelector('[data-hero-prev]'), nextEl: element.querySelector('[data-hero-next]'), addIcons: false },
            autoplay: false,
            on: {
                init: sync,
                slideChange(swiper) { sync(swiper); animateSlide(swiper); },
                destroy() { cancelAnimationFrame(frame); },
            },
        });

        if (cardsRoot && cards.length > 1) {
            cardsSwiper = new Swiper(cardsRoot, {
                slidesPerView: 1,
                spaceBetween: 20,
                speed: reducedMotion ? 0 : 600,
                watchOverflow: true,
                slideToClickedSlide: true,
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        allowTouchMove: false,
                    },
                },
                on: {
                    slideChange(instance) {
                        if (syncingCards || instance.destroyed) return;
                        swiper.slideTo(instance.activeIndex);
                    },
                },
            });
            sync(swiper);
        }
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
        cards.forEach((link, index) => {
            link.addEventListener('click', event => {
                event.preventDefault();
                swiper.slideTo(index);
            });
        });
        frame = requestAnimationFrame(tick);
    });
}
