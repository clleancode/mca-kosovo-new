const faq = () => {
    document.querySelectorAll('.m-faq__items').forEach(accordion => {
        if (accordion.dataset.initialized) return;
        accordion.dataset.initialized = 'true';

        const items = [...accordion.querySelectorAll('.m-faq__item')];
        const states = new Map();

        items.forEach(item => item.removeAttribute('name'));

        const toggle = (item, expanded) => {
            const summary = item.querySelector('summary');
            const currentHeight = item.getBoundingClientRect().height;
            const previous = states.get(item);

            previous?.animation?.cancel();

            const state = { expanded, animation: null };
            states.set(item, state);

            summary.setAttribute('aria-expanded', String(expanded));

            if (matchMedia('(prefers-reduced-motion: reduce)').matches) {
                item.open = expanded;
                item.style.height = '';
                item.style.overflow = '';
                return;
            }

            item.style.height = '';
            item.open = true;

            const styles = getComputedStyle(item);
            const closedHeight =
                summary.getBoundingClientRect().height +
                parseFloat(styles.paddingTop) +
                parseFloat(styles.paddingBottom) +
                parseFloat(styles.borderTopWidth) +
                parseFloat(styles.borderBottomWidth);
            const targetHeight = expanded
                ? item.getBoundingClientRect().height
                : closedHeight;

            item.style.height = `${currentHeight}px`;
            item.style.overflow = 'hidden';

            state.animation = item.animate(
                [
                    { height: `${currentHeight}px` },
                    { height: `${targetHeight}px` },
                ],
                { duration: 300, easing: 'ease-in-out' }
            );

            state.animation.onfinish = () => {
                if (states.get(item) !== state) return;

                item.open = expanded;
                item.style.height = '';
                item.style.overflow = '';
                state.animation = null;
            };
        };

        items.forEach(item => {
            const summary = item.querySelector('summary');

            states.set(item, { expanded: item.open, animation: null });
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