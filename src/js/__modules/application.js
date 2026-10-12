const application = () => {
    document.querySelectorAll('[data-application]').forEach(section => {
        section.querySelector('[data-application-preview]')?.addEventListener('submit', event => event.preventDefault());
        const position = new URL(location.href).searchParams.get('position');
        const select = section.querySelector('[name="application-position"]');
        if (position && select) {
            const slug = text => text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            const option = [...select.options].find(item => item.value === position || slug(item.text) === position);
            if (option) select.value = option.value;
        }
        section.querySelectorAll('input[type="file"]').forEach(input => input.addEventListener('change', () => {
            const file = input.files[0];
            input.setCustomValidity(file && file.size > 2 * 1024 * 1024 ? 'Maximum file size is 2MB.' : '');
            const name = input.closest('.m-application__upload')?.querySelector('[data-upload-name]');
            if (name) name.textContent = file?.name ?? '';
            if (!input.checkValidity()) input.reportValidity();
        }));
    });
};
export default application;
