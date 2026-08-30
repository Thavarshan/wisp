const ACCESS_TOKEN_PATTERN = /^[0-9a-f]{64}$/;

/**
 * Take the access token out of the URL fragment and remove it from history.
 * The token is deliberately kept in memory only; fragments are never sent
 * to the server or included in HTTP access logs.
 */
export function consumeAccessToken(): string | null {
    const token = window.location.hash.slice(1);

    window.history.replaceState(null, document.title, `${window.location.pathname}${window.location.search}`);

    return ACCESS_TOKEN_PATTERN.test(token) ? token : null;
}
