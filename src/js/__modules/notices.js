const notices = () => {
    const section = document.querySelector('[data-notices]');

    if (!section || section.dataset.initialized) {
        return;
    }

    section.dataset.initialized = 'true';

    let controller;

    const load = async (url, scroll = false, history = true) => {
        controller?.abort();
        controller = new AbortController();

        const signal = controller.signal;
        const results = section.querySelector('.m-notices__results');

        results?.setAttribute('aria-busy', 'true');
        section.querySelector('.m-notices__error')?.remove();

        try {
            const response = await fetch(url, {
                signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error('Unable to load notices');
            }

            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const updated = page.querySelector('[data-notices]');

            if (!updated) {
                throw new Error('Notice block missing');
            }

            section.innerHTML = updated.innerHTML;

            if (history) {
                window.history.pushState({}, '', url);
            }

            if (scroll) {
                const headerBottom = document.querySelector('.o-header')?.getBoundingClientRect().bottom ?? 0;
                const offset = Math.max(0, headerBottom) + 16;
                const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

                window.scrollTo({
                    top: Math.max(0, window.scrollY + section.getBoundingClientRect().top - offset),
                    behavior: reducedMotion ? 'auto' : 'smooth',
                });
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                const message = document.createElement('p');

                message.className = 'm-notices__error';
                message.setAttribute('role', 'alert');
                message.textContent = 'Could not load notices. Please try again.';

                section.querySelector('.m-notices')?.append(message);
            }
        } finally {
            if (!signal.aborted) {
                results?.removeAttribute('aria-busy');
            }
        }
    };

    document.addEventListener('procurement:filter', (event) => {
        const url = new URL(location.href);

        Object.entries(event.detail).forEach(([name, value]) => {
            if (!value || value === 'all' || (name === 'notice_sort' && value === 'newest')) {
                url.searchParams.delete(name);
            } else {
                url.searchParams.set(name, value);
            }
        });

        url.searchParams.delete('notice_page');

        load(url.href);
    });

    section.addEventListener('click', (event) => {
        const link = event.target.closest('.m-notices__pagination a');

        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        load(link.href, true);
    });

    window.addEventListener('popstate', () => load(location.href, false, false));
};

export default notices;