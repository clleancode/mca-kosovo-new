export default function documentPreview() {
    document.querySelectorAll('[data-document-preview]').forEach(section => {
        const pdf = section.querySelector('[data-document-pdf]');
        const image = section.querySelector('[data-document-image]');
        const page = section.querySelector('[data-document-page]');
        const output = section.querySelector('[data-document-scale]');
        const buttons = [...section.querySelectorAll('[data-document-zoom]')];
        const status = section.querySelector('[data-document-status]');
        let zoom = 100;
        let viewer;
        if (!pdf && !image) return;
        const update = () => {
            output.textContent = `${zoom}%`;
            buttons.forEach(button => {
                button.disabled = Number(button.dataset.documentZoom) < 0 ? zoom <= 50 : zoom >= 200;
            });
            if (pdf) {
                const number = Math.max(1, Math.floor(Number(page.value) || 1));
                page.value = String(number);
                const replacement = document.createElement('iframe');
                replacement.title = pdf.getAttribute('aria-label');
                replacement.src = `${pdf.dataset.documentPdf.split('#')[0]}#page=${number}&zoom=${zoom}&toolbar=0&navpanes=0`;
                if (viewer) viewer.replaceWith(replacement);
                else pdf.parentElement.prepend(replacement);
                viewer = replacement;
                pdf.hidden = true;
                status.hidden = true;
            } else {
                image.style.width = `${60 * zoom / 100}%`;
            }
        };
        buttons.forEach(button => button.addEventListener('click', () => {
            zoom = Math.max(50, Math.min(200, zoom + Number(button.dataset.documentZoom) * 25));
            update();
        }));
        page?.addEventListener('change', update);
        page?.addEventListener('keydown', event => {
            if (event.key === 'Enter') { event.preventDefault(); update(); }
        });
        update();
    });
}
