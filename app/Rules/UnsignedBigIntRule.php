<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedBigIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 18446744073709551615 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 18446744073709551615 or less than 0');
    }
  }
}
