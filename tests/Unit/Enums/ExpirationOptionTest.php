<?php

namespace Tests\Unit\Enums;

use App\Enums\ExpirationOption;
use Carbon\Carbon;
use Tests\TestCase;

class ExpirationOptionTest extends TestCase
{
    public function test_options_are_the_single_source_of_truth_for_the_frontend(): void
    {
        $this->assertSame([
            ['value' => '5m', 'label' => '5 minutes'],
            ['value' => '30m', 'label' => '30 minutes'],
            ['value' => '1h', 'label' => '1 hour'],
            ['value' => '6h', 'label' => '6 hours'],
            ['value' => '12h', 'label' => '12 hours'],
            ['value' => '1d', 'label' => '1 day'],
            ['value' => '2d', 'label' => '2 days'],
            ['value' => '1w', 'label' => '1 week'],
        ], ExpirationOption::options());
    }

    public function test_each_option_calculates_a_finite_expiration(): void
    {
        $from = Carbon::parse('2026-01-01 12:00:00');
        foreach (ExpirationOption::cases() as $option) {
            $this->assertGreaterThan($from, $option->expiresAt($from));
        }
    }
}
