<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure security headers for your application. These headers help
    | protect against common web vulnerabilities and attacks.
    |
    */

    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP)
    |--------------------------------------------------------------------------
    |
    | Configure Content Security Policy directives. The middleware will
    | automatically use relaxed settings for local development and
    | strict settings for production.
    |
    | Note: IPv6 localhost ([::1]) is not well-supported in CSP across all
    | browsers. We stick to localhost and 127.0.0.1 for development.
    |
    */

    'csp' => [
        'development' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' http://localhost:* http://127.0.0.1:*",
            'style-src' => "'self' 'unsafe-inline' http://localhost:* http://127.0.0.1:* https://fonts.bunny.net",
            'font-src' => "'self' https://fonts.bunny.net",
            'img-src' => "'self' data: blob:",
            'connect-src' => "'self' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*",
        ],

        'production' => [
            'default-src' => "'self'",
            'script-src' => "'self'",
            'style-src' => "'self' 'unsafe-inline' https://fonts.bunny.net",
            'font-src' => "'self' https://fonts.bunny.net",
            'img-src' => "'self' data:",
            'connect-src' => "'self'",
            'object-src' => "'none'",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Strict Transport Security (HSTS)
    |--------------------------------------------------------------------------
    |
    | HSTS header configuration. Only applied in production with HTTPS.
    |
    */

    'hsts' => [
        'max_age' => 31536000, // 1 year
        'include_subdomains' => true,
        'preload' => false,
    ],

];
