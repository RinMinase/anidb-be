<?php

namespace App\Requests\Codec;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="codec_add_edit_codec",
   *   name="codec",
   *   in="query",
   *   required=true,
   *   example="Sample Codec",
   *   @OA\Schema(type="string", minLength=1, maxLength=16),
   * ),
   * @OA\Parameter(
   *   parameter="codec_add_edit_order",
   *   name="order",
   *   in="query",
   *   example="1",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   */
  public function rules() {
    return [
      'codec' => ['required', 'string', 'max:16'],
      'order' => ['integer', 'min:0'],
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
