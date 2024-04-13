<?php

namespace App\Fourleaf\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditMaintenanceRequest extends FormRequest {
  public function rules() {
    return [
      'date' => ['required', 'string', 'date', 'before_or_equal:today'],
      'part' => ['required', 'string'],
      'odometer' => ['required', 'integer', 'min:0'],
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
