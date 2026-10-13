import header from "./__modules/header";
import slider from "./__modules/slider";
import AOS from "aos";
import countdown from "./__modules/countdown";
import cookie from "./__modules/cookie";
import compact from "./__modules/compact";
import compactProjects from "./__modules/compact-projects";
import news from "./__modules/news";
import gallerySlider from "./__modules/gallery-slider";
import overview from "./__modules/overview";
import filter from "./__modules/filter";
import notices from "./__modules/notices";
import currentCalls from "./__modules/current-calls";
import team from "./__modules/team";
import faq from "./__modules/faq";
import smallHero from "./__modules/small-hero";
import application from "./__modules/application";
import vacancies from "./__modules/vacancies";
import category from "./__modules/category";
import allDocuments from "./__modules/all-documents";
import allAlbums from "./__modules/all-albums";
import aboutProcurement from "./__modules/about-procurement";

const updateHeroFraction = (swiper) => {
	const current = swiper.el.querySelector(".m-hero__current");
	const total = swiper.el.querySelector(".m-hero__total");
	if (!current || !total) return;

	const slideCount = swiper.slides.length;
	current.textContent = String(swiper.realIndex + 1).padStart(2, "0");
	total.textContent = String(slideCount).padStart(2, "0");
};

document.addEventListener("DOMContentLoaded", () => {
    compact();
    compactProjects();
    news();
    gallerySlider();
    overview();
    filter();
    notices();
    currentCalls();
    team();
    faq();
    smallHero();
    application();
    vacancies();
    category();
    allDocuments();
    allAlbums();
    aboutProcurement();

	if (document.querySelector(".o-header")) {
		header();
	}

	if (document.querySelector(".swiper--hero")) {
		slider({
			name: "swiper--hero",
			effect: "fade",
			speed: 1200,
			navigation: true,
			rewind: true,
			pagination: {
				el: ".m-hero__pagination",
				type: "progressbar",
			},
			on: {
				init(swiper) {
					updateHeroFraction(swiper);
				},
				slideChange(swiper) {
					updateHeroFraction(swiper);
				},
			},
			autoplay: {
				delay: 8000,
				disableOnInteraction: false,
			},
			fadeEffect: { crossFade: true },
		});
	}

	if (document.querySelector("#a-countdown")) {
		countdown();
	}

	if (window.Fancybox && document.querySelector("[data-fancybox]")) {
		Fancybox.bind("[data-fancybox]:not([data-mca-gallery])", {
			closeButton: true,
			transition: "slide",
			clickContent: "toggle",
		});
	}

    if (window.Fancybox && document.querySelector('[data-mca-gallery]')) {
        Fancybox.bind('[data-mca-gallery]', {
            mainClass: 'gallery-lightbox',
            closeButton: false,
            infinite: true,
            Thumbs: { autoStart: true },
            Toolbar: { display: ['counter', 'close'] },
        });
    }

	if (document.querySelector(".cookie-banner")) {
		cookie();
	}

	AOS.init({
		duration: 350,
		once: true,
		offset: 250,
		mirror: false,
		easing: "ease",
	});

});

const updateHeaderScroll = () => {
	const oHeader = document.querySelector(".o-header");
	if (!oHeader) return;
	oHeader.classList.toggle("o-header--scrolled", window.scrollY > 0);
};

document.addEventListener("DOMContentLoaded", updateHeaderScroll);
window.addEventListener("scroll", updateHeaderScroll, { passive: true });
