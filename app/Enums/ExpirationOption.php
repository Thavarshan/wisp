<?php

namespace App\Enums;

use Carbon\Carbon;
use InvalidArgumentException;

enum ExpirationOption: string
{
    case NEVER = 'never';
    case FIVE_MINUTES = '5m';
    case THIRTY_MINUTES = '30m';
    case ONE_HOUR = '1h';
    case SIX_HOURS = '6h';
    case TWELVE_HOURS = '12h';
    case ONE_DAY = '1d';
    case TWO_DAYS = '2d';
    case ONE_WEEK = '1w';
    case ONE_MONTH = '1m';
    case ONE_YEAR = '1y';

    /**
     * Parse the expiration option into a Carbon instance.
     */
    public static function parse(string $option): ?Carbon
    {
        // Check if the option is a valid enum case
        if (! ExpirationOption::tryFrom($option)) {
            throw new InvalidArgumentException("Invalid expiration option: $option");
        }

        // Return Carbon instance based on the duration
        return match ($option) {
            self::NEVER->value => null,
            self::FIVE_MINUTES->value => Carbon::now()->addMinutes(5),
            self::THIRTY_MINUTES->value => Carbon::now()->addMinutes(30),
            self::ONE_HOUR->value => Carbon::now()->addHour(),
            self::SIX_HOURS->value => Carbon::now()->addHours(6),
            self::TWELVE_HOURS->value => Carbon::now()->addHours(12),
            self::ONE_DAY->value => Carbon::now()->addDay(),
            self::TWO_DAYS->value => Carbon::now()->addDays(2),
            self::ONE_WEEK->value => Carbon::now()->addWeek(),
            self::ONE_MONTH->value => Carbon::now()->addMonth(),
            self::ONE_YEAR->value => Carbon::now()->addYear(),
            default => throw new InvalidArgumentException("Invalid expiration option: $option"),
        };
    }
}
