<?php

namespace App\Fourleaf\Requests\Gas;

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
 *   @OA\Schema(type="integer", format="int32", minimum=0, maximum=100000),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_price_per_liter",
 *   name="price_per_liter",
 *   in="query",
 *   @OA\Schema(type="number", minimum=0, maximum=150),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_add_edit_fuel_liters_filled",
 *   name="liters_filled",
 *   in="query",
 *   @OA\Schema(type="number", minimum=0, maximum=40),
 * ),
 */
class AddEditFuelRequest extends FormRequest {
  public function rules() {
    $today = date("Y-m-d", strtotime("+8 hours"));
    $date_validation = 'before_or_equal:' . $today;

    return [
      'date' => ['required', 'string', 'date', $date_validation],
      'from_bars' => ['required', 'integer', 'min:0', 'max:9'],
      'to_bars' => ['required', 'integer', 'min:0', 'max:9', 'gte:from_bars'],
      'odometer' => ['required', 'integer', 'min:0', 'max:100000'],

      'price_per_liter' => ['numeric', 'min:0', 'max:150'],
      'liters_filled' => ['numeric', 'min:0', 'max:40'],
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
