const countdown = () => {
    const element = document.getElementById('a-countdown');
    if (!element) return;
    element.innerHTML = '<span class="a-countdown-label">Ends in</span><span class="a-countdown-value"></span>';
    const value = element.querySelector('.a-countdown-value');
    function updateCountdown() {
        const endDate = new Date("2030-04-30T00:00:00");
        const now = new Date();

        if (endDate <= now) {
            value.textContent = "Countdown finished!";
            return;
        }

        let years = endDate.getFullYear() - now.getFullYear();
        let months = endDate.getMonth() - now.getMonth();
        let days = endDate.getDate() - now.getDate();
        let hours = endDate.getHours() - now.getHours();
        let minutes = endDate.getMinutes() - now.getMinutes();
        let seconds = endDate.getSeconds() - now.getSeconds();

        if (seconds < 0) {
            seconds += 60;
            minutes--;
        }
        if (minutes < 0) {
            minutes += 60;
            hours--;
        }
        if (hours < 0) {
            hours += 24;
            days--;
        }
        if (days < 0) {
            const prevMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0);
            days += prevMonth.getDate();
            months--;
        }
        if (months < 0) {
            months += 12;
            years--;
        }

        value.textContent =
            years + "y " +
            months + "m " +
            days + "d " +
            String(hours).padStart(2, '0') + ":" +
            String(minutes).padStart(2, '0') + ":" +
            String(seconds).padStart(2, '0');
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
};

export default countdown;
