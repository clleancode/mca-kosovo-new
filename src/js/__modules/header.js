const header = () => {
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
