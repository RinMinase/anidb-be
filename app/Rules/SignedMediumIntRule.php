<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SignedMediumIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 8388607 || $value < -8388607) {
      $fail($attribute . ' must not be greater than 8388607 or -8388607');
    }
  }
}
