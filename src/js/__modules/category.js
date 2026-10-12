const category = () => {
    document.querySelectorAll('[data-resource-category]').forEach(section => {
        if (section.dataset.initialized) return;
        section.dataset.initialized = 'true';
        let controller;
        section.addEventListener('click', async event => {
            const link = event.target.closest('[data-category-filter], .m-category__pagination a');
            if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            controller?.abort();
            controller = new AbortController();
            const signal = controller.signal;
            const results = section.querySelector('.m-category__results');
            results.setAttribute('aria-busy', 'true');
            try {
                const response = await fetch(link.href, { signal });
                if (!response.ok) throw new Error('Request failed');
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const updated = [...page.querySelectorAll('[data-resource-category]')].find(item => item.dataset.resourceCategory === section.dataset.resourceCategory);
                if (!updated) throw new Error('Category block missing');
                results.innerHTML = updated.querySelector('.m-category__results').innerHTML;
                const active = updated.querySelector('[data-category-filter][aria-current]');
                section.querySelectorAll('[data-category-filter]').forEach(item => {
                    item.removeAttribute('aria-current');
                    if (active && item.getAttribute('aria-label') === active.getAttribute('aria-label') && item.href === active.href) item.setAttribute('aria-current', 'true');
                });
            } catch (error) { if (error.name !== 'AbortError') location.assign(link.href); }
            finally { if (!signal.aborted) results.removeAttribute('aria-busy'); }
        });
    });
};
export default category;
