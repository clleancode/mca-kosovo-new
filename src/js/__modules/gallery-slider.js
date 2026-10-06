import Swiper from 'swiper';
import { Navigation, A11y, Keyboard } from 'swiper/modules';

const gallerySlider = () => {
    document.querySelectorAll('[data-gallery-slider]').forEach(section => {
        const element = section.querySelector('.m-gallery-slider__swiper');
        if (!element || element.swiper) return;
        const container = section.querySelector('.container');
        const getOffset = () => container.getBoundingClientRect().left + parseFloat(getComputedStyle(container).paddingLeft);
        const offset = getOffset();
        new Swiper(element, {
            modules: [Navigation, A11y, Keyboard],
            slidesPerView: 1,
            breakpoints: {
                768: { slidesPerView: 3.7 },
            },
            spaceBetween: 12,
            speed: 600,
            grabCursor: true,
            watchOverflow: true,
            keyboard: { enabled: true, onlyInViewport: true },
            slidesOffsetBefore: offset,
            slidesOffsetAfter: offset,
            navigation: {
                addIcons: false,
                prevEl: section.querySelector('.m-gallery-slider__previous'),
                nextEl: section.querySelector('.m-gallery-slider__next'),
            },
            on: {
                beforeResize(swiper) {
                    const updatedOffset = getOffset();
                    swiper.params.slidesOffsetBefore = updatedOffset;
                    swiper.params.slidesOffsetAfter = updatedOffset;
                },
            },
        });
    });
};
export default gallerySlider;
