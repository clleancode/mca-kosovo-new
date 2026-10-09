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

	if (document.querySelector("[data-fancybox]")) {
		Fancybox.bind("[data-fancybox]", {
			closeButton: true,
			transition: "slide",
			clickContent: "toggle",
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
