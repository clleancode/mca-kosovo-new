const reveal = image => {
    image.classList.remove('is-image-loading');
    image.classList.add('is-image-loaded');
    image.addEventListener('animationend', () => image.classList.remove('is-image-loaded'), { once: true });
};

export default function imageLoading() {
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    document.querySelectorAll('img[loading="lazy"]').forEach(image => {
        if (image.complete) return;

        image.classList.add('is-image-loading');
        image.addEventListener('load', () => reveal(image), { once: true });
        image.addEventListener('error', () => image.classList.remove('is-image-loading'), { once: true });
    });
}
