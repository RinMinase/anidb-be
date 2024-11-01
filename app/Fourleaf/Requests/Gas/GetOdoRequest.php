<?php

namespace App\Fourleaf\Requests\Gas;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\YearRule;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_gas_get_odo_year",
 *   name="year",
 *   in="query",
 *   required=true,
 *   example="2020",
 *   @OA\Schema(ref="#/components/schemas/YearSchema"),
 * ),
 */
class GetOdoRequest extends FormRequest {
  public function rules() {
    return [
      'year' => [new YearRule],
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
