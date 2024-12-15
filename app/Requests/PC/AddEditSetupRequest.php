<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\JsonRule;

class AddEditSetupRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_add_edit_setup_id_owner",
   *   name="id_owner",
   *   in="query",
   *   required=true,
   *   example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_setup_id_info",
   *   name="id_info",
   *   in="query",
   *   required=true,
   *   example="87d66263-269c-4f7c-9fb8-dd78c4408ff6",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_setup_components",
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
      'id_owner' => ['required', 'string', 'exist:pc_owners,uuid'],
      'id_info' => ['required', 'string', 'exist:pc_infos,uuid'],
      'components' => ['required', 'string', new JsonRule],
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
