<?php

namespace App\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class EditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_edit_id_quality",
   *   name="id_quality",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_title",
   *   name="title",
   *   in="query",
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   */
  public function rules() {
    return [
      'id_quality' => 'integer|exists:qualities,id',
      'title' => 'string|max:256',
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
