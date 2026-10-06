import Swiper from 'swiper';
import { Navigation, Pagination, EffectFade, Autoplay } from 'swiper/modules';

Swiper.use([Navigation, Pagination, EffectFade, Autoplay]);

const slider = (options = {}) => {
    const sliderName = options.name || 'defaultSlider';

    const defaultOptions = {
        delay: 5000,
        slidesPerView: 1,
        speed: 800,
        navigation: true,
        pagination: false,
        centeredSlides: false,
        loop: false,
        slidesOffsetBefore: false,
        slidesOffsetAfter: false,
        spaceBetween: 15,
        breakpoints: {},
        autoplay: false, // Add autoplay to default options
        effect: 'slide', // default effect
        fadeEffect: { crossFade: false } // default fadeEffect config
    };

    const swiperOptions = { ...defaultOptions, ...options };
    const sliderElement = document.querySelector(`.${sliderName}`);
    if (!sliderElement) return;

    if (swiperOptions.navigation) {
        swiperOptions.navigation = {
            addIcons: false,
            nextEl: sliderElement.querySelector(".swiper-button-next"),
            prevEl: sliderElement.querySelector(".swiper-button-prev"),
        };
    }

    const container = document.querySelector('.container');
    if (container) {
        const styles = window.getComputedStyle(container);
        const paddingLeft = parseFloat(styles.paddingLeft);
        const paddingRight = parseFloat(styles.paddingRight);
        const containerWidth = container.clientWidth - paddingLeft - paddingRight;
        const windowWidth = window.innerWidth;
        const offset = ((windowWidth - containerWidth) / 2);

        if (swiperOptions.slidesOffsetBefore === true) {
            swiperOptions.slidesOffsetBefore = offset;
        }
        if (swiperOptions.slidesOffsetAfter === true) {
            swiperOptions.slidesOffsetAfter = offset;
        }
    }

    if (swiperOptions.pagination === true) {
        swiperOptions.pagination = {
            el: ".swiper-pagination",
            dynamicBullets: true,
            clickable: true,
        };
    } else if (swiperOptions.pagination && typeof swiperOptions.pagination === 'object') {
        swiperOptions.pagination = { ...swiperOptions.pagination };
        if (typeof swiperOptions.pagination.el === 'string') {
            swiperOptions.pagination.el = sliderElement.querySelector(swiperOptions.pagination.el);
        }
    }

    // If autoplay is true or an object, set up Swiper's autoplay config
    if (swiperOptions.autoplay) {
        if (typeof swiperOptions.autoplay === 'boolean') {
            swiperOptions.autoplay = {
                delay: 8000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            };
        }
        // else, if it's an object, let user config pass through
    }

    // Add crossFade if effect is fade
    if (swiperOptions.effect === 'fade') {
        // Allow user to override crossFade via options.fadeEffect
        swiperOptions.fadeEffect = {
            crossFade: true,
            ...(options.fadeEffect || {})
        };
    }

    new Swiper(sliderElement, swiperOptions);
};

export default slider;
