export default function jobRole() {
    document.querySelectorAll('[data-job-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const status = button.closest('.m-job-role__card').querySelector('[data-copy-status]');
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(button.dataset.jobCopy);
                } else {
                    const input = document.createElement('textarea');
                    input.value = button.dataset.jobCopy;
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    document.body.append(input);
                    input.select();
                    const copied = document.execCommand('copy');
                    input.remove();
                    button.focus();
                    if (!copied) throw new Error('Copy failed');
                }
                status.textContent = 'Link copied';
            } catch {
                status.textContent = 'Could not copy the link. Please copy it from your address bar.';
            }
        });
    });
}
