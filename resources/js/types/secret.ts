export const MAX_SECRET_LENGTH = 10_000;
export const MAX_PASSWORD_LENGTH = 255;

export type ExpirationValue = '5m' | '30m' | '1h' | '6h' | '12h' | '1d' | '2d' | '1w';

export interface ExpirationOption {
    value: ExpirationValue;
    label: string;
}

export interface CreateSecretPayload {
    content: string;
    expiration: ExpirationValue;
    password: string | null;
}

export interface CreatedSecret {
    accessToken: string;
    shareUrl: string;
    revocationToken: string;
    expiresAt: string;
    expiration: ExpirationOption;
    password: string | null;
}

export interface RevealSecretPayload {
    password: string | null;
}

export interface RevealedSecret {
    content: string;
}

export type ValidationErrors = Partial<Record<keyof CreateSecretPayload | 'password', string[]>>;

export type SecretApiErrorKind = 'validation' | 'session' | 'rate_limit' | 'not_found' | 'expired' | 'server' | 'network';
