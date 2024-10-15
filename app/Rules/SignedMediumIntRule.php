<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SignedMediumIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 8388607 || $value < -8388607) {
      $fail(ucfirst($attribute) . ' must not be greater than 8388607 or less than -8388607');
    }
  }
}
