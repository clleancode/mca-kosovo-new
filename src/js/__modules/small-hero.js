const smallHero = () => {
    document.querySelectorAll('[data-document-search]').forEach(form => {
        form.addEventListener('submit', event => {
            const input = form.querySelector('[name="s"]');
            input.value = input.value.trim();
            if (!input.checkValidity()) { event.preventDefault(); input.reportValidity(); }
        });
    });
};
export default smallHero;
