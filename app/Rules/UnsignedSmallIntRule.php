<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedSmallIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 65535 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 65535 or less than 0');
    }
  }
}
