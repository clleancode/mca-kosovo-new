export default function singleHero() {
    document.querySelectorAll('[data-single-hero-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const status = button.closest('.m-single-hero__share')?.querySelector('[data-single-hero-copy-status]');

            if (!status) return;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(button.dataset.singleHeroCopy);
                } else {
                    const input = document.createElement('textarea');
                    input.value = button.dataset.singleHeroCopy;
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    document.body.append(input);
                    input.select();
                    const copied = document.execCommand('copy');
                    input.remove();
                    button.focus();
                    if (!copied) throw new Error('Copy failed');
                }

                status.textContent = button.dataset.copySuccess;
            } catch {
                status.textContent = button.dataset.copyError;
            }
        });
    });
}
