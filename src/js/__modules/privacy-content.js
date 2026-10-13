export default function privacyContent() {
    document.querySelectorAll('[data-privacy-content]').forEach(root => {
        const sections = [...root.querySelectorAll('[data-privacy-section]')];
        const links = [...root.querySelectorAll('.m-privacy-content__contents > a')];
        if (!sections.length) return;
        let scheduled = false;
        const update = () => {
            scheduled = false;
            let active = sections[0];
            sections.forEach(section => {
                if (section.getBoundingClientRect().top <= 160) active = section;
            });
            links.forEach(link => {
                if (link.hash === `#${active.id}`) link.setAttribute('aria-current', 'location');
                else link.removeAttribute('aria-current');
            });
        };
        const schedule = () => {
            if (!scheduled) { scheduled = true; requestAnimationFrame(update); }
        };
        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', schedule);
        links.forEach(link => link.addEventListener('click', event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            const target = sections.find(section => `#${section.id}` === link.hash);
            if (!target) return;
            event.preventDefault();
            target.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
            history.replaceState(null, '', link.hash);
        }));
        update();
    });
}
