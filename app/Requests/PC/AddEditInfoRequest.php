<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\JsonRule;

class AddEditInfoRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_add_edit_info_id_owner",
   *   name="id_owner",
   *   in="query",
   *   required=true,
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
   * @OA\Parameter(
   *   parameter="pc_add_edit_info_components",
   *   name="components",
   *   description="PC Setup Components JSON String",
   *   in="query",
   *   required=true,
   *   example="[{""id_component"":1,""count"":1,""is_hidden"":false},{""id_component"":10,""count"":2,""is_hidden"":true}]",
   *   @OA\Schema(type="string"),
   * ),
   */
  public function rules() {
    return [
      'id_owner' => ['required', 'string', 'exists:pc_owners,uuid'],
      'label' => ['required', 'string', 'max:128'],
      'is_active' => ['nullable', 'boolean'],
      'is_hidden' => ['nullable', 'boolean'],
      'components' => ['required', 'string', new JsonRule],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'is_active' => to_boolean($this->is_active, true),
      'is_hidden' => to_boolean($this->is_hidden, true),
    ]);
  }

  public function failedValidation(Validator $validator) {
    /** @disregard TypeInvalid */
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }
}
