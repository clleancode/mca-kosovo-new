import eReveal from './e-reveal';

const allDocuments = () => {
    document.querySelectorAll('[data-all-documents]').forEach((section, sectionIndex) => {
        if (section.dataset.initialized) return;
        section.dataset.initialized = 'true';
        const results = section.querySelector('.m-all-documents__results');
        const sort = section.querySelector('[data-documents-sort]');
        let selected = section.dataset.selectedFilter;
        let controller;
        let loading = false;
        const filters = section.querySelector('.m-all-documents__filters');
        const mobile = matchMedia('(max-width: 767px)');
        let toggle;
        const updateLabel = () => {
            const active = filters?.querySelector('[aria-current]') || filters?.querySelector('a');
            if (toggle && active) toggle.textContent = active.textContent.trim();
        };
        const setOpen = open => {
            section.classList.toggle('is-document-filters-open', open);
            toggle?.setAttribute('aria-expanded', String(open));
            if (filters) filters.inert = mobile.matches && !open;
        };
        if (filters) {
            const panel = document.createElement('div');
            panel.className = 'm-all-documents__filter-panel';
            filters.before(panel);
            panel.append(filters);
            toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'm-all-documents__filter-toggle a-text a-text--m';
            filters.id ||= `document-filters-${sectionIndex}`;
            toggle.setAttribute('aria-controls', filters.id);
            panel.before(toggle);
            section.classList.add('has-document-filters-dropdown');
            updateLabel();
            setOpen(false);
            toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
            mobile.addEventListener('change', () => setOpen(false));
            filters.addEventListener('keydown', event => {
                if (event.key === 'Escape' && mobile.matches) {
                    setOpen(false);
                    toggle.focus();
                }
            });
        }
        const load = async (url, append = false) => {
            if (append && loading) return;
            controller?.abort();
            controller = new AbortController();
            const signal = controller.signal;
            loading = true;
            results.setAttribute('aria-busy', 'true');
            const message = section.querySelector('[data-documents-message]');
            message.textContent = '';
            try {
                const response = await fetch(url, { signal });
                if (!response.ok) throw new Error('Request failed');
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const updated = [...page.querySelectorAll('[data-all-documents]')].find(item => item.dataset.allDocuments === section.dataset.allDocuments);
                if (!updated) throw new Error('Document block missing');
                const incoming = updated.querySelector('.m-all-documents__results');
                if (append) {
                    const grid = results.querySelector('.m-all-documents__grid');
                    const ids = new Set([...grid.children].map(card => card.dataset.documentId));
                    incoming.querySelectorAll('[data-document-id]').forEach(card => {
                        if (!ids.has(card.dataset.documentId)) grid.append(card);
                    });
                    eReveal();
                    results.querySelector('.m-all-documents__more').innerHTML = incoming.querySelector('.m-all-documents__more').innerHTML;
                } else {
                    results.innerHTML = incoming.innerHTML;
                    eReveal();
                }
                selected = updated.dataset.selectedFilter;
                section.querySelectorAll('[data-document-filter]').forEach(link => {
                    link.removeAttribute('aria-current');
                    if (link.dataset.documentFilter === selected) link.setAttribute('aria-current', 'true');
                });
                updateLabel();
            } catch (error) {
                if (error.name !== 'AbortError') message.textContent = 'Could not load documents. Please try again.';
            } finally {
                if (!signal.aborted) { results.removeAttribute('aria-busy'); loading = false; }
            }
        };
        const filterUrl = () => {
            const url = new URL(location.href);
            url.searchParams.set(section.dataset.filterKey, selected);
            url.searchParams.set(section.dataset.sortKey, sort.value);
            url.searchParams.delete(section.dataset.pageKey);
            return url.href;
        };
        section.addEventListener('click', event => {
            const link = event.target.closest('[data-document-filter], [data-documents-more]');
            if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            if (link.hasAttribute('data-documents-more')) load(link.href, true);
            else {
                selected = link.dataset.documentFilter;
                if (mobile.matches && toggle) {
                    setOpen(false);
                    toggle.focus({ preventScroll: true });
                }
                load(filterUrl());
            }
        });
        sort.addEventListener('change', () => load(filterUrl()));
    });
};
export default allDocuments;
