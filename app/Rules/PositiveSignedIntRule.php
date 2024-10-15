<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PositiveSignedIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 2147483647 || $value < -2147483647) {
      $fail(ucfirst($attribute) . ' must not be greater than 2147483647 and should be a positive number');
    }
  }
}
