const overview = () => {
    document.querySelectorAll('[data-overview]').forEach(section => {
        if (section.dataset.overviewInitialized) return;
        section.dataset.overviewInitialized = 'true';
        const entries = [...section.querySelectorAll('[data-overview-link]')].map(link => {
            const url = new URL(link.href, location.href);
            if (url.origin !== location.origin || url.pathname !== location.pathname || !url.hash) return null;
            let target;
            try { target = document.getElementById(decodeURIComponent(url.hash.slice(1))); } catch { return null; }
            return target ? { link, target } : null;
        }).filter(Boolean);
        if (!entries.length) return;
        const offset = () => Math.max(0, document.querySelector('.o-header')?.getBoundingClientRect().bottom ?? 0) + 16;
        const activate = selected => entries.forEach(({ link }) => {
            if (link === selected) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
        entries.forEach(({ link, target }) => link.addEventListener('click', event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0 || link.target === '_blank') return;
            event.preventDefault();
            window.scrollTo({ top: Math.max(0, window.scrollY + target.getBoundingClientRect().top - offset()), behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
            activate(link);
        }));
        let pending = false;
        const update = () => {
            const ordered = entries.map(entry => ({ ...entry, top: entry.target.getBoundingClientRect().top })).sort((a, b) => a.top - b.top);
            const reached = ordered.filter(entry => entry.top <= offset() + 24);
            activate((reached.at(-1) ?? ordered[0]).link);
            pending = false;
        };
        window.addEventListener('scroll', () => {
            if (!pending) { pending = true; requestAnimationFrame(update); }
        }, { passive: true });
        update();
    });
};
export default overview;
