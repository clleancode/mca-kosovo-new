const filter = () => {
    document.querySelectorAll('[data-procurement-filter]').forEach((form) => {
        if (form.dataset.initialized) {
            return;
        }
        form.dataset.initialized = 'true';
        const choices = [...form.querySelectorAll('[data-filter-choice]')];
        const selectChoice = (selected) => {
            const name = selected.dataset.filterChoice;
            choices
                .filter((button) => button.dataset.filterChoice === name)
                .forEach((button) => {
                    button.setAttribute('aria-pressed', String(button === selected));
                });
            form.elements.namedItem(name).value = selected.dataset.value;
        };
        choices.forEach((button) => {
            button.addEventListener('click', () => selectChoice(button));
        });
        form.addEventListener('reset', () => {
            choices
                .filter((button) => button.dataset.value === 'all')
                .forEach(selectChoice);
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
        });
    });
};
export default filter;