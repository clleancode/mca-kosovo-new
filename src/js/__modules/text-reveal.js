// Prepare text before viewport entry to avoid a visible flash when scrolling.
const textReveal = () => {
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (!('IntersectionObserver' in window) || !Element.prototype.animate) return;

    const textBlocks = 'h1, h2, h3, h4, h5, h6, p, blockquote, figcaption, li, dt, dd, address, summary, td, th, .a-heading, .a-text';
    const buttonSelector = 'button, .a-btn, [class*="a-btn--"], .m-transparency__documents .a-transparency-download, [role="button"], input[type="submit"], input[type="button"]';
    // Keep live countdown numbers intact: their text is replaced every second.
    const faqIconSelector = '.m-faq__item > summary > span:last-child';
    // Use the outer badge so its background, pseudo-element dot and icons
    // reveal together. Compact project category text lives inside its number pill.
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
    const decorativeSelector = `${faqIconSelector}, .m-how-to-apply__number, .m-contact-form__icon`;
    const contactFieldSelector = '.m-contact-form__field, .m-contact-form__topics, .m-contact-form__privacy, .m-contact-form__submit';
    const wholeElementSelector = `${buttonSelector}, ${badgeSelector}, .m-compact__time, ${decorativeSelector}, .m-contact-form__directions, ${contactFieldSelector}`;
    const selector = `${textBlocks}, a, span, time, div, ${wholeElementSelector}`;
    // Live regions can contain ordinary articles (news, search, filtered cards).
    // Exclude feedback messages themselves, rather than all live-region content.
    const excluded = '.o-header, .o-cookie-banner, #wpadminbar, select, textarea, script, style, svg, [role="status"], [role="alert"], .screen-reader-text, .sr-only, [aria-hidden="true"], .mca-reveal-word, [data-aos], [data-text-reveal="off"], .a-btn--fixed-left, .a-btn--fixed, .swiper-button-prev, .swiper-button-next, .swiper-pagination, .m-hero__pagination, .m-hero__current, .m-hero__total';
    const registered = new WeakSet();
    const revealed = new WeakSet();
    const animations = new Set();
    const pending = new WeakMap();
    const words = new WeakMap();
    const wholeElements = new WeakSet();
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

    const observer = new IntersectionObserver(entries => {
        updateScrollDirection();
        let order = 0;
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
            if (wholeElements.has(target)) {
                // Move the whole button, including its background and icon.
                // Individual translate leaves existing hover transforms intact.
                animation.cancel();
                const style = getComputedStyle(target);
                const effect = target.animate([
                    { opacity: 0, translate: '0 10px' },
                    { opacity: style.opacity, translate: style.translate },
                ], {
                    duration: 1200,
                    delay: Math.min(order++ * 80, 240),
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    fill: 'both',
                });
                animations.add(effect);
                effect.addEventListener('finish', () => effect.cancel(), { once: true });
                effect.addEventListener('cancel', () => animations.delete(effect), { once: true });
                return;
            }
            const spans = words.get(target) || [];
            // Measure the actual wrapped lines at entry, including the current
            // mobile width and font metrics. Every word on a line moves together.
            const positions = spans.map(span => span.getBoundingClientRect().top);
            const lines = [...positions].sort((a, b) => a - b).reduce((result, top) => {
                if (!result.length || top - result[result.length - 1] > 5) result.push(top);
                return result;
            }, []);
            const blockDelay = Math.min(order++ * 80, 240);
            const heading = target.matches('h1, h2, h3, h4, h5, h6, .a-heading');
            // Start all line effects in the same frame before releasing the block.
            const running = spans.map((span, index) => {
                const line = Math.max(0, lines.findIndex(top => Math.abs(top - positions[index]) <= 5));
                const effect = span.animate([
                    { opacity: 0, transform: 'translateY(0.65em)', clipPath: 'inset(0 0 100% 0)' },
                    { opacity: 1, transform: 'translateY(0)', clipPath: 'inset(0 0 0% 0)' },
                ], {
                    duration: heading ? 1300 : 1100,
                    delay: blockDelay + line * 160,
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
    }, { threshold: 0, rootMargin: '0px 0px 40px 0px' });

    const register = root => {
        const candidates = [...root.querySelectorAll(selector)];
        if (root.matches?.(selector)) candidates.unshift(root);
        candidates.forEach(element => {
            const button = element.matches(wholeElementSelector);
            // FAQ icons are decorative (aria-hidden) but still need a visual
            // reveal. Keep summary layout intact and reveal its children separately.
            if (element.matches('.m-faq__item > summary')) return;
            const exclusions = element.matches(decorativeSelector)
                ? excluded.replace('[aria-hidden="true"], ', '')
                : excluded;
            if (registered.has(element) || revealed.has(element) || element.closest(exclusions)) return;
            if (!button && (element.closest('form, button, input') || !element.textContent.trim())) return;
            const buttonParent = element.closest(wholeElementSelector);
            if (buttonParent && buttonParent !== element) return;
            // Catch unclassified captions and links without treating section or
            // card wrappers as one large text block.
            if (!button && !element.matches(textBlocks) && ![...element.childNodes].some(node => node.nodeType === Node.TEXT_NODE && node.textContent.trim())) return;
            if (!button && element.querySelector(`h1, h2, h3, h4, h5, h6, p, div, article, section, ul, ol, dl, table, ${wholeElementSelector}`)) return;
            // Animate the outer text block only; nested spans and links stay intact.
            let parent = element.parentElement;
            while (parent && !registered.has(parent)) parent = parent.parentElement;
            if (parent) return;
            // Lists containing cards are layout containers, not text blocks.
            if (element.matches('li') && element.querySelector('h1, h2, h3, h4, h5, h6, p, div, article')) return;
            registered.add(element);
            // Direct word spans become separate flex/grid items, causing
            // space-between to pull words apart until the reveal is restored.
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
                animations.add(animation);
                const cleanup = () => {
                    animations.delete(animation);
                    pending.delete(element);
                };
                animation.addEventListener('finish', () => animation.cancel(), { once: true });
                animation.addEventListener('cancel', cleanup, { once: true });
            }
            observer.observe(element);
        });
    };

    register(document.body);
    // Filters and pagination replace cards without reloading the page.
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

    motion.addEventListener('change', () => {
        if (motion.matches) {
            animations.forEach(animation => animation.cancel());
            document.querySelectorAll(selector).forEach(restoreWords);
        }
    });
};

export default textReveal;
