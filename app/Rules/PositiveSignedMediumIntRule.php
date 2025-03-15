<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PositiveSignedMediumIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 8388607 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 8388607 and should be a positive number');
    }
  }
}
