const team = () => {
    document.querySelectorAll('[data-team]').forEach((section, sectionIndex) => {
        if (section.dataset.teamInitialized) return;
        section.dataset.teamInitialized = 'true';
        section.querySelectorAll('[data-team-group]').forEach((group, groupIndex) => {
            const filters = [...group.querySelectorAll('[data-team-department]')];
            const members = [...group.querySelectorAll('[data-team-member-department]')];
            const filterList = group.querySelector('.m-team__filters');
            const mobile = matchMedia('(max-width: 767px)');
            let toggle;
            let dropdownAnimation;
            const setOpen = (open, animate = true) => {
                const currentHeight = filterList?.getBoundingClientRect().height ?? 0;
                const currentOpacity = filterList ? getComputedStyle(filterList).opacity : '0';
                dropdownAnimation?.cancel();
                group.classList.remove('is-filters-animating');
                group.classList.toggle('is-filters-open', open);
                toggle?.setAttribute('aria-expanded', String(open));
                if (filterList) filterList.inert = mobile.matches && !open;
                if (!filterList || !animate || !mobile.matches || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                group.classList.add('is-filters-animating');
                const targetHeight = open ? filterList.getBoundingClientRect().height : 0;
                const animation = filterList.animate([
                    { height: `${currentHeight}px`, opacity: currentHeight ? currentOpacity : 0, paddingBottom: currentHeight ? '24px' : '0px' },
                    { height: `${targetHeight}px`, opacity: open ? 1 : 0, paddingBottom: open ? '24px' : '0px' },
                ], { duration: 380, easing: 'cubic-bezier(0.22, 1, 0.36, 1)', fill: 'both' });
                dropdownAnimation = animation;
                animation.onfinish = () => {
                    if (dropdownAnimation !== animation) return;
                    group.classList.remove('is-filters-animating');
                    animation.cancel();
                    dropdownAnimation = null;
                };
            };
            if (filterList && filters.length) {
                toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'm-team__filters-toggle a-text a-text--m';
                toggle.dataset.textReveal = 'off';
                toggle.textContent = (filters.find(filter => filter.getAttribute('aria-current') === 'true') || filters[0]).textContent.trim();
                filterList.id ||= `team-filters-${sectionIndex}-${groupIndex}`;
                toggle.setAttribute('aria-controls', filterList.id);
                filterList.before(toggle);
                filterList.dataset.textReveal = 'off';
                group.classList.add('has-mobile-filters');
                setOpen(false, false);
                toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
                mobile.addEventListener('change', () => setOpen(false, false));
                filterList.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && mobile.matches) {
                        setOpen(false);
                        toggle.focus();
                    }
                });
            }
            filters.forEach(button => button.addEventListener('click', event => {
                event.preventDefault();
                filters.forEach(filter => filter.setAttribute('aria-current', String(filter === button)));
                members.forEach(member => {
                    member.hidden = button.dataset.teamDepartment !== '' && member.dataset.teamMemberDepartment !== button.dataset.teamDepartment;
                });
                if (toggle) {
                    toggle.textContent = button.textContent.trim();
                    if (mobile.matches) {
                        setOpen(false);
                        toggle.focus({ preventScroll: true });
                    }
                }
            }));
        });
        const tabs = [...section.querySelectorAll('[data-team-tab]')];
        const panels = [...section.querySelectorAll('[data-team-panel]')];
        const activate = selected => {
            tabs.forEach(tab => {
                const active = tab === selected;
                tab.setAttribute('aria-selected', String(active));
                tab.tabIndex = active ? 0 : -1;
            });
            panels.forEach(panel => { panel.hidden = panel.dataset.teamPanel !== selected.dataset.teamTab; });
        };
        tabs.forEach((tab, index) => {
            const panel = panels.find(item => item.dataset.teamPanel === tab.dataset.teamTab);
            panel?.setAttribute('role', 'tabpanel');
            panel?.setAttribute('aria-labelledby', tab.id);
            tab.addEventListener('click', () => activate(tab));
            tab.addEventListener('keydown', event => {
                let next;
                if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
                if (event.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
                if (event.key === 'Home') next = 0;
                if (event.key === 'End') next = tabs.length - 1;
                if (next === undefined) return;
                event.preventDefault();
                tabs[next].focus();
                activate(tabs[next]);
            });
        });
        if (tabs.length) activate(tabs[0]);
        panels.forEach(panel => {
            const alternates = [...panel.querySelectorAll('[data-team-alternate]')];
            const button = panel.querySelector('[data-team-toggle]');
            if (!button) return;
            alternates.forEach(group => { group.hidden = true; });
            panel.querySelector('[data-team-more]').hidden = false;
            button.addEventListener('click', () => {
                const expanded = button.getAttribute('aria-expanded') !== 'true';
                button.setAttribute('aria-expanded', String(expanded));
                button.querySelector('span').textContent = expanded ? 'Show less \u2212' : 'Show all +';
                alternates.forEach(group => { group.hidden = !expanded; });
            });
        });
    });
};
export default team;
