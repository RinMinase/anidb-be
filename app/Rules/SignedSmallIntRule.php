<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SignedSmallIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 32767 || $value < -32767) {
      $fail($attribute . ' must not be greater than 32767 or -32767');
    }
  }
}
