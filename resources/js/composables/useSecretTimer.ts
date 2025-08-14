import { ref, computed, onMounted, onUnmounted, watch, readonly } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import duration from 'dayjs/plugin/duration';

dayjs.extend(relativeTime);
dayjs.extend(duration);

export function useSecretTimer(expiredAt: string, onExpired?: () => void) {
    const countdown = ref('');
    const intervalId = ref<number | undefined>(undefined);
    const expirationTimeout = ref<number | null>(null);

    const expirationTime = computed(() => dayjs(expiredAt));
    const isExpired = computed(() => expirationTime.value.isBefore(dayjs()));

    function updateCountdown() {
        const now = dayjs();
        const timeLeft = expirationTime.value.diff(now);

        if (timeLeft <= 0) {
            countdown.value = 'Expired';
            clearInterval(intervalId.value);
            onExpired?.();
        } else {
            const durationLeft = dayjs.duration(timeLeft);
            const hours = durationLeft.hours();
            const minutes = durationLeft.minutes();
            const seconds = durationLeft.seconds();

            if (hours > 0) {
                countdown.value = `${hours}h ${minutes}m ${seconds}s`;
            } else {
                countdown.value = `${minutes}m ${seconds}s`;
            }
        }
    }

    function setupExpirationTimeout() {
        if (expirationTimeout.value) {
            clearTimeout(expirationTimeout.value);
        }

        const now = dayjs();
        const timeLeft = expirationTime.value.diff(now);

        if (timeLeft > 0) {
            expirationTimeout.value = setTimeout(() => onExpired?.(), timeLeft);
        }
    }

    function startTimer() {
        updateCountdown();
        intervalId.value = setInterval(updateCountdown, 1000);
        setupExpirationTimeout();
    }

    function stopTimer() {
        if (intervalId.value) {
            clearInterval(intervalId.value);
            intervalId.value = undefined;
        }
        if (expirationTimeout.value) {
            clearTimeout(expirationTimeout.value);
            expirationTimeout.value = null;
        }
    }

    // Watch for changes in expiration time
    watch(() => expiredAt, () => {
        stopTimer();
        startTimer();
    }, { immediate: true });

    onMounted(startTimer);
    onUnmounted(stopTimer);

    return {
        countdown: readonly(countdown),
        isExpired: readonly(isExpired),
        expirationTime: readonly(expirationTime),
        startTimer,
        stopTimer
    };
}
