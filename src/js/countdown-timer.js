function countdownTimerInstance($container) {
    console.log($container);
    const countDownDate = new Date();
    countDownDate.setMinutes(countDownDate.getMinutes() + 20);
    const countDownTime = countDownDate.getTime();

    const $seconds = $container.querySelector('.countdown-timer__seconds')
    const $minutes = $container.querySelector('.countdown-timer__minutes')
    const $hours = $container.querySelector('.countdown-timer__hours')
    const $days = $container.querySelector('.countdown-timer__days')

    console.log($container.querySelector('.countdown-timer__seconds'));

    function withLeadingZero(value) {
        return value < 10 ? `0${value}` : value;
    }

    let lastDays = 0;
    let lastHours = 0;
    let lastMinutes = 0;
    let lastSeconds = 0;

    const x = setInterval(() => {
        const now = new Date().getTime();
        const distance = countDownTime - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

//        document.getElementById("countdown").innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

        if ($days && lastDays !== days) {
            $days.innerHTML = withLeadingZero( days );
        }
        if ($hours && lastHours !== hours) {
            $hours.innerHTML = withLeadingZero( hours );
        }
        if ($minutes && lastMinutes !== minutes) {
            $minutes.innerHTML = withLeadingZero( minutes );
        }
        if ($seconds && lastSeconds !== seconds) {
            $seconds.innerHTML = withLeadingZero( seconds );
        }

        lastDays = days;
        lastHours = hours;
        lastMinutes = minutes;
        lastSeconds = seconds;

        if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "EXPIRED";
        }
    }, 200);
}

export function countdownTimer() {
    const countdown = document.querySelectorAll('.countdown-timer');
    if (!countdown) return;

    countdown.forEach((item) => countdownTimerInstance(item));
}
