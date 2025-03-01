<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SignedTinyIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 127 || $value < -127) {
      $fail(ucfirst($attribute) . ' must not be greater than 127 or less than -127');
    }
  }
}
