import { scrollToTarget } from './lenis-scroll';

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
        const desktop = matchMedia('(min-width: 1200px)');
        const sortLabel = section.querySelector('.a-overview-factsheet');
        const sortPosition = sortLabel ? document.createTextNode('') : null;
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
        const setOpen = open => {
            section.classList.toggle('is-mobile-overview', !desktop.matches);
            if (projectSlot) {
                if (!desktop.matches && project.parentElement !== projectSlot) projectSlot.append(project);
                else if (desktop.matches && project.parentElement === projectSlot) projectPosition.after(project);
            }
            if (sortLabel && navigation) {
                if (!desktop.matches && sortLabel.parentElement !== navigation) navigation.append(sortLabel);
                else if (desktop.matches && sortLabel.parentElement === navigation) sortPosition.after(sortLabel);
            }
            section.classList.remove('is-navigation-animating');
            toggle?.setAttribute('aria-expanded', String(open));
            section.classList.toggle('is-navigation-open', open);
            if (navigation) navigation.inert = !desktop.matches && !open;
        };
        if (toggle && navigation) {
            section.classList.add('has-mobile-navigation');
            setOpen(false);
            toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
            desktop.addEventListener('change', () => setOpen(false));
            navigation.addEventListener('keydown', event => {
                if (event.key === 'Escape' && !desktop.matches) {
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
                if (!desktop.matches) {
                    setOpen(false);
                    toggle.focus({ preventScroll: true });
                }
            }, true);
        }
        let newsRequest;
        let newsCategory = navigation?.querySelector('[data-news-category][aria-current]')?.dataset.newsCategory || '0';
        const newsSort = section.querySelector('[data-news-sort]');
        const loadNews = async () => {
            const grid = document.querySelector('.o-news-grid');
            if (!grid) return;
            newsRequest?.abort();
            const request = new AbortController();
            newsRequest = request;
            const url = new URL(location.href);
            [...url.searchParams.keys()].filter(key => key.startsWith('news_grid_page_')).forEach(key => url.searchParams.delete(key));
            url.searchParams.set('news_category', newsCategory);
            url.searchParams.set('news_sort', newsSort?.value || 'newest');
            grid.setAttribute('aria-busy', 'true');
            try {
                const response = await fetch(url, { signal: request.signal });
                if (!response.ok) throw new Error('News request failed');
                const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
                const replacement = doc.querySelector('.o-news-grid');
                if (!replacement) throw new Error('News grid missing');
                const featured = document.querySelector('.o-spotlight--featured');
                const nextFeatured = doc.querySelector('.o-spotlight--featured');
                if (featured && nextFeatured) featured.replaceWith(nextFeatured);
                grid.replaceWith(replacement);
            } catch (error) {
                if (error.name !== 'AbortError') grid.removeAttribute('aria-busy');
            }
        };
        navigation?.addEventListener('click', event => {
            const button = event.target.closest('button[data-news-category]');
            if (!button) return;
            newsCategory = button.dataset.newsCategory;
            loadNews();
        });
        newsSort?.addEventListener('change', loadNews);
        const entries = [...section.querySelectorAll('a[data-overview-link]')].map(link => {
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
            scrollToTarget(target, { offset: -offset() });
            activate(link);
        }));
        activate(entries[0].link);
    });
};
export default overview;
