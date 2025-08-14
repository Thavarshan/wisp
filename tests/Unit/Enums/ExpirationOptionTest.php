<?php

namespace Tests\Unit\Enums;

use App\Enums\ExpirationOption;
use Carbon\Carbon;
use InvalidArgumentException;
use Tests\TestCase;

class ExpirationOptionTest extends TestCase
{
    public function test_it_has_correct_values()
    {
        $this->assertEquals('never', ExpirationOption::NEVER->value);
        $this->assertEquals('5m', ExpirationOption::FIVE_MINUTES->value);
        $this->assertEquals('30m', ExpirationOption::THIRTY_MINUTES->value);
        $this->assertEquals('1h', ExpirationOption::ONE_HOUR->value);
        $this->assertEquals('6h', ExpirationOption::SIX_HOURS->value);
        $this->assertEquals('12h', ExpirationOption::TWELVE_HOURS->value);
        $this->assertEquals('1d', ExpirationOption::ONE_DAY->value);
        $this->assertEquals('2d', ExpirationOption::TWO_DAYS->value);
        $this->assertEquals('1w', ExpirationOption::ONE_WEEK->value);
        $this->assertEquals('1m', ExpirationOption::ONE_MONTH->value);
        $this->assertEquals('1y', ExpirationOption::ONE_YEAR->value);
    }

    public function test_it_parses_never_correctly()
    {
        $result = ExpirationOption::parse('never');

        $this->assertNull($result);
    }

    public function test_it_parses_time_durations_correctly()
    {
        Carbon::setTestNow(Carbon::parse('2025-01-01 12:00:00'));

        $fiveMinutes = ExpirationOption::parse('5m');
        $this->assertEquals('2025-01-01 12:05:00', $fiveMinutes->format('Y-m-d H:i:s'));

        $thirtyMinutes = ExpirationOption::parse('30m');
        $this->assertEquals('2025-01-01 12:30:00', $thirtyMinutes->format('Y-m-d H:i:s'));

        $oneHour = ExpirationOption::parse('1h');
        $this->assertEquals('2025-01-01 13:00:00', $oneHour->format('Y-m-d H:i:s'));

        $oneDay = ExpirationOption::parse('1d');
        $this->assertEquals('2025-01-02 12:00:00', $oneDay->format('Y-m-d H:i:s'));

        $oneWeek = ExpirationOption::parse('1w');
        $this->assertEquals('2025-01-08 12:00:00', $oneWeek->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_it_throws_exception_for_invalid_option()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid expiration option: invalid');

        ExpirationOption::parse('invalid');
    }

    public function test_all_enum_cases_can_be_parsed()
    {
        $cases = ExpirationOption::cases();

        foreach ($cases as $case) {
            $result = ExpirationOption::parse($case->value);

            if ($case === ExpirationOption::NEVER) {
                $this->assertNull($result);
            } else {
                $this->assertInstanceOf(Carbon::class, $result);
                $this->assertGreaterThan(Carbon::now(), $result);
            }
        }
    }
}
