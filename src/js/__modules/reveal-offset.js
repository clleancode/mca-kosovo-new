// IntersectionObserver percentage margins use width; calculate from height instead.
export const revealOffset = () => Math.round(
    window.innerHeight * (matchMedia('(max-width: 767px)').matches ? 0.15 : 0.20)
);
