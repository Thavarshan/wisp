import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function generateSecurePassword(length = 12): string {
    const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';

    // Validate input
    if (length < 8 || length > 128) {
        throw new Error('Password length must be between 8 and 128 characters');
    }

    let password = '';

    if (typeof crypto === 'undefined' || !crypto.getRandomValues) {
        throw new Error('Secure random number generation is unavailable');
    }

    const maxUnbiasedValue = Math.floor(0x100000000 / charset.length) * charset.length;
    const array = new Uint32Array(length);
    let index = 0;

    while (index < length) {
        crypto.getRandomValues(array);

        for (const randomValue of array) {
            if (randomValue >= maxUnbiasedValue) continue;
            password += charset[randomValue % charset.length];
            index++;
            if (index === length) break;
        }
    }

    return password;
}
