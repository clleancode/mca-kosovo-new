import { revealOffset } from './reveal-offset';

const timeline = () => {
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    if (motion.matches || !window.IntersectionObserver || !Element.prototype.animate) return;
    document.querySelectorAll('.m-timeline__board').forEach(board => {
        const items = [...board.querySelectorAll('.m-timeline__milestone')];
        const line = board.querySelector('.m-timeline__line');
        const decoration = board.querySelector('.m-timeline__decoration');
        const decorationOpacity = decoration ? getComputedStyle(decoration).opacity : '1';
        const decorationHidden = decoration?.animate([{ opacity: 0 }, { opacity: 0 }], { duration: 1, fill: 'both' });
        decorationHidden?.pause();
        const hidden = items.map(item => {
            const effect = item.animate([{ opacity: 0 }, { opacity: 0 }], { duration: 1, fill: 'both' });
            effect.pause();
            return effect;
        });
        const lineHidden = line?.animate([{ clipPath: 'inset(0 100% 0 0)' }, { clipPath: 'inset(0 100% 0 0)' }], { duration: 1, fill: 'both' });
        lineHidden?.pause();
        const effects = [...hidden, lineHidden, decorationHidden].filter(Boolean);
        const show = new IntersectionObserver(entries => {
            if (!entries.some(entry => entry.isIntersecting)) return;
            show.disconnect();
            const desktop = matchMedia('(min-width: 1024px)').matches;
            lineHidden?.cancel();
            decorationHidden?.cancel();
            if (decoration && !motion.matches) {
                const effect = decoration.animate([
                    { opacity: 0, clipPath: 'inset(0 100% 0 0)' },
                    { opacity: decorationOpacity, clipPath: 'inset(0 0% 0 0)' },
                ], { duration: 1100, easing: 'ease-in-out', fill: 'both' });
                effects.push(effect);
                effect.onfinish = () => effect.cancel();
            }
            if (line && desktop && !motion.matches) {
                const effect = line.animate([{ clipPath: 'inset(0 100% 0 0)' }, { clipPath: 'inset(-16px -16px -16px 0)' }], { duration: 1100, easing: 'ease-in-out', fill: 'both' });
                effects.push(effect);
                effect.onfinish = () => effect.cancel();
            }
            items.forEach((item, index) => {
                hidden[index].cancel();
                if (motion.matches) return;
                const effect = item.animate([
                    { opacity: 0, translate: '0 20px' },
                    { opacity: 1, translate: '0 0' },
                ], { duration: 650, delay: (desktop ? 1100 : 0) + index * 380, easing: 'ease-out', fill: 'both' });
                effects.push(effect);
                effect.onfinish = () => effect.cancel();
            });
        }, { rootMargin: `0px 0px -${revealOffset()}px 0px` });
        show.observe(board);
        motion.addEventListener('change', () => {
            if (!motion.matches) return;
            show.disconnect();
            effects.forEach(effect => effect.cancel());
        });
    });
};
export default timeline;
