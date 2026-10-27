const compactProjects = () => {
    document.querySelectorAll('.m-compact-projects__grid').forEach(grid => {
        if (grid.classList.contains('m-compact-projects__grid--two')) return;
        if (grid.dataset.initialized) return;
        grid.dataset.initialized = 'true';
        const cards = [...grid.querySelectorAll('.m-compact-projects__card')];
        let animations = [];
        let anchorFrame;
        const activate = (selected, animate = false) => {
            const mobile = window.matchMedia('(max-width: 767px)').matches;
            const smooth = animate && mobile && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const heights = cards.map(card => card.getBoundingClientRect().height);
            const anchorTop = selected.getBoundingClientRect().top;
            cancelAnimationFrame(anchorFrame);
            animations.forEach(animation => animation.cancel());
            animations = [];
            cards.forEach(card => {
                const active = card === selected;
                const content = card.querySelector('.m-compact-projects__feature-content');
                const toggle = card.querySelector('.m-compact-projects__open');
                if (!active && content.contains(document.activeElement)) toggle.focus();
                card.classList.toggle('m-compact-projects__card--feature', active);
                card.classList.toggle('m-compact-projects__card--compact', !active);
                content.hidden = !active;
                toggle.setAttribute('aria-expanded', String(active));
            });
            if (smooth) {
                animations = cards.map((card, index) => card.animate([
                    { height: `${heights[index]}px` },
                    { height: `${card.getBoundingClientRect().height}px` },
                ], { duration: 800, easing: 'cubic-bezier(.4, 0, .2, 1)' }));
                const keepAnchor = () => {
                    const shift = selected.getBoundingClientRect().top - anchorTop;
                    if (Math.abs(shift) > .5) window.scrollBy({ top: shift, behavior: 'instant' });
                    if (animations.some(animation => animation.playState === 'running')) {
                        anchorFrame = requestAnimationFrame(keepAnchor);
                    }
                };
                anchorFrame = requestAnimationFrame(keepAnchor);
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
