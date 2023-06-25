<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddRewatchRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_add_rewatch_date_rewatched",
   *   name="date_rewatched",
   *   in="query",
   *   required=true,
   *   example="2022-01-23",
   *   @OA\Schema(type="string", format="date"),
   * ),
   */
  public function rules() {
    return [
      'date_rewatched' => ['required', 'date', 'before_or_equal:today'],
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
