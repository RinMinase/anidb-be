<?php

namespace App\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsignedMediumIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 16777215) {
      $fail($attribute . ' must not be greater than 16777215');
    }
  }
}
