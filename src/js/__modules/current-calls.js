import eReveal from './e-reveal';

const currentCalls = () => {
    const section = document.querySelector('[data-current-calls]');

    if (!section || section.dataset.initialized) {
        return;
    }

    section.dataset.initialized = 'true';

    let controller;

    const load = async (url, updateHistory = true) => {
        controller?.abort();
        controller = new AbortController();
        const { signal } = controller;

        section.setAttribute('aria-busy', 'true');
        section.querySelector('.m-current-calls__error')?.remove();

        try {
            const response = await fetch(url, {
                signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error('Unable to load calls');
            }

            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const updated = page.querySelector('[data-current-calls]');

            if (!updated) {
                throw new Error('Current calls section missing');
            }

            section.innerHTML = updated.innerHTML;
            eReveal();

            if (updateHistory) {
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                const message = document.createElement('p');
                message.className = 'm-current-calls__error a-text a-text--s';
                message.setAttribute('role', 'alert');
                message.textContent = 'Could not load calls. Please try again.';
                section.querySelector('.m-current-calls')?.append(message);
            }
        } finally {
            if (!signal.aborted) {
                section.removeAttribute('aria-busy');
            }
        }
    };

    section.addEventListener('click', (event) => {
        const link = event.target.closest('.m-current-calls__tab');

        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        load(link.href);
    });

    window.addEventListener('popstate', () => load(window.location.href, false));
};

export default currentCalls;
