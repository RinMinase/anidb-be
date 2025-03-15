<?php

namespace App\Fourleaf\Requests\Gas;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

use App\Fourleaf\Enums\GetGasOrderColumnsEnum;

class GetFuelRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="fourleaf_gas_get_fuel_column",
   *   name="column",
   *   description="Order - Column to order",
   *   in="query",
   *   example="odometer",
   *   @OA\Schema(type="string", default="odometer"),
   * ),
   * @OA\Parameter(
   *   parameter="fourleaf_gas_get_fuel_order",
   *   name="order",
   *   description="Order - Direction of order column",
   *   in="query",
   *   @OA\Schema(type="string", default="asc", enum={"asc", "desc"}),
   * ),
   * @OA\Parameter(
   *   parameter="fourleaf_gas_get_fuel_page",
   *   name="page",
   *   description="Pagination - Page to query",
   *   in="query",
   *   example=1,
   *   @OA\Schema(type="integer", format="int32", default=1, minimum=1),
   * ),
   * @OA\Parameter(
   *   parameter="fourleaf_gas_get_fuel_limit",
   *   name="limit",
   *   description="Pagination - Page item limit",
   *   in="query",
   *   example=30,
   *   @OA\Schema(type="integer", format="int32", default=30, minimum=1, maximum=9999),
   * ),
   */
  public function rules() {
    return [
      'column' => [new Enum(GetGasOrderColumnsEnum::class)],
      'order' => ['in:asc,desc,ASC,DESC'],
      'page' => ['integer', 'min:1'],
      'limit' => ['integer', 'min:1', 'max:9999'],
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
