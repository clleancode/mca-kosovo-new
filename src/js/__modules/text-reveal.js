import { revealOffset } from './reveal-offset';

const textReveal = () => {
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (!('IntersectionObserver' in window) || !Element.prototype.animate) return;

    const textBlocks = 'h1, h2, h3, h4, h5, h6, p, blockquote, figcaption, li, dt, dd, address, summary, td, th, .a-heading, .a-text';
    const buttonSelector = '.m-all-documents__filters > a, button, .a-btn, [class*="a-btn--"], .m-overview__navigation .a-overview-link, .m-overview__navigation .a-overview-factsheet, .m-transparency__documents .a-transparency-download, [role="button"], input[type="submit"], input[type="button"]';
    const faqIconSelector = '.m-faq__item > summary > span:last-child';
    const badgeSelector = [
        '.a-badge', '.a-notices-status', '.a-opportunities-status',
        '.a-activities-pill', '.a-progress-status', '.a-progress-pill',
        '.a-project-updates-pill', '.a-spotlight-pill',
        '.m-compact-projects__number', '.m-compact-projects__pair-label',
        '.m-benefits__badge', '.m-all-albums__badge', '.m-news-grid__badge',
        '.m-partnership__badge', '.m-single-hero__badge',
        '.m-hero-collection__pill', '.m-related-programs__status',
        '.m-small-hero__status', '.m-team__badge', '.m-timeline__pill',
        '.m-vacancies__status', '.m-current-calls__eyebrow',
        '.m-newsletter-subscribe__label',
        '.m-how-to-apply__number',
    ].join(', ');
    const decorativeSelector = `${faqIconSelector}, .m-how-to-apply__number, .m-contact-form__icon, .m-opportunities__panel-icon`;
    const contactFieldSelector = '.m-contact-form__field, .m-contact-form__topics, .m-contact-form__privacy, .m-contact-form__submit, .m-application__field, .m-application__upload, .m-application__consent, .m-application__submit, .m-application__main-photo, .m-application__secondary-photo';
    const panelSelectors = ['card', 'panel', 'photo', 'image', 'cover', 'fact', 'statistic', 'document', 'contact', 'album', 'way', 'step']
        .flatMap(name => [`[class$="__${name}"]`, `[class*="__${name} "]`, `[class*="__${name}--"]`])
        .map(selector => `${selector}:not(.m-team__panel):not(.m-vacancies__panel):not(.m-all-documents__filter-panel)`);
    const contentSelector = ['article', 'figure', 'picture', 'img', 'video', 'ul:not(.m-documents__document-list)', 'ol:not(.m-progress__milestones):not(.m-how-to-apply__steps):not(.m-timeline__milestones)', 'dl', 'table', '[data-aos]', '[data-scroll-reveal]', ...panelSelectors, '.m-progress__milestone', '.m-documents__document-list > li', '.m-timeline__milestone'].join(', ');
    const barSelector = '.m-compact__funding-bar, .m-storage__capacity-bar, .m-progress__phase .a-progress-bar, .m-partnership__bar, .m-join__image';
    const wholeElementSelector = `${contentSelector}, ${buttonSelector}, ${badgeSelector}, .m-compact__time, ${decorativeSelector}, .m-contact-form__directions, ${contactFieldSelector}, ${barSelector}, .m-partnership__partner-card, .m-gallery-banner__search, .m-small-hero__search, .m-our-office__content, .m-our-office__map, .m-compact__countdown, .m-storage__map, .m-storage__capacity, .m-impact__description, .m-opportunities__procurements, .m-partnership__implementation, .m-partnership__partners-header, .m-news__newsletter`;
    const selector = `${textBlocks}, a, span, time, div, ${wholeElementSelector}`;
    const excluded = '.m-timeline__board, #a-countdown, .o-header, .o-cookie-banner, #wpadminbar, select, textarea, script, style, svg, [role="status"], [role="alert"], .screen-reader-text, .sr-only, [aria-hidden="true"], .mca-reveal-word, .swiper:not(.m-gallery-slider__swiper), .m-hero, .m-header, .fancybox__container, .m-document-preview__stage, [data-text-reveal="off"], .a-btn--fixed-left, .a-btn--fixed, .swiper-button-prev, .swiper-button-next, .swiper-pagination, .m-hero__pagination, .m-hero__current, .m-hero__total';
    const registered = new WeakSet();
    const revealed = new WeakSet();
    const animations = new Set();
    const pending = new WeakMap();
    const pendingElements = new Set();
    const words = new WeakMap();
    const wholeElements = new WeakSet();
    const sequences = new WeakMap();
    const orderedDelay = target => {
        const scope = target.closest('section') || target.closest('main') || document.body;
        const now = performance.now();
        const start = Math.max(now, sequences.get(scope) || 0);
        sequences.set(scope, start + 160);
        return Math.min(start - now, 1600);
    };
    let lastScrollY = window.scrollY;
    let scrollingDown = true;
    const updateScrollDirection = () => {
        const currentScrollY = Math.max(0, window.scrollY);
        if (currentScrollY !== lastScrollY) scrollingDown = currentScrollY > lastScrollY;
        lastScrollY = currentScrollY;
    };
    window.addEventListener('scroll', updateScrollDirection, { passive: true });

    const splitWords = element => {
        const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) {
            if (!walker.currentNode.parentElement.closest('script, style, svg, [aria-hidden="true"]')) nodes.push(walker.currentNode);
        }
        const spans = [];
        nodes.forEach(node => {
            const fragment = document.createDocumentFragment();
            node.textContent.split(/(\s+)/).filter(Boolean).forEach(part => {
                if (/^\s+$/.test(part)) {
                    fragment.append(document.createTextNode(part));
                    return;
                }
                const span = document.createElement('span');
                span.className = 'mca-reveal-word';
                span.textContent = part;
                spans.push(span);
                fragment.append(span);
            });
            node.replaceWith(fragment);
        });
        return spans;
    };

    const restoreWords = element => {
        words.get(element)?.forEach(span => {
            if (span.parentNode) span.replaceWith(...span.childNodes);
        });
        words.delete(element);
    };

    const onIntersection = entries => {
        updateScrollDirection();
        entries.sort((a, b) => {
            const first = a.target.getBoundingClientRect();
            const second = b.target.getBoundingClientRect();
            if (a.target.matches('.m-timeline__milestone') && b.target.matches('.m-timeline__milestone') && a.target.parentElement === b.target.parentElement) {
                return a.target.compareDocumentPosition(b.target) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1;
            }
            if (a.target.matches('.m-gallery-slider__figure') && b.target.matches('.m-gallery-slider__figure') && a.target.closest('[data-gallery-slider]') === b.target.closest('[data-gallery-slider]')) {
                return first.left - second.left;
            }
            return Math.abs(first.top - second.top) > 24 ? first.top - second.top : first.left - second.left;
        });
        entries.forEach(({ target, isIntersecting }) => {
            if (!target.isConnected) {
                observer.unobserve(target);
                return;
            }
            if (!isIntersecting) return;
            observer.unobserve(target);
            if (revealed.has(target)) return;
            revealed.add(target);
            const animation = pending.get(target);
            if (!animation) return;
            if (motion.matches || !scrollingDown) {
                animation.cancel();
                restoreWords(target);
                return;
            }
            const blockDelay = orderedDelay(target) + (target.closest('.m-join__content') ? 1400 : 0);
            if (wholeElements.has(target)) {
                animation.cancel();
                const style = getComputedStyle(target);
                const isBar = target.matches(barSelector);
                const effect = target.animate(isBar ? [
                    { clipPath: 'inset(0 100% 0 0)' },
                    { clipPath: 'inset(0 0% 0 0)' },
                ] : [
                    { opacity: 0, translate: target.matches('.m-progress__milestone, .m-gallery-slider__figure, .m-overview__bar .a-overview-link, .m-all-documents__filters > a') ? '-24px 0' : '0 24px' },
                    { opacity: style.opacity, translate: style.translate },
                ], {
                    duration: isBar ? 1400 : 700,
                    delay: blockDelay + (target.matches('.m-our-office__map') ? 700 : 0),
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    fill: 'both',
                });
                animations.add(effect);
                effect.addEventListener('finish', () => effect.cancel(), { once: true });
                effect.addEventListener('cancel', () => animations.delete(effect), { once: true });
                return;
            }
            const spans = words.get(target) || [];
            const positions = spans.map(span => span.getBoundingClientRect().top);
            const lines = [...positions].sort((a, b) => a - b).reduce((result, top) => {
                if (!result.length || top - result[result.length - 1] > 5) result.push(top);
                return result;
            }, []);
            const heading = target.matches('h1, h2, h3, h4, h5, h6, .a-heading');
            const running = spans.map((span, index) => {
                const line = Math.max(0, lines.findIndex(top => Math.abs(top - positions[index]) <= 5));
                const effect = span.animate([
                    { opacity: 0, transform: 'translateY(0.65em)', clipPath: 'inset(0 0 100% 0)' },
                    { opacity: 1, transform: 'translateY(0)', clipPath: 'inset(0 0 0% 0)' },
                ], {
                    duration: heading ? 850 : 700,
                    delay: blockDelay + Math.min(line, 5) * 80,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    fill: 'both',
                });
                animations.add(effect);
                effect.addEventListener('cancel', () => animations.delete(effect), { once: true });
                return effect;
            });
            animation.cancel();
            Promise.allSettled(running.map(effect => effect.finished)).then(() => {
                running.forEach(effect => { effect.cancel(); animations.delete(effect); });
                restoreWords(target);
            });
        });
    };
    const createObserver = () => new IntersectionObserver(onIntersection, {
        threshold: 0, rootMargin: `0px 0px -${revealOffset()}px 0px`,
    });
    let observer = createObserver();
    let resizeFrame;
    window.addEventListener('resize', () => {
        cancelAnimationFrame(resizeFrame);
        resizeFrame = requestAnimationFrame(() => {
            observer.disconnect();
            observer = createObserver();
            pendingElements.forEach(element => {
                if (!revealed.has(element)) observer.observe(element);
            });
        });
    }, { passive: true });

    const register = root => {
        const candidates = [...root.querySelectorAll(selector)];
        if (root.matches?.(selector)) candidates.unshift(root);
        candidates.forEach(element => {
            const button = element.matches(wholeElementSelector);
            if (element.matches('.m-faq__item > summary')) return;
            const exclusions = element.matches(`${decorativeSelector}, ${barSelector}`)
                ? excluded.replace('[aria-hidden="true"], ', '')
                : excluded;
            if (registered.has(element) || revealed.has(element) || element.closest(exclusions)) return;
            if (!button && (element.closest('form, button, input') || !element.textContent.trim())) return;
            const buttonParent = element.closest(wholeElementSelector);
            if (buttonParent && buttonParent !== element) return;
            if (!button && !element.matches(textBlocks) && ![...element.childNodes].some(node => node.nodeType === Node.TEXT_NODE && node.textContent.trim())) return;
            if (!button && element.querySelector(`h1, h2, h3, h4, h5, h6, p, div, article, section, ul, ol, dl, table, ${wholeElementSelector}`)) return;
            let parent = element.parentElement;
            while (parent && !registered.has(parent)) parent = parent.parentElement;
            if (parent) return;
            if (!button && element.matches('li') && element.querySelector('h1, h2, h3, h4, h5, h6, p, div, article')) return;
            registered.add(element);
            const display = getComputedStyle(element).display;
            const wholeElement = button || ['flex', 'inline-flex', 'grid', 'inline-grid'].includes(display);
            if (wholeElement) wholeElements.add(element);
            if (!motion.matches) {
                if (!wholeElement) words.set(element, splitWords(element));
                const animation = element.animate([
                    { opacity: 0 },
                    { opacity: 0 },
                ], {
                    duration: 1,
                    fill: 'both',
                });
                animation.pause();
                pending.set(element, animation);
                pendingElements.add(element);
                animations.add(animation);
                const cleanup = () => {
                    animations.delete(animation);
                    pending.delete(element);
                    pendingElements.delete(element);
                };
                animation.addEventListener('finish', () => animation.cancel(), { once: true });
                animation.addEventListener('cancel', cleanup, { once: true });
            }
            observer.observe(element);
        });
    };

    register(document.body);
    const finishForFocus = event => {
        let parent = event.target;
        while (parent && parent !== document.body) {
            pending.get(parent)?.cancel();
            if (registered.has(parent)) {
                revealed.add(parent);
                observer.unobserve(parent);
                restoreWords(parent);
                parent.getAnimations().forEach(animation => animation.cancel());
            }
            parent = parent.parentElement;
        }
    };
    document.addEventListener('focusin', finishForFocus);
    new MutationObserver(records => {
        records.forEach(record => {
            record.removedNodes.forEach(node => {
                if (node.nodeType !== Node.ELEMENT_NODE) return;
                [node, ...node.querySelectorAll(selector)].forEach(element => {
                    if (!element.isConnected) {
                        pending.get(element)?.cancel();
                        restoreWords(element);
                        observer.unobserve(element);
                        registered.delete(element);
                    }
                });
            });
            record.addedNodes.forEach(node => {
                if (node.nodeType === Node.ELEMENT_NODE) register(node);
            });
        });
    }).observe(document.body, { childList: true, subtree: true });

    window.addEventListener('beforeprint', () => {
        animations.forEach(animation => animation.cancel());
        pendingElements.forEach(element => { revealed.add(element); observer.unobserve(element); restoreWords(element); });
    });
    motion.addEventListener('change', () => {
        if (motion.matches) {
            animations.forEach(animation => animation.cancel());
            document.querySelectorAll(selector).forEach(restoreWords);
        }
    });
};

export default textReveal;
