<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DateValidationForBookingRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $now = Carbon::now();
        $bookingTime = Carbon::parse($value);
        if ($bookingTime->lessThan($now)) {
            \Jeybin\Toastr\Toastr::error('You cannot book for past time.')->timeOut(5000)->toast();
            $fail('You cannot book for past time.');
        }
    }
}
