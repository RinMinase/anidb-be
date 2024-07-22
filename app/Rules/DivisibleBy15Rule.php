<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function Fuse\Helpers\Types\isNumber;

class DivisibleBy15Rule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if (!isNumber($value) || $value % 15 !== 0) {
      $fail($attribute . ' must be divisible by 15');
    }
  }
}
