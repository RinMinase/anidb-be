<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YearRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if (!is_numeric($value)) {
      $fail($attribute . ' should be an integer');
    }

    if (intval($value) < 1900 || intval($value) > 2999) {
      $fail($attribute . ' should be a valid year format from 1900-2999');
    }
  }
}
