const overview = () => {
    document.querySelectorAll('[data-overview]').forEach(section => {
        if (section.dataset.overviewInitialized) return;
        section.dataset.overviewInitialized = 'true';
        const bar = section.querySelector('.m-overview__navigation');
        if (bar) {
            // A placeholder keeps the original position even when this short
            // block's navigation follows the rest of the page.
            const placeholder = document.createElement('div');
            placeholder.className = 'm-overview__navigation-placeholder';
            bar.before(placeholder);
            placeholder.append(bar);
            let scheduled = false;
            const updateSticky = () => {
                scheduled = false;
                const header = document.querySelector('.o-header');
                const top = Math.max(0, header?.getBoundingClientRect().bottom ?? 0);
                const rect = placeholder.getBoundingClientRect();
                const stuck = window.scrollY > 0 && rect.top < top;
                if (stuck) {
                    placeholder.style.height = `${bar.getBoundingClientRect().height}px`;
                    bar.style.setProperty('--overview-sticky-top', `${top}px`);
                    bar.style.setProperty('--overview-sticky-left', `${rect.left}px`);
                    bar.style.setProperty('--overview-sticky-width', `${rect.width}px`);
                }
                bar.classList.toggle('is-sticky', stuck);
                if (!stuck) placeholder.style.height = '';
            };
            const scheduleSticky = () => {
                if (scheduled) return;
                scheduled = true;
                requestAnimationFrame(updateSticky);
            };
            window.addEventListener('scroll', scheduleSticky, { passive: true });
            window.addEventListener('resize', scheduleSticky);
            document.querySelector('.o-header')?.addEventListener('transitionend', scheduleSticky);
            const resize = new ResizeObserver(scheduleSticky);
            resize.observe(bar);
            resize.observe(placeholder);
            if (document.querySelector('.o-header')) resize.observe(document.querySelector('.o-header'));
            updateSticky();
        }
        const toggle = section.querySelector('.m-overview__mobile-toggle');
        const navigation = section.querySelector('.m-overview__links');
        const currentLabel = section.querySelector('[data-overview-current]');
        const mobile = matchMedia('(max-width: 767px)');
        const sortLabel = section.querySelector('.a-overview-factsheet');
        const sortPosition = sortLabel ? document.createComment('Overview desktop sort position') : null;
        if (sortLabel) sortLabel.before(sortPosition);
        const project = section.querySelector('.m-overview__project');
        const projectPosition = project ? document.createTextNode('') : null;
        let projectSlot;
        if (project && bar) {
            project.before(projectPosition);
            projectSlot = document.createElement('div');
            projectSlot.className = 'm-overview__mobile-project container';
            bar.parentElement.before(projectSlot);
        }
        let dropdownAnimation;
        const setOpen = (open, animate = true) => {
            if (projectSlot) {
                if (mobile.matches && project.parentElement !== projectSlot) projectSlot.append(project);
                else if (!mobile.matches && project.parentElement === projectSlot) projectPosition.after(project);
            }
            if (sortLabel && navigation) {
                if (mobile.matches && sortLabel.parentElement !== navigation) navigation.append(sortLabel);
                else if (!mobile.matches && sortLabel.parentElement === navigation) sortPosition.after(sortLabel);
            }
            const currentHeight = navigation?.getBoundingClientRect().height ?? 0;
            const currentOpacity = navigation ? getComputedStyle(navigation).opacity : '0';
            dropdownAnimation?.cancel();
            dropdownAnimation = null;
            section.classList.remove('is-navigation-animating');
            toggle?.setAttribute('aria-expanded', String(open));
            section.classList.toggle('is-navigation-open', open);
            if (navigation) navigation.inert = mobile.matches && !open;
            if (!navigation || !animate || !mobile.matches || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            section.classList.add('is-navigation-animating');
            const targetHeight = open ? navigation.getBoundingClientRect().height : 0;
            const animation = navigation.animate([
                { height: `${currentHeight}px`, opacity: currentHeight ? currentOpacity : 0, paddingTop: currentHeight ? '8px' : '0px', paddingBottom: currentHeight ? '16px' : '0px' },
                { height: `${targetHeight}px`, opacity: open ? 1 : 0, paddingTop: open ? '8px' : '0px', paddingBottom: open ? '16px' : '0px' },
            ], { duration: 550, easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)', fill: 'both' });
            dropdownAnimation = animation;
            animation.onfinish = () => {
                if (dropdownAnimation !== animation) return;
                section.classList.remove('is-navigation-animating');
                animation.cancel();
                dropdownAnimation = null;
            };
        };
        if (toggle && navigation) {
            section.classList.add('has-mobile-navigation');
            navigation.dataset.textReveal = 'off';
            setOpen(false, false);
            toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
            mobile.addEventListener('change', () => setOpen(false, false));
            navigation.addEventListener('keydown', event => {
                if (event.key === 'Escape' && mobile.matches) {
                    setOpen(false);
                    toggle.focus();
                }
            });
            navigation.addEventListener('click', event => {
                const link = event.target.closest('[data-overview-link]');
                if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank') return;
                if (currentLabel) currentLabel.textContent = link.textContent.trim();
                navigation.querySelectorAll('[data-overview-link]').forEach(item => {
                    if (item === link) item.setAttribute('aria-current', 'location');
                    else item.removeAttribute('aria-current');
                });
                if (mobile.matches) {
                    setOpen(false);
                    toggle.focus({ preventScroll: true });
                }
            }, true);
        }
        const entries = [...section.querySelectorAll('[data-overview-link]')].map(link => {
            const url = new URL(link.href, location.href);
            if (url.origin !== location.origin || url.pathname !== location.pathname || !url.hash) return null;
            let target;
            try { target = document.getElementById(decodeURIComponent(url.hash.slice(1))); } catch { return null; }
            return target ? { link, target } : null;
        }).filter(Boolean);
        if (!entries.length) return;
        const offset = () => Math.max(0, document.querySelector('.o-header')?.getBoundingClientRect().bottom ?? 0)
            + (bar?.getBoundingClientRect().height ?? 0) + 16;
        const activate = selected => {
            if (currentLabel) currentLabel.textContent = selected.textContent.trim();
            entries.forEach(({ link }) => {
            if (link === selected) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
            });
        };
        entries.forEach(({ link, target }) => link.addEventListener('click', event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0 || link.target === '_blank') return;
            event.preventDefault();
            window.scrollTo({ top: Math.max(0, window.scrollY + target.getBoundingClientRect().top - offset()), behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
            activate(link);
        }));
        activate(entries[0].link);
    });
};
export default overview;
