const MIN_ZOOM = 50;
const MAX_ZOOM = 200;
const ZOOM_STEP = 25;
const IMAGE_WIDTH = 60;
const PDF_CDN = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.8.69';

const pdfHash = (url, page, zoom) => {
    const base = url.split('#')[0];
    const params = zoom === 100
        ? `page=${page}&view=FitH&toolbar=0&navpanes=0`
        : `page=${page}&zoom=${zoom}&toolbar=0&navpanes=0`;
    return `${base}#${params}`;
};

const isTouchDevice = () => matchMedia('(pointer: coarse)').matches;

let pdfjsModule;

const loadPdfjs = () => {
    pdfjsModule ??= import(/* @vite-ignore */ `${PDF_CDN}/legacy/build/pdf.mjs`).then(module => {
        module.GlobalWorkerOptions.workerSrc = `${PDF_CDN}/legacy/build/pdf.worker.min.mjs`;
        return module;
    });
    return pdfjsModule;
};

export default function documentPreview() {
    document.querySelectorAll('[data-document-preview]').forEach(section => {
        const pdf = section.querySelector('[data-document-pdf]');
        const image = section.querySelector('[data-document-image]');
        const page = section.querySelector('[data-document-page]');
        const output = section.querySelector('[data-document-scale]');
        const buttons = [...section.querySelectorAll('[data-document-zoom]')];
        const status = section.querySelector('[data-document-status]');
        const embed = section.querySelector('[data-document-embed]');
        const stage = section.querySelector('.m-document-preview__stage');
        if (!pdf && !image) return;

        let zoom = 100;
        let viewer;
        let renderToken = 0;

        const pageNumber = () => Math.max(1, Math.floor(Number(page?.value) || 1));

        const scrollToPage = number => {
            const target = embed?.querySelector(`[data-page="${number}"]`);
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const showPdfIframe = () => {
            const number = pageNumber();
            if (page) page.value = String(number);
            const replacement = document.createElement('iframe');
            replacement.title = pdf.getAttribute('aria-label');
            replacement.src = pdfHash(pdf.dataset.documentPdf, number, zoom);
            embed?.querySelector('[data-document-canvas]')?.remove();
            if (viewer?.tagName === 'IFRAME') viewer.replaceWith(replacement);
            else {
                embed?.insertBefore(replacement, status);
                viewer?.remove();
            }
            viewer = replacement;
            pdf.hidden = true;
            if (status) status.hidden = true;
        };

        const renderCanvasPages = async () => {
            const token = ++renderToken;
            const url = pdf.dataset.documentPdf;

            if (status) {
                status.hidden = false;
                status.textContent = 'Loading document…';
            }

            if (viewer?.tagName === 'IFRAME') {
                viewer.remove();
                viewer = null;
            }

            let canvasHost = embed?.querySelector('[data-document-canvas]');
            if (!canvasHost) {
                canvasHost = document.createElement('div');
                canvasHost.dataset.documentCanvas = '';
                canvasHost.className = 'm-document-preview__canvas';
                embed?.insertBefore(canvasHost, status);
            } else {
                canvasHost.innerHTML = '';
            }

            pdf.hidden = true;

            try {
                const pdfjs = await loadPdfjs();
                if (token !== renderToken) return;

                const doc = await pdfjs.getDocument(url).promise;
                if (token !== renderToken) return;

                if (page) page.max = doc.numPages;
                if (status) status.hidden = true;

                const baseWidth = Math.max(240, (stage?.clientWidth || embed?.clientWidth || 320) - 64);
                const ratio = Math.min(window.devicePixelRatio || 1, 2);

                for (let index = 1; index <= doc.numPages; index += 1) {
                    if (token !== renderToken) return;

                    const pdfPage = await doc.getPage(index);
                    const viewport = pdfPage.getViewport({ scale: 1 });
                    const scale = (baseWidth * zoom / 100) / viewport.width;
                    const scaled = pdfPage.getViewport({ scale });

                    const canvas = document.createElement('canvas');
                    canvas.className = 'm-document-preview__canvas-page';
                    canvas.dataset.page = String(index);
                    canvas.width = Math.floor(scaled.width * ratio);
                    canvas.height = Math.floor(scaled.height * ratio);
                    canvas.style.width = `${scaled.width}px`;
                    canvas.style.height = `${scaled.height}px`;

                    const context = canvas.getContext('2d');
                    context.scale(ratio, ratio);
                    await pdfPage.render({ canvasContext: context, viewport: scaled }).promise;
                    canvasHost.appendChild(canvas);
                }

                viewer = canvasHost;
                scrollToPage(pageNumber());
            } catch {
                if (token !== renderToken) return;
                if (status) {
                    status.hidden = false;
                    status.textContent = 'Could not load document.';
                }
            }
        };

        const updatePdf = () => {
            if (isTouchDevice()) renderCanvasPages();
            else showPdfIframe();
        };

        const update = () => {
            output.textContent = `${zoom}%`;
            buttons.forEach(button => {
                button.disabled = Number(button.dataset.documentZoom) < 0 ? zoom <= MIN_ZOOM : zoom >= MAX_ZOOM;
            });
            if (image) image.style.width = `${IMAGE_WIDTH * zoom / 100}%`;
            else updatePdf();
        };

        buttons.forEach(button => button.addEventListener('click', () => {
            zoom = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, zoom + Number(button.dataset.documentZoom) * ZOOM_STEP));
            update();
        }));

        page?.addEventListener('change', () => {
            if (isTouchDevice() && embed?.querySelector('[data-document-canvas]')) {
                const number = pageNumber();
                if (page) page.value = String(number);
                scrollToPage(number);
                return;
            }
            update();
        });

        page?.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                page.dispatchEvent(new Event('change'));
            }
        });

        update();
    });
}
