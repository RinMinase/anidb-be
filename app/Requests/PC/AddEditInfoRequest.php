<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditInfoRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_add_edit_info_id_owner",
   *   name="id_owner",
   *   in="query",
   *   required=true,
   *   example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_info_label",
   *   name="label",
   *   in="query",
   *   required=true,
   *   @OA\Schema(type="string", minLength=1, maxLength=128),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_info_is_active",
   *   name="is_active",
   *   in="query",
   *   @OA\Schema(type="boolean"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_info_is_hidden",
   *   name="is_hidden",
   *   in="query",
   *   @OA\Schema(type="boolean"),
   * ),
   */
  public function rules() {
    return [
      'id_owner' => ['required', 'string', 'uuid', 'exists:pc_owners,uuid'],
      'label' => ['required', 'string', 'max:128'],
      'is_active' => ['nullable', 'boolean'],
      'is_hidden' => ['nullable', 'boolean'],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'is_active' => to_boolean($this->is_active, true),
      'is_hidden' => to_boolean($this->is_hidden, true),
    ]);
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
