<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditComponentTypeRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_type_type",
   *   name="type",
   *   in="query",
   *   required=true,
   *   example="sample type",
   *   @OA\Schema(type="string", minLength=1, maxLength=32),
   * ),
   */
  public function rules() {
    return [
      'type' => ['required', 'string', 'max:32'],
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
