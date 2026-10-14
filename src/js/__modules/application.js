const application = () => {
    // CF7 leaves the form submitting when its REST request rejects.
    // Keep CF7's submission/validation flow and recover application forms only.
    if (window.wpcf7?.submit && !window.wpcf7.submit.applicationErrorHandling) {
        const submit = window.wpcf7.submit;
        const guardedSubmit = async (form, options) => {
            if (!form.closest('[data-application]')) return submit(form, options);
            try {
                return await submit(form, options);
            } catch (error) {
                console.error('Application form submission failed:', error);
                const previousStatus = form.getAttribute('data-status');
                form.classList.remove(previousStatus, 'submitting', 'resetting', 'validating');
                form.classList.add('aborted');
                form.setAttribute('data-status', 'aborted');
                if (form.wpcf7) form.wpcf7.status = 'aborted';
                const message = form.closest('[data-application]').dataset.applicationError;
                form.querySelectorAll('.wpcf7-response-output').forEach(output => {
                    output.textContent = message;
                    output.removeAttribute('aria-hidden');
                });
                const liveRegion = form.closest('.wpcf7')?.querySelector('.screen-reader-response [role="status"]');
                if (liveRegion) liveRegion.textContent = message;
                form.dispatchEvent(new CustomEvent('wpcf7statuschanged', {
                    bubbles: true,
                    detail: { status: 'aborted', prevStatus: previousStatus, contactFormId: form.wpcf7?.id },
                }));
            }
        };
        guardedSubmit.applicationErrorHandling = true;
        window.wpcf7.submit = guardedSubmit;
    }
    // CF7 resets asynchronously after a successful send. A failed refill
    // request must not leave the form resetting or lose its success feedback.
    if (window.wpcf7?.reset && !window.wpcf7.reset.applicationErrorHandling) {
        const reset = window.wpcf7.reset;
        const guardedReset = async form => {
            if (!form.closest('[data-application]')) return reset(form);
            try {
                return await reset(form);
            } catch (error) {
                console.error('Application form reset failed:', error);
                const sent = Boolean(form.wpcf7?.resetOnMailSent);
                const status = sent ? 'sent' : 'init';
                const previousStatus = form.getAttribute('data-status');
                form.classList.remove(previousStatus, 'resetting', 'submitting', 'validating');
                form.classList.add(status);
                form.setAttribute('data-status', status);
                if (form.wpcf7) {
                    form.wpcf7.status = status;
                    delete form.wpcf7.resetOnMailSent;
                }
                form.dispatchEvent(new CustomEvent('wpcf7statuschanged', {
                    bubbles: true,
                    detail: { status, prevStatus: previousStatus, contactFormId: form.wpcf7?.id },
                }));
            }
        };
        guardedReset.applicationErrorHandling = true;
        window.wpcf7.reset = guardedReset;
    }
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
        section.addEventListener('wpcf7mailsent', () => {
            section.querySelectorAll('[data-upload-name]').forEach(name => { name.textContent = ''; });
            section.querySelectorAll('input[type="file"]').forEach(input => input.setCustomValidity(''));
        });
    });
};
export default application;
