<?php

namespace App\Requests\Partial;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="partial_add_edit_id_catalog",
   *   name="id_catalog",
   *   in="query",
   *   required=true,
   *   example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   * @OA\Parameter(
   *   parameter="partial_add_edit_id_priority",
   *   name="id_priority",
   *   in="query",
   *   required=true,
   *   example=1,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="partial_add_edit_title",
   *   name="title",
   *   in="query",
   *   required=true,
   *   example="Partial Title",
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   */
  public function rules() {
    return [
      'id_catalog' => ['required', 'uuid', 'exists:catalogs,uuid'],
      'id_priority' => ['required', 'integer', 'exists:priorities,id'],
      'title' => ['required', 'string', 'max:256'],
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
