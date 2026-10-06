const compact = () => {
    const clocks = [...document.querySelectorAll('[data-compact-end]')].map(element => ({
        element,
        end: new Date(element.dataset.compactEnd),
    })).filter(clock => !Number.isNaN(clock.end.getTime()));
    if (!clocks.length) return;

    const addMonths = (date, months) => {
        const result = new Date(date);
        const day = result.getUTCDate();
        result.setUTCDate(1);
        result.setUTCMonth(result.getUTCMonth() + months);
        const lastDay = new Date(Date.UTC(result.getUTCFullYear(), result.getUTCMonth() + 1, 0)).getUTCDate();
        result.setUTCDate(Math.min(day, lastDay));
        return result;
    };

    const update = () => {
        const now = new Date();
        let running = false;
        clocks.forEach(({ element, end }) => {
            const values = { years: 0, months: 0, days: 0, hours: 0, minutes: 0, seconds: 0 };
            if (end > now) {
                running = true;
                let months = (end.getUTCFullYear() - now.getUTCFullYear()) * 12 + end.getUTCMonth() - now.getUTCMonth();
                if (addMonths(now, months) > end) months--;
                values.years = Math.floor(months / 12);
                values.months = months % 12;
                let remaining = Math.floor((end - addMonths(now, months)) / 1000);
                for (const [unit, seconds] of [['days', 86400], ['hours', 3600], ['minutes', 60], ['seconds', 1]]) {
                    values[unit] = Math.floor(remaining / seconds);
                    remaining %= seconds;
                }
            }
            element.querySelectorAll('[data-compact-unit]').forEach(unit => {
                unit.textContent = String(values[unit.dataset.compactUnit]).padStart(2, '0');
            });
        });
        return running;
    };

    if (update()) {
        const interval = setInterval(() => {
            if (!update()) clearInterval(interval);
        }, 1000);
    }
};

export default compact;
