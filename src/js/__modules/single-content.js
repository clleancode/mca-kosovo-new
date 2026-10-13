export default function singleContent() {
    document.querySelectorAll('.m-single-content__toc').forEach((navigation) => {
        const links = [...navigation.querySelectorAll('[data-single-content-toc-link]')];
        const headings = links
            .map((link) => document.getElementById(decodeURIComponent(link.hash.slice(1))))
            .filter(Boolean);

        if (!headings.length) return;

        const updateActiveLink = () => {
            const activationPoint = Math.min(window.innerHeight * 0.3, 220);
            let activeHeading = headings[0];

            headings.forEach((heading) => {
                if (heading.getBoundingClientRect().top <= activationPoint) {
                    activeHeading = heading;
                }
            });

            links.forEach((link, index) => {
                if (headings[index] === activeHeading) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        };

        let animationFrame = 0;
        const scheduleUpdate = () => {
            if (animationFrame) return;
            animationFrame = window.requestAnimationFrame(() => {
                animationFrame = 0;
                updateActiveLink();
            });
        };

        updateActiveLink();
        window.addEventListener('scroll', scheduleUpdate, { passive: true });
        window.addEventListener('resize', scheduleUpdate, { passive: true });
    });

    document.querySelectorAll('[data-single-content-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const status = button.closest('.m-single-content__share')?.querySelector('[data-single-content-copy-status]');

            if (!status) return;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(button.dataset.singleContentCopy);
                } else {
                    const input = document.createElement('textarea');
                    input.value = button.dataset.singleContentCopy;
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
