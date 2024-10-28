<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\PositiveSignedTinyIntRule;

class LastWatchRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_last_items",
   *   name="items",
   *   description="Number of items to load",
   *   in="query",
   *   example=20,
   *   @OA\Schema(type="integer", format="int8", default=20, minimum=1, maximum=127),
   * ),
   */
  public function rules() {
    return [
      'items' => ['nullable', 'integer', 'min:1', new PositiveSignedTinyIntRule],
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
