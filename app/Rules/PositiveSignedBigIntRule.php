<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PositiveSignedBigIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 9223372036854775807 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 9223372036854775807 and should be a positive number');
    }
  }
}
