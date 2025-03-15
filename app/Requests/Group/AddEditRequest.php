<?php

namespace App\Requests\Group;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
    /** @disregard TypeInvalid */
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }
}
