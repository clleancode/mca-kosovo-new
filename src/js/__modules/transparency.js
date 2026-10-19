export default function transparency() {
    document.querySelectorAll('[data-transparency-toggle]').forEach((container) => {
        const list = container.querySelector('ul');
        const button = container.querySelector('button');
        const label = button.querySelector('[data-toggle-label]');
        const update = (expanded) => {
            list.hidden = !expanded;
            button.setAttribute('aria-expanded', String(expanded));
            label.textContent = expanded ? button.dataset.less : button.dataset.more;
        };
        button.setAttribute('aria-controls', list.id);
        update(false);
        button.hidden = false;
        button.addEventListener('click', () => update(list.hidden));
    });
}
