const header = () => {
    const desktopItems = [...document.querySelectorAll('.m-header__inner .m-menu > ul > li')];
    const desktopHover = window.matchMedia('(min-width: 1200px) and (hover: hover)');
    let closeDropdownTimer;
    let restoringDesktopFocus = false;
    const setDesktopDropdown = (item, open) => {
        const dropdown = item.querySelector(':scope > .m-sub-menu');
        if (!dropdown) return;
        item.classList.toggle('is-submenu-open', open);
        item.querySelector(':scope > a, :scope > button')?.setAttribute('aria-expanded', String(open));
        dropdown.inert = !open;
    };
    const closeDesktopDropdowns = (except = null) => {
        clearTimeout(closeDropdownTimer);
        desktopItems.forEach(item => { if (item !== except) setDesktopDropdown(item, false); });
    };
    desktopItems.forEach(item => {
        const trigger = item.querySelector(':scope > a, :scope > button');
        const dropdown = item.querySelector(':scope > .m-sub-menu');
        if (!trigger || !dropdown) return;
        setDesktopDropdown(item, false);
        const open = () => {
            closeDesktopDropdowns(item);
            setDesktopDropdown(item, true);
        };
        trigger?.addEventListener('pointerenter', () => {
            if (!desktopHover.matches) return;
            open();
        });
        dropdown?.addEventListener('pointerenter', () => clearTimeout(closeDropdownTimer));
        item.addEventListener('pointerleave', () => {
            if (!desktopHover.matches || !item.classList.contains('is-submenu-open')) return;
            clearTimeout(closeDropdownTimer);
            closeDropdownTimer = setTimeout(() => {
                if (!item.contains(document.activeElement)) setDesktopDropdown(item, false);
            }, 350);
        });
        trigger.addEventListener('click', () => {
            if (trigger.tagName !== 'BUTTON') return;
            if (trigger.getAttribute('aria-expanded') === 'true') closeDesktopDropdowns();
            else open();
        });
        item.addEventListener('focusin', event => {
            if (restoringDesktopFocus) return;
            closeDesktopDropdowns(item);
            // Native buttons open on activation; navigation links retain focus-to-open.
            if (event.target !== trigger || trigger.tagName !== 'BUTTON') open();
        });
        item.addEventListener('focusout', event => {
            if (!item.contains(event.relatedTarget)) setDesktopDropdown(item, false);
        });
        item.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown' && event.target === trigger) {
                event.preventDefault();
                open();
                dropdown.querySelector('a[href], button:not(:disabled)')?.focus();
            }
            if (event.key === 'Escape' && item.classList.contains('is-submenu-open')) {
                event.preventDefault();
                closeDesktopDropdowns();
                restoringDesktopFocus = true;
                trigger.focus();
                restoringDesktopFocus = false;
            }
        });
    });
    desktopHover.addEventListener('change', () => closeDesktopDropdowns());
    document.addEventListener('click', event => {
        if (!desktopItems.some(item => item.contains(event.target))) closeDesktopDropdowns();
    });
    const headerBurger = document.querySelector('.m-header__burger');
    const menu = document.querySelector('.m-header__menu-mobile');
    const searchForm = document.querySelector(".m-search--form");
    const searchIcon = document.querySelector(".a-icon--search");
    const closeIcon = document.querySelector(".a-icon--close");
    const closeSubmenus = () => {
        menu.querySelectorAll('.m-header__sub-menu.active').forEach(submenu => {
            submenu.classList.remove('active');
            submenu.inert = true;
        });
        menu.querySelectorAll('[aria-expanded="true"]').forEach(link => link.setAttribute('aria-expanded', 'false'));
    };

    headerBurger.addEventListener('click', (e) => {
        e.preventDefault();
        if (menu) {
            menu.classList.toggle('active');
            headerBurger.classList.toggle('active');
            headerBurger.setAttribute('aria-expanded', String(menu.classList.contains('active')));
            if (!menu.classList.contains('active')) closeSubmenus();
        }
    });

    menu.addEventListener("click", (event) => {
        const back = event.target.closest('.m-header__submenu-back');
        if (back) {
            event.preventDefault();
            const submenu = back.closest('.m-header__sub-menu');
            submenu.classList.remove('active');
            submenu.inert = true;
            const trigger = submenu.parentElement.querySelector(':scope > .a-list-item');
            trigger?.setAttribute('aria-expanded', 'false');
            trigger?.focus();
            return;
        }
        const link = event.target.closest(".a-list-item");
        if (!link || !menu.contains(link)) return;

        const subMenu = link.parentElement.querySelector(":scope > .m-header__sub-menu");
        if (!subMenu) return;

        event.preventDefault();
        subMenu.classList.add("active");
        subMenu.inert = false;
        link.setAttribute("aria-expanded", "true");
        subMenu.querySelector('.m-header__submenu-back')?.focus();
    });

    menu.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        const submenu = event.target.closest('.m-header__sub-menu.active');
        if (submenu) {
            event.preventDefault();
            submenu.querySelector('.m-header__submenu-back')?.click();
        } else if (menu.classList.contains('active')) {
            headerBurger.click();
            headerBurger.focus();
        }
    });

    searchIcon.addEventListener("click", (event) => {
        event.preventDefault();

        searchForm.classList.add("active");
        searchForm.style.opacity = '1';
        searchForm.style.visibility = 'visible';
        searchForm.style.zIndex = '1';

        setTimeout(() => {
            searchForm.querySelector("input").focus();
        }, 100);
    });

    closeIcon.addEventListener("click", (event) => {
        event.preventDefault();

        searchForm.classList.remove("active");
        searchForm.style.opacity = '0';
        searchForm.style.visibility = 'hidden';
        searchForm.style.zIndex = '-1';
    });
}

export default header;
