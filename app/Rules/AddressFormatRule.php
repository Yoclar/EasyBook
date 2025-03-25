<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressFormatRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^\d{4},\s[A-ZÁÉÍÓÖŐÚÜŰa-záéíóöőúüű]+,\s[A-ZÁÉÍÓÖŐÚÜŰa-záéíóöőúüű]+\s[\w\s]+\s\d+$/u';

        if (! preg_match($pattern, $value)) {
            $fail("The {$attribute} must be in the format '1133, Budapest, Tisza utca 26'.");
        }
    }
}
