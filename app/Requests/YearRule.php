<?php

namespace App\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YearRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if (!is_int($value) || $value < 1900 || $value > 2999) {
      $fail($attribute . ' should be a valid year format from 1900-2999');
    }
  }
}
