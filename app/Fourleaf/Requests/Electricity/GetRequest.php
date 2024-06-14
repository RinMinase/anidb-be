<?php

namespace App\Fourleaf\Requests\Electricity;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_electricity_get_year",
 *   name="year",
 *   in="query",
 *   required=true,
 *   example=2020,
 *   @OA\Schema(type="integer", format="int32", minimum=1900),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_electricity_get_month",
 *   name="month",
 *   in="query",
 *   required=true,
 *   example=1,
 *   @OA\Schema(type="integer", format="int32", minimum=1, maximum=12),
 * ),
 */
class GetRequest extends FormRequest {
  public function rules() {
    return [
      'year' => ['required', 'integer', 'min:1900', 'max:2099'],
      'month' => ['required', 'integer', 'min:1', 'max:12'],
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
