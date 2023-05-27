<?php

namespace App\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_add_id_quality",
   *   name="id_quality",
   *   in="query",
   *   required=true,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_add_title",
   *   name="title",
   *   in="query",
   *   required=true,
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   */
  public function rules() {
    return [
      'id_quality' => 'required|integer|exists:qualities,id',
      'title' => 'required|string|max:256',
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
