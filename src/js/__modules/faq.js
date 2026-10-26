const faq = () => {
    document.querySelectorAll('.m-faq__items').forEach(accordion => {
        if (accordion.dataset.initialized) return;
        accordion.dataset.initialized = 'true';

        const items = [...accordion.querySelectorAll('.m-faq__item')];
        const states = new Map();

        items.forEach(item => item.removeAttribute('name'));

        const toggle = (item, expanded) => {
            const summary = item.querySelector('summary');
            const state = { expanded, animation: null };
            states.set(item, state);
            summary.setAttribute('aria-expanded', String(expanded));
            item.open = expanded;
            item.style.height = '';
            item.style.overflow = '';
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