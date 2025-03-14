<?php

namespace App\Support;

use Hashids\Hashids;
use Hashids\HashidsInterface;

class HashId
{
    /**
     * Default UID character length.
     */
    public const CHARACTER_LENGTH = 7;

    /**
     * String of acceptable characters.
     *
     * @var string
     */
    protected static $characterPool = 'ABCDEFGHJKLMNOPQRSTUVWXYZ23456789';

    /**
     * Generate a new and unique code.
     */
    public static function encode(int|string $id): string
    {
        if (is_string($id) && is_numeric($id)) {
            $id = (int) $id;
        }

        return static::createHasher()->encode($id);
    }

    /**
     * Decode the given hash.
     *
     *
     * @return array<int>
     */
    public static function decode(string $hash): array
    {
        return static::createHasher()->decode($hash);
    }

    /**
     * Create Hashids instance.
     */
    protected static function createHasher(?string $key = null): HashidsInterface
    {
        return new Hashids(
            salt: static::generateSalt($key),
            minHashLength: self::CHARACTER_LENGTH,
            alphabet: static::$characterPool
        );
    }

    /**
     * Generate a salt value for the Hashids instance.
     */
    protected static function generateSalt(?string $key = null): string
    {
        // Retrieve the APP_KEY from the Laravel configuration
        $appKey = config('app.key', $key);

        // Ensure the key is in a compatible format, since APP_KEY may be base64-encoded
        if (substr($appKey, 0, 7) === 'base64:') {
            $appKey = base64_decode(substr($appKey, 7));
        }

        // Escape the salt value for SQL safety
        return addslashes($appKey);
    }
}
