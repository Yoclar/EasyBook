<?php
namespace Tests\Unit;

use App\Rules\DateValidationForBookingRule;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DateValidationForBookingRuleTest extends TestCase
{
    public function test_future_date_passes()
    {
        $rule = new DateValidationForBookingRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $rule->validate('start_time', Carbon::now()->addHour()->toDateTimeString(), $fail);
        
        $this->assertFalse($called, 'Expected validation to pass, but it failed.');
    }

    public function test_past_date_fails()
    {
        $rule = new DateValidationForBookingRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $rule->validate('start_time', Carbon::now()->subHour()->toDateTimeString(), $fail);

        $this->assertTrue($called, 'Expected validation to fail, but it passed.');
    }

    public function test_invalid_date_string_fails_gracefully()
    {
        $rule = new DateValidationForBookingRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $rule->validate('start_time', 'invalid-date-string', $fail);
        $this->assertTrue($called);
    }
}
