<?php

namespace App\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddRequest extends FormRequest {

  public function rules() {
    return [
      'description' => 'required|string|max:16|unique:catalogs',
      'order' => 'integer|min:1|max:32767|unique:catalogs',
    ];
  }

  public function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ])
    );
  }

  public function messages() {
    $validation = require config_path('validation.php');

    return array_merge($validation, []);
  }
}
