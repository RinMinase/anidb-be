<?php

namespace App\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnsigneSmallIntRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if ($value > 65535) {
      $fail($attribute . ' must not be greater than 65535');
    }
  }
}
