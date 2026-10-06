const news = () => {
    document.querySelectorAll('[data-news]').forEach(section => {
        if (section.dataset.newsInitialized) return;
        section.dataset.newsInitialized = 'true';
        let controller;
        section.addEventListener('click', async event => {
            const link = event.target.closest('[data-news-filter]');
            if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
            event.preventDefault();
            controller?.abort();
            controller = new AbortController();
            const signal = controller.signal;
            const stories = section.querySelector('.m-news__stories');
            stories.setAttribute('aria-busy', 'true');
            try {
                const response = await fetch(link.href, { signal });
                if (!response.ok) throw new Error('News request failed');
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const updated = [...page.querySelectorAll('[data-news]')].find(item => item.dataset.news === section.dataset.news);
                const content = updated?.querySelector('.m-news__stories');
                if (!content) throw new Error('News block missing');
                const newsletter = stories.querySelector('.m-news__newsletter');
                stories.innerHTML = content.innerHTML;
                if (newsletter) stories.querySelector('.m-news__newsletter')?.replaceWith(newsletter);
                section.querySelectorAll('[data-news-filter]').forEach(filter => {
                    filter.removeAttribute('aria-current');
                    if (filter === link) filter.setAttribute('aria-current', 'true');
                });
            } catch (error) {
                if (error.name !== 'AbortError') window.location.assign(link.href);
            } finally {
                if (!signal.aborted) stories.removeAttribute('aria-busy');
            }
        });
    });
};
export default news;
