import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import SplitType from 'split-type';

gsap.registerPlugin(ScrollTrigger);

const DESKTOP = '(min-width: 768px)';
const DEFAULT_OFFSET = { desktop: 30, mobile: 15 };
const FONT_TIMEOUT = 1200;
const MAX_TEXT_STAGGER = 0.9;
const TEXT_OFFSET = 15;
const IMAGE_SCALE = 1.12;
const SEQUENCE_STEP = 120;
const ROW_TOLERANCE = 24;
const REFRESH_DEBOUNCE = 200;

const root = document.documentElement;

const prefersReducedMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

const resolveOffsetPercent = element => {
    const custom = element.dataset.offset;
    if (custom !== undefined && custom.trim() !== '') {
        return parseFloat(custom);
    }
    return matchMedia(DESKTOP).matches ? DEFAULT_OFFSET.desktop : DEFAULT_OFFSET.mobile;
};

// Capped at the max scroll, so content at the very end of a page still reaches its trigger.
const scrollStart = element => {
    const offset = resolveOffsetPercent(element) / 100;
    const natural = element.getBoundingClientRect().top + window.scrollY - window.innerHeight * (1 - offset);
    return Math.min(natural, ScrollTrigger.maxScroll(window) - 1);
};

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

const splitText = element => {
    // Line masks break nested markup such as links, so those get a mask per word instead.
    const hasNested = [...element.children].some(child => child.tagName !== 'BR');
    const split = new SplitType(element, {
        types: hasNested ? 'words' : 'lines, words',
        lineClass: 'e-reveal__line',
        wordClass: 'e-reveal__word',
    });
    if (hasNested) split.words?.forEach(maskWord);
    gsap.set(split.words, { y: TEXT_OFFSET, rotate: 2, opacity: 0, transformOrigin: '0% 100%' });
    return split;
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
        gsap.set(images, { scale: IMAGE_SCALE, transition: 'none' });
        return { mode, images };
    }

    if (mode === 'text') {
        return { mode, split: splitText(element) };
    }

    return { mode };
};

const playClip = element => {
    gsap.to(element, {
        clipPath: 'inset(0 0% 0 0)',
        duration: 1.2,
        ease: 'power4.inOut',
        clearProps: 'clipPath',
    });
};

const playImage = (element, images) => {
    gsap.timeline()
        .to(element, {
            clipPath: 'inset(0% 0% 0% 0%)',
            duration: 1.2,
            ease: 'expo.inOut',
            clearProps: 'clipPath',
        })
        .to(images, {
            scale: 1,
            duration: 1.6,
            ease: 'expo.out',
            clearProps: 'transform,transition',
        }, 0.1);
};

const playText = split => {
    const words = split.words ?? [];
    if (!words.length) return;

    gsap.to(words, {
        y: 0,
        rotate: 0,
        opacity: 1,
        duration: 0.9,
        ease: 'power4.out',
        stagger: Math.min(0.05, MAX_TEXT_STAGGER / words.length),
        // Restores the original markup so lines re-wrap naturally on resize.
        onComplete: () => split.revert(),
    });
};

// Reveals inside a `[data-e-reveal-sequence]` container that fire together are queued one step apart,
// in on-screen order, so a grid entering the viewport at once still opens item by item.
const sequences = new Map();
const flushSequence = group => {
    const { items, step } = sequences.get(group);
    items.splice(0).sort((a, b) => {
        const ra = a.element.getBoundingClientRect();
        const rb = b.element.getBoundingClientRect();
        // Items whose tops differ by less than a few pixels sit on the same row.
        return Math.abs(ra.top - rb.top) > ROW_TOLERANCE ? ra.top - rb.top : ra.left - rb.left;
    }).forEach(({ run }) => {
        const state = sequences.get(group);
        const now = performance.now();
        const start = Math.max(now, state.next);
        state.next = start + step;
        run(start - now);
    });
    sequences.get(group).scheduled = false;
};

const inSequence = (element, run) => {
    const group = element.closest('[data-e-reveal-sequence]');
    if (!group) {
        run(0);
        return;
    }
    if (!sequences.has(group)) {
        sequences.set(group, { items: [], next: 0, scheduled: false, step: parseFloat(group.dataset.eRevealSequence) || SEQUENCE_STEP });
    }
    const state = sequences.get(group);
    state.items.push({ element, run });
    if (state.scheduled) return;
    state.scheduled = true;
    requestAnimationFrame(() => flushSequence(group));
};

const play = (element, state) => inSequence(element, extra => {
    const delay = parseDelayMs(element) + extra;
    element.style.setProperty('--e-reveal-delay', `${delay}ms`);

    if (state.mode === 'fade') {
        element.classList.add('visible');
        return;
    }

    gsap.delayedCall(delay / 1000, () => {
        element.classList.add('visible');
        if (state.mode === 'clip') playClip(element);
        else if (state.mode === 'image') playImage(element, state.images);
        else playText(state.split);
    });
});

let refreshTimer;
const scheduleRefresh = () => {
    clearTimeout(refreshTimer);
    refreshTimer = setTimeout(() => ScrollTrigger.refresh(), REFRESH_DEBOUNCE);
};

// Show more, accordions, AJAX filters and late images shift everything below; trigger positions must follow.
let heightObserver;
const watchPageHeight = () => {
    if (heightObserver) return;
    let lastHeight = document.documentElement.scrollHeight;
    heightObserver = new ResizeObserver(() => {
        const height = document.documentElement.scrollHeight;
        if (height === lastHeight) return;
        lastHeight = height;
        scheduleRefresh();
    });
    heightObserver.observe(document.body);
};

// Collapsed groups and inactive tabs have no layout yet: measuring them would fire the trigger instantly.
const waiting = new Map();
let renderObserver;
const whenRendered = (element, callback) => {
    if (element.getClientRects().length) {
        callback();
        return;
    }
    renderObserver ??= new ResizeObserver(entries => {
        entries.forEach(({ target }) => {
            if (!target.getClientRects().length) return;
            renderObserver.unobserve(target);
            waiting.get(target)?.();
            waiting.delete(target);
        });
    });
    waiting.set(element, () => {
        callback();
        scheduleRefresh();
    });
    renderObserver.observe(element);
};

const observe = element => {
    const state = prepare(element);

    ScrollTrigger.create({
        trigger: element,
        start: () => scrollStart(element),
        once: true,
        invalidateOnRefresh: true,
        onEnter: () => play(element, state),
    });
};

const init = elements => {
    elements.forEach(element => whenRendered(element, () => observe(element)));
    ScrollTrigger.refresh();
    watchPageHeight();
};

export default function eReveal() {
    root.classList.add('is-reveal-ready');

    const elements = [...document.querySelectorAll('.e-reveal:not([data-e-reveal-init])')];
    if (!elements.length) return;

    elements.forEach(element => {
        element.dataset.eRevealInit = 'true';
        element.style.setProperty('--e-reveal-delay', `${parseDelayMs(element)}ms`);
    });

    // `has-js` is dropped by the head failsafe when this bundle loads too late; content is already shown then.
    if (prefersReducedMotion() || !root.classList.contains('has-js')) {
        elements.forEach(element => element.classList.add('visible'));
        return;
    }

    fontsReady().then(() => init(elements));
    if (document.readyState !== 'complete') {
        window.addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
    }
}
