<?php

namespace App\Fourleaf\Requests\Bills;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\YearRule;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_bills_electricity_get_year",
 *   name="year",
 *   in="query",
 *   example="2020",
 *   @OA\Schema(ref="#/components/schemas/YearSchema"),
 * ),
 */
class GetBillsElectricityRequest extends FormRequest {
  public function rules() {
    return [
      'year' => ['nullable', new YearRule],
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
