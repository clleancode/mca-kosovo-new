import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

let lenis = null;

const prefersReducedMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

export const getLenis = () => lenis;

export const scrollToTarget = (target, { offset = 0, duration = 1.65 } = {}) => {
    const top = typeof target === 'number'
        ? target
        : window.scrollY + target.getBoundingClientRect().top + offset;

    if (lenis) {
        lenis.scrollTo(top, {
            duration,
            easing: t => 1 - (1 - t) ** 4,
        });
        return;
    }

    window.scrollTo({ top, behavior: 'auto' });
};

export default function initLenis() {
    if (lenis || prefersReducedMotion()) return null;

    lenis = new Lenis({
        lerp: 0.055,
        smoothWheel: true,
        syncTouch: false,
        allowNestedScroll: true,
        wheelMultiplier: 0.85,
        touchMultiplier: 1.35,
    });

    document.documentElement.classList.add('lenis', 'lenis-smooth');

    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add(time => {
        lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    return lenis;
}
