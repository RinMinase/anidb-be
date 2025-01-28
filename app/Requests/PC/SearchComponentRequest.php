<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchComponentRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_search_component_id_type",
   *   name="id_type",
   *   in="query",
   *   example=1,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   */
  public function rules() {
    return [
      'id_type' => ['nullable', 'integer', 'exists:pc_component_types,id'],
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
