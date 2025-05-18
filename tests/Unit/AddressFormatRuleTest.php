<?php

namespace Tests\Unit;

use App\Rules\AddressFormatRule;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AddressFormatRuleTest extends TestCase
{
    public function test_regex_correct_format_passes()
    {
        $rule = new AddressFormatRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $testAddress = "1133, Budapest, Tisza utca 26";
        $rule->validate('address', $testAddress, $fail);

        $this->assertFalse($called, 'Expected validation to pass, but it failed.');
    }

    public function test_regex_incorrect_format_extra_whitespaces_fails()
    {
        $rule = new AddressFormatRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $testAddress = "1133  , Budapest , Tisza utca 26";
        $rule->validate('address', $testAddress, $fail);

        $this->assertTrue($called, 'Expected validation to fail, but it passed.');
    }

    public function test_regex_incorrect_format_wrong_order_fails()
    {
        $rule = new AddressFormatRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $testAddress = "Budapest, 1133, Tisza utca 26";
        $rule->validate('address', $testAddress, $fail);

        $this->assertTrue($called, 'Expected validation to fail, but it passed.');
    }

    public function test_regex_incorrect_no_postal_code_fails()
    {
        $rule = new AddressFormatRule();

        $called = false;
        $fail = function () use (&$called) {
            $called = true;
        };

        $testAddress = "Budapest, Tisza utca 26";
        $rule->validate('address', $testAddress, $fail);

        $this->assertTrue($called, 'Expected validation to fail, but it passed.');
    }
}

