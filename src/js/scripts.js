import heroSlider from "./__modules/hero-slider";
import transparency from "./__modules/transparency";
import header from "./__modules/header";
import slider from "./__modules/slider";
import countdown from "./__modules/countdown";
import cookieBanner from "./__modules/cookie-banner";
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
import jobRole from "./__modules/job-role";
import documentPreview from "./__modules/document-preview";
import privacyContent from "./__modules/privacy-content";
import singleHero from "./__modules/single-hero";
import singleContent from "./__modules/single-content";
import newsletterSubscribe from "./__modules/newsletter-subscribe";
import initLenis, { getLenis } from "./__modules/lenis-scroll";
import eReveal from "./__modules/e-reveal";

document.addEventListener("DOMContentLoaded", () => {
    initLenis();
    eReveal();

    const lenis = getLenis();
    if (lenis) lenis.on("scroll", updateHeaderScroll);
    else window.addEventListener("scroll", updateHeaderScroll, { passive: true });
    updateHeaderScroll();

    transparency();
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
    jobRole();
    documentPreview();
    privacyContent();
    singleHero();
    singleContent();
    newsletterSubscribe();

	if (document.querySelector(".o-header")) {
		header();
	}

    heroSlider();

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

	if (document.querySelector(".o-cookie-banner")) {
		cookieBanner();
	}

});

const updateHeaderScroll = () => {
	const oHeader = document.querySelector(".o-header");
	if (!oHeader) return;
	const scrollY = getLenis()?.scroll ?? window.scrollY;
	oHeader.classList.toggle("o-header--scrolled", scrollY > 0);
};

