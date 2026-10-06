const countdown = () => {
    function updateCountdown() {
        const endDate = new Date("2030-04-30T00:00:00");
        const now = new Date();

        if (endDate <= now) {
            document.getElementById("a-countdown").innerHTML = "Countdown finished!";
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

        document.getElementById("a-countdown").innerHTML =
            years + "y " +
            months + "m " +
            days + "d " +
            hours + "h " +
            minutes + "m " +
            seconds + "s";
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
};

export default countdown;
