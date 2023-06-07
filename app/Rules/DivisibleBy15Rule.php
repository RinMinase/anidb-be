<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DivisibleBy15Rule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value % 15 !== 0) {
      $fail($attribute . ' must be divisible by 15');
    }
  }
}
