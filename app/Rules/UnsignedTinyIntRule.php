<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedTinyIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 255 || $value < 0) {
      $fail(ucfirst($attribute) . ' must not be greater than 255 or less than 0');
    }
  }
}
