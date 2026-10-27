const filter = () => {
    document.querySelectorAll('[data-procurement-filter]').forEach(form => {
        if (form.dataset.initialized) return;
        form.dataset.initialized = 'true';
        const choices = [...form.querySelectorAll('[data-filter-choice]')];
        const resetButton = form.querySelector('button[type="reset"]');
        const names = { notice_search: 'notice_search', status: 'notice_status', notice_type: 'notice_type', project: 'notice_project', sort: 'notice_sort' };
        const defaults = { notice_search: '', status: 'all', notice_type: 'all', project: '', sort: 'newest' };
        const toggleReset = () => {
            if (!resetButton) return;
            const active = Object.entries(defaults).some(([name, value]) => {
                const field = form.elements.namedItem(name);
                return field && field.value.trim() !== value;
            });
            resetButton.hidden = !active;
        };
        const sync = () => {
            const params = new URL(location.href).searchParams;
            Object.entries(names).forEach(([name, query]) => {
                const field = form.elements.namedItem(name);
                if (!field) return;
                field.value = params.get(query) ?? defaults[name];
            });
            choices.forEach(button => button.setAttribute('aria-pressed', String(form.elements.namedItem(button.dataset.filterChoice).value === button.dataset.value)));
            toggleReset();
        };
        const emit = () => {
            toggleReset();
            const data = new FormData(form);
            const params = {};
            Object.entries(names).forEach(([name, query]) => { params[query] = String(data.get(name) ?? ''); });
            form.dispatchEvent(new CustomEvent('procurement:filter', { bubbles: true, detail: params }));
        };
        choices.forEach(button => button.addEventListener('click', () => {
            const name = button.dataset.filterChoice;
            choices.filter(choice => choice.dataset.filterChoice === name).forEach(choice => choice.setAttribute('aria-pressed', String(choice === button)));
            form.elements.namedItem(name).value = button.dataset.value;
            emit();
        }));
        form.querySelectorAll('select').forEach(select => select.addEventListener('change', emit));
        form.elements.namedItem('notice_search')?.addEventListener('input', toggleReset);
        form.addEventListener('submit', event => { event.preventDefault(); emit(); });
        form.addEventListener('reset', event => {
            event.preventDefault();
            Object.entries(defaults).forEach(([name, value]) => {
                const field = form.elements.namedItem(name);
                if (field) field.value = value;
            });
            choices.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.value === 'all')));
            emit();
        });
        window.addEventListener('popstate', sync);
        sync();
    });
};
export default filter;
