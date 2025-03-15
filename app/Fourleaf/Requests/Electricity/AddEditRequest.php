<?php

namespace App\Fourleaf\Requests\Electricity;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_electricity_add_edit_datetime",
 *   name="datetime",
 *   in="query",
 *   required=true,
 *   example="2020-10-20 13:00",
 *   @OA\Schema(type="string"),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_electricity_add_edit_reading",
 *   name="reading",
 *   in="query",
 *   required=true,
 *   @OA\Schema(type="integer", format="int32", minimum=0),
 * ),
 */
class AddEditRequest extends FormRequest {
  public function rules() {
    $today = date("Y-m-d H:i:s", strtotime("+8 hours"));
    $date_validation = 'before_or_equal:' . $today;

    return [
      'datetime' => ['required', 'string', 'date_format:Y-m-d H:i', $date_validation],
      'reading' => ['required', 'integer', 'min:0', 'max:200000'],
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
