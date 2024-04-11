<?php

namespace App\Fourleaf\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditFuelRequest extends FormRequest {
  public function rules() {
    return [
      'date' => ['required', 'string', 'date', 'before_or_equal:today'],
      'from_bars' => ['required', 'integer', 'min:0', 'max:9'],
      'to_bars' => ['required', 'integer', 'min:0', 'max:9'],

      // should not be lower than max value of db column
      'odometer' => ['required', 'integer', 'min:0'],

      'price_per_liter' => ['float', 'min:0'],
      'liters_filled' => ['float', 'min:0'],
    ];
  }

  public function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }

  public function messages() {
    $validation = require config_path('validation.php');

    return array_merge($validation, []);
  }
}
