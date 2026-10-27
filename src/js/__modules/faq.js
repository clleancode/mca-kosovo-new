import gsap from 'gsap';

const ANIMATED_PROPS = 'height,paddingTop,opacity,overflow';

const prefersReducedMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

const faq = () => {
    document.querySelectorAll('.m-faq__items').forEach(accordion => {
        if (accordion.dataset.initialized) return;
        accordion.dataset.initialized = 'true';

        const items = [...accordion.querySelectorAll('.m-faq__item')];
        const states = new Map();

        items.forEach(item => item.removeAttribute('name'));

        const toggle = (item, expanded) => {
            const summary = item.querySelector('summary');
            const answer = item.querySelector('.m-faq__answer');
            const state = states.get(item);

            state.expanded = expanded;
            state.animation?.kill();
            summary.setAttribute('aria-expanded', String(expanded));
            item.classList.toggle('is-collapsing', !expanded && item.open);

            if (!answer || prefersReducedMotion()) {
                item.classList.remove('is-collapsing');
                item.open = expanded;
                gsap.set(answer, { clearProps: ANIMATED_PROPS });
                return;
            }

            if (expanded) {
                if (!item.open) gsap.set(answer, { height: 0, paddingTop: 0, opacity: 0 });
                item.open = true;
                state.animation = gsap.to(answer, {
                    height: 'auto',
                    paddingTop: state.padding,
                    opacity: 1,
                    overflow: 'hidden',
                    duration: 0.5,
                    ease: 'power3.out',
                    clearProps: ANIMATED_PROPS,
                });
                return;
            }

            state.animation = gsap.to(answer, {
                height: 0,
                paddingTop: 0,
                opacity: 0,
                overflow: 'hidden',
                duration: 0.4,
                ease: 'power3.inOut',
                onComplete: () => {
                    item.classList.remove('is-collapsing');
                    item.open = false;
                    gsap.set(answer, { clearProps: ANIMATED_PROPS });
                },
            });
        };

        items.forEach(item => {
            const summary = item.querySelector('summary');
            const answer = item.querySelector('.m-faq__answer');

            states.set(item, {
                expanded: item.open,
                animation: null,
                padding: answer ? parseFloat(getComputedStyle(answer).paddingTop) || 0 : 0,
            });
            summary.setAttribute('aria-expanded', String(item.open));

            summary.addEventListener('click', event => {
                event.preventDefault();

                const expanded = !states.get(item).expanded;

                if (expanded) {
                    items.forEach(other => {
                        if (other !== item && states.get(other).expanded) {
                            toggle(other, false);
                        }
                    });
                }

                toggle(item, expanded);
            });
        });
    });
};

export default faq;
