const compactProjects = () => {
    document.querySelectorAll('.m-compact-projects__grid').forEach(grid => {
        if (grid.classList.contains('m-compact-projects__grid--two')) return;
        if (grid.dataset.initialized) return;
        grid.dataset.initialized = 'true';
        const cards = [...grid.querySelectorAll('.m-compact-projects__card')];
        let animations = [];
        const activate = (selected, animate = false) => {
            const mobile = window.matchMedia('(max-width: 767px)').matches;
            const smooth = animate && mobile && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const heights = cards.map(card => card.getBoundingClientRect().height);
            animations.forEach(animation => animation.cancel());
            animations = [];
            cards.forEach(card => {
                const active = card === selected;
                const content = card.querySelector('.m-compact-projects__feature-content');
                const toggle = card.querySelector('.m-compact-projects__open');
                if (!active && content.contains(document.activeElement)) toggle.focus({ preventScroll: true });
                card.classList.toggle('m-compact-projects__card--feature', active);
                card.classList.toggle('m-compact-projects__card--compact', !active);
                content.hidden = !active;
                toggle.setAttribute('aria-expanded', String(active));
            });
            if (smooth) {
                const targetHeights = cards.map(card => card.getBoundingClientRect().height);
                animations = cards.map((card, index) => card.animate([
                    { height: `${heights[index]}px` },
                    { height: `${targetHeights[index]}px` },
                ], { duration: 800, easing: 'cubic-bezier(.4, 0, .2, 1)' }));
            }
        };
        cards.forEach(card => card.addEventListener('click', event => {
            if (event.target.closest('a')) return;
            if (event.target.closest('button')) {
                activate(card, true);
                return;
            }
            if (card.classList.contains('m-compact-projects__card--feature')) {
                const link = card.querySelector('.m-compact-projects__button');
                if (link && !window.getSelection()?.toString()) link.click();
                return;
            }
            activate(card, true);
        }));
        if (cards.length) activate(cards[0]);
    });
};

export default compactProjects;
