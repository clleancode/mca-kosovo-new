export default function allAlbums() {
    document.querySelectorAll('[data-all-albums]').forEach((section) => {
        const cards = [...section.querySelectorAll('[data-album-card]')];
        const filters = [...section.querySelectorAll('[data-album-category]')];
        const year = section.querySelector('[data-album-year]');
        let category = filters.find((filter) => filter.hasAttribute('aria-current'))?.dataset.albumCategory || 'all';
        const update = () => {
            let visible = 0;
            cards.forEach((card) => {
                const matches = (category === 'all' || card.dataset.categories.split(' ').includes(category))
                    && (year.value === 'all' || card.dataset.year === year.value);
                card.hidden = !matches;
                const featured = matches && visible === 0;
                card.classList.toggle('is-featured', featured);
                const heading = card.querySelector('h2');
                heading.classList.toggle('a-heading--h3', featured);
                heading.classList.toggle('a-heading--h5', !featured);
                card.querySelector('[data-album-photo-label]').hidden = !featured;
                card.querySelector('[data-album-label]').hidden = featured;
                if (matches) visible++;
            });
            section.querySelector('[data-album-empty]').hidden = visible > 0;
        };
        filters.forEach((link) => link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            category = link.dataset.albumCategory;
            filters.forEach((filter) => {
                if (filter === link) filter.setAttribute('aria-current', 'true');
                else filter.removeAttribute('aria-current');
            });
            update();
        }));
        year.addEventListener('change', update);
        update();
    });
}
