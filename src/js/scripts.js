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
import eParallax from "./__modules/e-parallax";
import imageLoading from "./__modules/image-loading";

document.addEventListener("DOMContentLoaded", () => {
    initLenis();
    eReveal();
    eParallax();
    imageLoading();

    const lenis = getLenis();
    if (lenis) lenis.on("scroll", updateHeaderScroll);
    else window.addEventListener("scroll", updateHeaderScroll, { passive: true });
    updateHeaderScroll();

    if (document.querySelector('[data-transparency-toggle]')) {
        transparency();
    }
    if (document.querySelector('[data-compact-end]')) {
        compact();
    }
    if (document.querySelector('.m-compact-projects__grid')) {
        compactProjects();
    }
    if (document.querySelector('[data-news]')) {
        news();
    }
    if (document.querySelector('[data-gallery-slider]')) {
        gallerySlider();
    }
    if (document.querySelector('[data-overview]')) {
        overview();
    }
    if (document.querySelector('[data-procurement-filter]')) {
        filter();
    }
    if (document.querySelector('[data-notices]')) {
        notices();
    }
    if (document.querySelector('[data-current-calls]')) {
        currentCalls();
    }
    if (document.querySelector('[data-team]')) {
        team();
    }
    if (document.querySelector('.m-faq__items')) {
        faq();
    }
    if (document.querySelector('[data-document-search]')) {
        smallHero();
    }
    if (document.querySelector('[data-application]')) {
        application();
    }
    if (document.querySelector('[data-vacancies-archive], [data-vacancies]')) {
        vacancies();
    }
    if (document.querySelector('[data-resource-category]')) {
        category();
    }
    if (document.querySelector('[data-all-documents]')) {
        allDocuments();
    }
    if (document.querySelector('[data-all-albums]')) {
        allAlbums();
    }
    if (document.querySelector('[data-procurement-copy]')) {
        aboutProcurement();
    }
    if (document.querySelector('[data-job-copy]')) {
        jobRole();
    }
    if (document.querySelector('[data-document-preview]')) {
        documentPreview();
    }
    if (document.querySelector('[data-privacy-content]')) {
        privacyContent();
    }
    if (document.querySelector('[data-single-hero-copy]')) {
        singleHero();
    }
    if (document.querySelector('.m-single-content__toc, [data-single-content-copy]')) {
        singleContent();
    }
    if (document.querySelector('[data-newsletter-subscribe]')) {
        newsletterSubscribe();
    }

	if (document.querySelector(".o-header")) {
		header();
	}

    if (document.querySelector('[data-hero-slider], .m-hero-about__image img, .m-small-hero__image img, .m-projects-hero__image img')) {
        heroSlider();
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

