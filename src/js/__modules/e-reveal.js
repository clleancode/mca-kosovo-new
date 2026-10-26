import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import SplitType from 'split-type';

gsap.registerPlugin(ScrollTrigger);

const DESKTOP = '(min-width: 768px)';
const DEFAULT_OFFSET = { desktop: 30, mobile: 15 };
const FONT_TIMEOUT = 1200;
const MAX_TEXT_STAGGER = 0.9;
const TEXT_OFFSET = 15;
const IMAGE_SCALE = 1.15;
const IMAGE_BLUR = 4;

const prefersReducedMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

const resolveOffsetPercent = element => {
    const custom = element.dataset.offset;
    if (custom !== undefined && custom.trim() !== '') {
        return parseFloat(custom);
    }
    return matchMedia(DESKTOP).matches ? DEFAULT_OFFSET.desktop : DEFAULT_OFFSET.mobile;
};

const scrollStart = offsetPercent => `top ${100 - offsetPercent}%`;

const parseDelayMs = element => {
    const parsed = parseFloat(element.dataset.delay);
    return Number.isFinite(parsed) ? parsed : 0;
};

const getMode = element => {
    if (element.classList.contains('e-reveal--clip')) return 'clip';
    if (element.classList.contains('e-reveal--image')) return 'image';
    if (element.classList.contains('e-reveal--text')) return 'text';
    return 'fade';
};

// Lines are measured when splitting, so wait for webfonts (capped) to avoid wrong breaks.
const fontsReady = () => Promise.race([
    document.fonts?.ready ?? Promise.resolve(),
    new Promise(resolve => setTimeout(resolve, FONT_TIMEOUT)),
]);

const maskWord = word => {
    const mask = document.createElement('span');
    mask.className = 'e-reveal__mask';
    word.before(mask);
    mask.append(word);
};

const prepare = element => {
    const mode = getMode(element);

    if (mode === 'clip') {
        gsap.set(element, { clipPath: 'inset(0 100% 0 0)' });
    }

    if (mode === 'image') {
        const images = [...element.querySelectorAll('img')];
        gsap.set(element, { clipPath: 'inset(0% 0% 100% 0%)' });
        // Component hover transitions on transform would make GSAP's per-frame updates lag.
        gsap.set(images, { scale: IMAGE_SCALE, filter: `blur(${IMAGE_BLUR}px)`, transition: 'none' });
        return { mode, images };
    }

    if (mode === 'text') {
        // Line masks break nested markup such as links, so those get a mask per word instead.
        const hasNested = [...element.children].some(child => child.tagName !== 'BR');
        const split = new SplitType(element, {
            types: hasNested ? 'words' : 'lines, words',
            lineClass: 'e-reveal__line',
            wordClass: 'e-reveal__word',
        });
        if (hasNested) split.words?.forEach(maskWord);
        gsap.set(split.words, { y: TEXT_OFFSET, rotate: 2, opacity: 0, transformOrigin: '0% 100%' });
        return { mode, split };
    }

    return { mode };
};

const playClip = element => {
    gsap.to(element, {
        clipPath: 'inset(0 0% 0 0)',
        duration: 1.4,
        ease: 'power4.inOut',
        clearProps: 'clipPath',
    });
};

const playImage = (element, images) => {
    gsap.timeline()
        .to(element, {
            clipPath: 'inset(0% 0% 0% 0%)',
            duration: 1.5,
            ease: 'expo.inOut',
            clearProps: 'clipPath',
        })
        .to(images, {
            scale: 1,
            duration: 2,
            ease: 'expo.out',
            clearProps: 'transform,transition',
        }, 0.15)
        .to(images, {
            filter: 'blur(0px)',
            duration: 1.2,
            ease: 'power2.out',
            clearProps: 'filter',
        }, 0.3);
};

const playText = split => {
    const words = split.words ?? [];
    if (!words.length) return;

    gsap.to(words, {
        y: 0,
        rotate: 0,
        opacity: 1,
        duration: 1,
        ease: 'power4.out',
        stagger: Math.min(0.06, MAX_TEXT_STAGGER / words.length),
        // Restores the original markup so lines re-wrap naturally on resize.
        onComplete: () => split.revert(),
    });
};

const play = (element, state) => {
    if (state.mode === 'fade') {
        element.classList.add('visible');
        return;
    }

    gsap.delayedCall(parseDelayMs(element) / 1000, () => {
        element.classList.add('visible');
        if (state.mode === 'clip') playClip(element);
        else if (state.mode === 'image') playImage(element, state.images);
        else playText(state.split);
    });
};

const init = elements => {
    elements.forEach(element => {
        const state = prepare(element);

        ScrollTrigger.create({
            trigger: element,
            start: () => scrollStart(resolveOffsetPercent(element)),
            once: true,
            invalidateOnRefresh: true,
            onEnter: () => play(element, state),
        });
    });

    ScrollTrigger.refresh();
};

export default function eReveal() {
    const elements = [...document.querySelectorAll('.e-reveal:not([data-e-reveal-init])')];
    if (!elements.length) return;

    elements.forEach(element => {
        element.dataset.eRevealInit = 'true';
        element.style.setProperty('--e-reveal-delay', `${parseDelayMs(element)}ms`);
    });

    if (prefersReducedMotion()) {
        elements.forEach(element => element.classList.add('visible'));
        return;
    }

    fontsReady().then(() => init(elements));
    window.addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
}
