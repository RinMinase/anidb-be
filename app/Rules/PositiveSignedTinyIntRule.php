<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PositiveSignedTinyIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 127 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 127 and should be a positive number');
    }
  }
}
