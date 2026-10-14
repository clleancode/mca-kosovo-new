const cookieBanner = () => {
    const root = document.getElementById('cookie-banner');
    if (!root) return;

    const notice = root.querySelector('[data-cookie-notice]');
    const dialog = root.querySelector('[data-cookie-panel]');
    const overlay = root.querySelector('[data-cookie-overlay]');
    let previousOverflow = '';
    const inertStates = new Map();
    const categories = ['preferences', 'statistics', 'marketing'];
    const empty = { functional: true, preferences: false, statistics: false, marketing: false };
    let consent = null;
    let opener = null;

    try {
        const stored = localStorage.getItem('cookie_consent');
        if (stored === 'accepted' || stored === 'rejected') {
            consent = { ...empty, ...Object.fromEntries(categories.map(key => [key, stored === 'accepted'])) };
        } else if (stored) {
            const parsed = JSON.parse(stored);
            if (parsed.version === 1 && categories.every(key => typeof parsed[key] === 'boolean')) {
                consent = { ...parsed, functional: true };
            }
        }
    } catch (_) { }

    const sync = () => categories.forEach(key => {
        dialog.querySelector(`[name="${key}"]`).checked = Boolean(consent?.[key]);
    });

    const close = () => {
        if (!overlay.hidden) {
            overlay.hidden = true;
            document.body.style.overflow = previousOverflow;
            inertStates.forEach((value, element) => { element.inert = value; });
            inertStates.clear();
        }
        notice.hidden = Boolean(consent);
        root.hidden = Boolean(consent);
        if (opener?.isConnected && !opener.closest('[hidden]')) opener.focus();
    };

    const open = () => {
        if (!overlay.hidden) return;
        opener = document.activeElement;
        sync();
        root.hidden = false;
        notice.hidden = true;
        overlay.hidden = false;
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        let branch = root;
        while (branch.parentElement) {
            Array.from(branch.parentElement.children).forEach(element => {
                if (element !== branch) {
                    inertStates.set(element, element.inert);
                    element.inert = true;
                }
            });
            if (branch.parentElement === document.body) break;
            branch = branch.parentElement;
        }
        dialog.querySelector('[data-cookie-action="close"]').focus();
    };

    const save = (values) => {
        consent = { ...empty, ...values, version: 1 };
        try { localStorage.setItem('cookie_consent', JSON.stringify(consent)); } catch (_) {}
        if (typeof window.gtag === 'function') {
            window.gtag('consent', 'update', {
                analytics_storage: consent.statistics ? 'granted' : 'denied',
                ad_storage: consent.marketing ? 'granted' : 'denied',
                ad_user_data: consent.marketing ? 'granted' : 'denied',
                ad_personalization: consent.marketing ? 'granted' : 'denied',
                functionality_storage: consent.preferences ? 'granted' : 'denied',
                personalization_storage: consent.preferences ? 'granted' : 'denied',
                security_storage: 'granted',
            });
        }
        window.dispatchEvent(new CustomEvent('mca:cookie-consent', { detail: { ...consent } }));
        close();
    };

    root.addEventListener('click', event => {
        const action = event.target.closest('[data-cookie-action]')?.dataset.cookieAction;
        if (action) event.preventDefault();
        if (event.target === overlay) close();
        if (action === 'preferences') open();
        if (action === 'close') close();
        if (action === 'deny') save(empty);
        if (action === 'accept') save(Object.fromEntries(categories.map(key => [key, true])));
        if (action === 'save') save(Object.fromEntries(categories.map(key => [key, dialog.querySelector(`[name="${key}"]`).checked])));
    });
    root.addEventListener('keydown', event => {
        const actionLink = event.target.closest('a[data-cookie-action]');
        if (event.key === ' ' && actionLink) {
            event.preventDefault();
            actionLink.click();
            return;
        }
        if (overlay.hidden) return;
        if (event.key === 'Escape') { event.preventDefault(); close(); }
        if (event.key === 'Tab') {
            const focusable = Array.from(dialog.querySelectorAll('a[href], button:not(:disabled), input:not(:disabled)'));
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    document.addEventListener('click', event => {
        if (event.target.closest('[data-cookie-settings], a[href="#cookie-settings"]')) {
            event.preventDefault();
            open();
        }
    });
    window.openCookieBanner = open;
    sync();
    root.hidden = Boolean(consent);
};

export default cookieBanner;
