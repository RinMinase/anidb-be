<?php

namespace App\Fourleaf\Requests\Bills;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\SignedSmallIntRule;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_bills_electricity_add_edit_date",
 *   name="date",
 *   in="query",
 *   required=true,
 *   example="2020-10-20",
 *   @OA\Schema(type="string"),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_bills_electricity_add_edit_kwh",
 *   name="kwh",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="integer", format="int16", minimum=0, maximum=32767),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_bills_electricity_add_edit_cost",
 *   name="cost",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="number", minimum=0),
 * ),
 */
class AddEditBillsElectricityRequest extends FormRequest {
  public function rules() {
    $today = date("Y-m-d", strtotime("+8 hours"));
    $date_validation = 'before_or_equal:' . $today;

    return [
      'date' => ['required', 'string', 'date', $date_validation],
      'kwh' => ['required', 'integer', 'min:0', new SignedSmallIntRule],
      'cost' => ['required', 'numeric', 'min:0'],
    ];
  }

  public function failedValidation(Validator $validator) {
    /** @disregard TypeInvalid */
    throw new HttpResponseException(response()->json([
      'status' => 401,
      'data' => $validator->errors(),
    ], 401));
  }
}
