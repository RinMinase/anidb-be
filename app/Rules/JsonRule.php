<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JsonRule implements ValidationRule {

  public function validate(string $attribute, mixed $value, Closure $fail): void {
    if (!is_json($value)) {
      $fail($attribute . ' is not a valid JSON string');
    }
  }
}
