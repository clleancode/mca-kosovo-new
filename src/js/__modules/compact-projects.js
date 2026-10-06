const compactProjects = () => {
    document.querySelectorAll('.m-compact-projects__grid').forEach(grid => {
        if (grid.dataset.initialized) return;
        grid.dataset.initialized = 'true';
        const cards = [...grid.querySelectorAll('.m-compact-projects__card')];
        const activate = selected => {
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
        };
        cards.forEach(card => card.addEventListener('click', event => {
            if (!event.target.closest('a')) activate(card);
        }));
        if (cards.length) activate(cards[0]);
    });
};

export default compactProjects;
