import eReveal from './e-reveal';

const vacancies = () => {
    document.querySelectorAll('[data-vacancies-archive]').forEach(section => {
        if (section.dataset.initialized) return;
        section.dataset.initialized = 'true';
        let controller;
        const load = async (url, updateHistory = true) => {
            controller?.abort();
            controller = new AbortController();
            const { signal } = controller;
            section.setAttribute('aria-busy', 'true');
            try {
                const response = await fetch(url, { signal });
                if (!response.ok) throw new Error('Unable to load vacancies');
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const updated = [...page.querySelectorAll('[data-vacancies-archive]')].find(item => item.dataset.vacanciesArchive === section.dataset.vacanciesArchive);
                if (!updated) throw new Error('Vacancies archive missing');
                const focused = document.activeElement;
                const filterHref = focused?.closest('[data-vacancy-filter]')?.href;
                const searchFocused = focused?.matches('input[type="search"]');
                section.innerHTML = updated.innerHTML;
                eReveal();
                if (updateHistory) window.history.pushState({}, '', url);
                if (searchFocused) section.querySelector('input[type="search"]')?.focus({ preventScroll: true });
                else if (filterHref) [...section.querySelectorAll('[data-vacancy-filter]')].find(link => link.href === filterHref)?.focus({ preventScroll: true });
            } catch (error) {
                if (error.name !== 'AbortError') window.location.assign(url);
            } finally {
                if (!signal.aborted) section.removeAttribute('aria-busy');
            }
        };
        section.addEventListener('click', event => {
            const link = event.target.closest('[data-vacancy-filter], .m-vacancies__pagination a');
            if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            load(link.href);
        });
        section.addEventListener('submit', event => {
            const form = event.target.closest('[data-vacancy-search]');
            if (!form) return;
            event.preventDefault();
            const url = new URL(form.action);
            for (const [key, value] of new FormData(form)) url.searchParams.set(key, value);
            load(url.href);
        });
        window.addEventListener('popstate', () => load(window.location.href, false));
    });
    document.querySelectorAll('[data-vacancies]').forEach(section => {
        if (section.dataset.initialized) return;
        section.dataset.initialized = 'true';
        const tabs = [...section.querySelectorAll('[data-vacancy-tab]')];
        const panels = [...section.querySelectorAll('[data-vacancy-panel]')];
        const activate = state => {
            tabs.forEach(tab => {
                if (tab.dataset.vacancyTab === state) tab.setAttribute('aria-current', 'true');
                else tab.removeAttribute('aria-current');
            });
            panels.forEach(panel => { panel.hidden = panel.dataset.vacancyPanel !== state; });
        };
        section.addEventListener('click', event => {
            const link = event.target.closest('[data-vacancy-tab], [data-vacancy-switch]');
            if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            activate(link.dataset.vacancyTab ?? link.dataset.vacancySwitch);
        });
    });
};
export default vacancies;
