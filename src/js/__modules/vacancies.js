const vacancies = () => {
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
