<?php

namespace App\Enums;

use Carbon\Carbon;

enum ExpirationOption: string
{
    case FIVE_MINUTES = '5m';
    case THIRTY_MINUTES = '30m';
    case ONE_HOUR = '1h';
    case SIX_HOURS = '6h';
    case TWELVE_HOURS = '12h';
    case ONE_DAY = '1d';
    case TWO_DAYS = '2d';
    case ONE_WEEK = '1w';

    /**
     * Get the human-readable label for this expiration option.
     *
     * @return string The label displayed in the creation form.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIVE_MINUTES => '5 minutes',
            self::THIRTY_MINUTES => '30 minutes',
            self::ONE_HOUR => '1 hour',
            self::SIX_HOURS => '6 hours',
            self::TWELVE_HOURS => '12 hours',
            self::ONE_DAY => '1 day',
            self::TWO_DAYS => '2 days',
            self::ONE_WEEK => '1 week',
        };
    }

    /**
     * Calculate the expiration timestamp from the supplied starting point.
     *
     * @param  Carbon|null  $from  The starting point, or the current time.
     * @return Carbon The calculated expiration timestamp.
     */
    public function expiresAt(?Carbon $from = null): Carbon
    {
        return match ($this) {
            self::FIVE_MINUTES => ($from ?? now())->copy()->addMinutes(5),
            self::THIRTY_MINUTES => ($from ?? now())->copy()->addMinutes(30),
            self::ONE_HOUR => ($from ?? now())->copy()->addHour(),
            self::SIX_HOURS => ($from ?? now())->copy()->addHours(6),
            self::TWELVE_HOURS => ($from ?? now())->copy()->addHours(12),
            self::ONE_DAY => ($from ?? now())->copy()->addDay(),
            self::TWO_DAYS => ($from ?? now())->copy()->addDays(2),
            self::ONE_WEEK => ($from ?? now())->copy()->addWeek(),
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $option): array => [
                'value' => $option->value,
                'label' => $option->label(),
            ],
            self::cases(),
        );
    }
}
