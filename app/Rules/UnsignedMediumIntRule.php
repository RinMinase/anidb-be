<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedMediumIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 16777215 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 16777215 or less than 0');
    }
  }
}
