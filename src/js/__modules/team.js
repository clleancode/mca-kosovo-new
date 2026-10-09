const team = () => {
    document.querySelectorAll('[data-team]').forEach(section => {
        if (section.dataset.teamInitialized) return;
        section.dataset.teamInitialized = 'true';
        section.querySelectorAll('[data-team-group]').forEach(group => {
            const filters = [...group.querySelectorAll('[data-team-department]')];
            const members = [...group.querySelectorAll('[data-team-member-department]')];
            filters.forEach(button => button.addEventListener('click', () => {
                filters.forEach(filter => filter.setAttribute('aria-pressed', String(filter === button)));
                members.forEach(member => {
                    member.hidden = button.dataset.teamDepartment !== '' && member.dataset.teamMemberDepartment !== button.dataset.teamDepartment;
                });
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
