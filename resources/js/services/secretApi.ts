import { route as ziggyRoute, type Config } from 'ziggy-js';

import type {
    CreatedSecret,
    CreateSecretPayload,
    ExpirationOption,
    RevealedSecret,
    RevealSecretPayload,
    SecretApiErrorKind,
    ValidationErrors,
} from '@/types/secret';

let ziggyConfig: Config | undefined;

export function configureSecretApi(config: Config): void {
    ziggyConfig = config;
}

function apiRoute(name: string, params?: Record<string, string>): string {
    if (typeof route !== 'undefined') {
        return route(name, params);
    }

    return ziggyRoute(name as never, params as never, true, ziggyConfig);
}

interface CreatedSecretResponse {
    secret_id: string;
    share_url: string;
    revocation_token: string;
    expires_at: string;
    expiration: ExpirationOption;
}

interface JsonObject {
    [key: string]: unknown;
}

export class SecretApiError extends Error {
    constructor(
        public readonly status: number,
        public readonly kind: SecretApiErrorKind,
        message: string,
        public readonly fieldErrors: ValidationErrors = {},
    ) {
        super(message);
        this.name = 'SecretApiError';
    }
}

function isJsonObject(value: unknown): value is JsonObject {
    return typeof value === 'object' && value !== null;
}

function validationErrors(value: unknown): ValidationErrors {
    if (!isJsonObject(value) || !isJsonObject(value.errors)) {
        return {};
    }

    return Object.fromEntries(
        Object.entries(value.errors).filter(([, messages]) => Array.isArray(messages) && messages.every((message) => typeof message === 'string')),
    ) as ValidationErrors;
}

function errorDetails(status: number): {
    kind: SecretApiErrorKind;
    message: string;
} {
    switch (status) {
        case 419:
            return {
                kind: 'session',
                message: 'Your session expired. Refresh the page and try again.',
            };
        case 404:
            return {
                kind: 'not_found',
                message: 'This secret is no longer available.',
            };
        case 410:
            return {
                kind: 'expired',
                message: 'This secret has expired.',
            };
        case 422:
            return {
                kind: 'validation',
                message: 'Check the highlighted fields and try again.',
            };
        case 429:
            return {
                kind: 'rate_limit',
                message: 'Too many attempts. Please wait a moment and try again.',
            };
        default:
            return {
                kind: status >= 500 ? 'server' : 'network',
                message: 'Something went wrong. Please try again.',
            };
    }
}

async function request<T>(url: string, options: RequestInit): Promise<T> {
    let response: Response;

    try {
        response = await fetch(url, {
            ...options,
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
                ...options.headers,
            },
        });
    } catch {
        throw new SecretApiError(0, 'network', 'We could not reach Wisp. Check your connection and try again.');
    }

    const text = await response.text();
    let payload: unknown = null;

    if (text) {
        try {
            payload = JSON.parse(text);
        } catch {
            payload = null;
        }
    }

    if (!response.ok) {
        const details = errorDetails(response.status);

        throw new SecretApiError(response.status, details.kind, details.message, validationErrors(payload));
    }

    if (response.status === 204) {
        return undefined as T;
    }

    if (!isJsonObject(payload)) {
        throw new SecretApiError(response.status, 'server', 'Wisp returned an unexpected response. Please try again.');
    }

    return payload as T;
}

export async function createSecret(payload: CreateSecretPayload, signal?: AbortSignal): Promise<CreatedSecret> {
    const result = await request<CreatedSecretResponse>(apiRoute('secrets.store'), {
        method: 'POST',
        body: JSON.stringify(payload),
        signal,
    });

    return {
        secretId: result.secret_id,
        shareUrl: result.share_url,
        revocationToken: result.revocation_token,
        expiresAt: result.expires_at,
        expiration: result.expiration,
        password: payload.password,
    };
}

export function revealSecret(secretId: string, payload: RevealSecretPayload, signal?: AbortSignal): Promise<RevealedSecret> {
    return request<RevealedSecret>(apiRoute('secrets.reveal', { secret_id: secretId }), {
        method: 'POST',
        body: JSON.stringify(payload),
        signal,
    });
}

export function revokeSecret(secretId: string, revocationToken: string, signal?: AbortSignal): Promise<void> {
    return request<void>(apiRoute('secrets.revoke', { secret_id: secretId }), {
        method: 'DELETE',
        body: JSON.stringify({ revocation_token: revocationToken }),
        signal,
    });
}
