<?php

namespace App\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="group_add_edit_name",
   *   name="name",
   *   in="query",
   *   required=true,
   *   example="Sample Group Name",
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   */
  public function rules() {
    return [
      'name' => ['required', 'string', 'max:64'],
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
