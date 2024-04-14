<?php

namespace App\Fourleaf\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_date",
 *   name="date",
 *   in="query",
 *   required=true,
 *   example="2020-10-20",
 *   @OA\Schema(type="string"),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_from_bars",
 *   name="from_bars",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="integer", format="int32", minimum=0, maximum=9),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_to_bars",
 *   name="to_bars",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="integer", format="int32", minimum=0, maximum=9),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_odometer",
 *   name="odometer",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="integer", format="int32", minimum=0),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_price_per_liter",
 *   name="price_per_liter",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="float", minimum=0),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_liters_filled",
 *   name="liters_filled",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="float", minimum=0),
 * ),
 */
class AddEditFuelRequest extends FormRequest {
  public function rules() {
    return [
      'date' => ['required', 'string', 'date', 'before_or_equal:today'],
      'from_bars' => ['required', 'integer', 'min:0', 'max:9'],
      'to_bars' => ['required', 'integer', 'min:0', 'max:9'],

      // should not be lower than max value of db column
      'odometer' => ['required', 'integer', 'min:0'],

      'price_per_liter' => ['float', 'min:0'],
      'liters_filled' => ['float', 'min:0'],
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
