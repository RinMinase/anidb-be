<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SignedIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 2147483647 || $value < -2147483647) {
      $fail($attribute . ' must not be greater than 2147483647 or -2147483647');
    }
  }
}
