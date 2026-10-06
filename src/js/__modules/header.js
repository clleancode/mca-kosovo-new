const header = () => {
    const headerBurger = document.querySelector('.m-header__burger');
    const menu = document.querySelector('.m-header__menu-mobile');
    const iconItem = document.querySelectorAll(".m-header__menu-mobile .a-list-item .a-icon");
    const searchForm = document.querySelector(".m-search--form");
    const searchIcon = document.querySelector(".a-icon--search");
    const closeIcon = document.querySelector(".a-icon--close");

    headerBurger.addEventListener('click', (e) => {
        e.preventDefault();
        if (menu) {
            menu.classList.toggle('active');
            headerBurger.classList.toggle('active');
        }
    });

    iconItem.forEach(icon => {
        icon.addEventListener("touchstart", function (event) {
            event.preventDefault();
            const listItem = this.closest("li");
            const subMenu = listItem.querySelector(".m-header__sub-menu");
            if (subMenu) {
                subMenu.classList.add("active");
            }
        });
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
