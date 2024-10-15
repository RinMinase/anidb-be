<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 4294967295 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 4294967295 or less than 0');
    }
  }
}
