<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedBigIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 18446744073709551615) {
      $fail($attribute . ' must not be greater than 18446744073709551615');
    }
  }
}
