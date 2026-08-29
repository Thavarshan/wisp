import { computed, onMounted, onUnmounted, readonly, ref } from 'vue';

export function useSecretTimer(expiredAt: string, onExpired?: () => void) {
    const countdown = ref('');
    let intervalId: number | undefined;
    let expirationTimeout: number | undefined;
    let expiredCallbackCalled = false;

    const expirationTime = computed(() => new Date(expiredAt).getTime());
    const isExpired = ref(false);

    function updateCountdown() {
        const timeLeft = expirationTime.value - Date.now();

        if (timeLeft <= 0) {
            countdown.value = 'Expired';
            isExpired.value = true;
            if (intervalId !== undefined) {
                clearInterval(intervalId);
                intervalId = undefined;
            }
            if (expirationTimeout !== undefined) {
                clearTimeout(expirationTimeout);
                expirationTimeout = undefined;
            }
            if (!expiredCallbackCalled) {
                expiredCallbackCalled = true;
                onExpired?.();
            }
        } else {
            const totalSeconds = Math.floor(timeLeft / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            isExpired.value = false;

            if (hours > 0) {
                countdown.value = `${hours}h ${minutes}m ${seconds}s`;
            } else {
                countdown.value = `${minutes}m ${seconds}s`;
            }
        }
    }

    function setupExpirationTimeout() {
        if (expirationTimeout !== undefined) {
            clearTimeout(expirationTimeout);
        }

        const timeLeft = expirationTime.value - Date.now();

        if (timeLeft > 0) {
            expirationTimeout = window.setTimeout(updateCountdown, timeLeft);
        }
    }

    function startTimer() {
        stopTimer();
        updateCountdown();
        if (!isExpired.value) {
            intervalId = window.setInterval(updateCountdown, 1000);
        }
        setupExpirationTimeout();
    }

    function stopTimer() {
        if (intervalId !== undefined) {
            clearInterval(intervalId);
            intervalId = undefined;
        }
        if (expirationTimeout !== undefined) {
            clearTimeout(expirationTimeout);
            expirationTimeout = undefined;
        }
    }

    onMounted(startTimer);
    onUnmounted(stopTimer);

    return {
        countdown: readonly(countdown),
        isExpired: readonly(isExpired),
        expirationTime: readonly(expirationTime),
        startTimer,
        stopTimer,
    };
}
