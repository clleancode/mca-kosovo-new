import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const QUERY = '(min-width: 1024px) and (prefers-reduced-motion: no-preference)';
const DEPTH = 0.2;

// Layers drift down while their section scrolls out the top, so the exposed edge always stays above the viewport.
export default function eParallax() {
    const layers = [...document.querySelectorAll('[data-parallax]:not([data-parallax-init])')];
    if (!layers.length) return;

    const mm = gsap.matchMedia();

    layers.forEach(layer => {
        layer.dataset.parallaxInit = 'true';
        const trigger = layer.closest('section') ?? layer.parentElement;

        mm.add(QUERY, () => {
            gsap.to(layer, {
                y: () => trigger.offsetHeight * DEPTH,
                ease: 'none',
                scrollTrigger: {
                    trigger,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: true,
                    invalidateOnRefresh: true,
                },
            });
        });
    });
}
