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

    // Use crypto.getRandomValues if available for better randomness
    if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
        const array = new Uint32Array(length);
        crypto.getRandomValues(array);

        for (let i = 0; i < length; i++) {
            password += charset[array[i] % charset.length];
        }
    } else {
        // Fallback to Math.random (less secure but compatible)
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
    }

    return password;
};

export function getExpirationDate(duration: string): string | null {
    const now = new Date();
    const timeUnits: { [key: string]: (date: Date, value: number) => void; } = {
        mins: (date, value) => date.setMinutes(date.getMinutes() + value),
        hour: (date, value) => date.setHours(date.getHours() + value),
        hours: (date, value) => date.setHours(date.getHours() + value),
        day: (date, value) => date.setDate(date.getDate() + value),
        days: (date, value) => date.setDate(date.getDate() + value),
    };

    for (const unit in timeUnits) {
        if (duration.endsWith(unit)) {
            const value = parseInt(duration.slice(0, -unit.length).trim());
            if (isNaN(value)) return null;  // Invalid number
            timeUnits[unit](now, value);
            return now.toISOString();  // Return ISO date string
        }
    }

    return null;  // Return null if no valid unit found
}

export function extractSecretUid(url: string): string | null {
    try {
        const parsedUrl = new URL(url);
        const pathParts = parsedUrl.pathname.split('/').filter(Boolean); // Split path and remove empty parts

        // Check if the URL contains the expected path structure
        if (pathParts.length >= 2 && pathParts[0] === 'secrets') {
            const uid = pathParts[1];
            return uid || null;
        } else {
            return null;
        }
    } catch {
        return null;
    }
}

export function getUidFromRoute(): string | undefined {
    return window.location.pathname.split('/').pop();
}
