<?php

namespace App\Requests\Car;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes as OA;

use App\Enums\CarGasOrderColumnsEnum;

class GetFuelRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "car_get_fuel_column",
    name: "column",
    description: "Order - Column to order",
    in: "query",
    example: "odometer",
    schema: new OA\Schema(type: "string", default: "odometer")
  )]
  #[OA\Parameter(
    parameter: "car_get_fuel_order",
    name: "order",
    description: "Order - Direction of order column",
    in: "query",
    schema: new OA\Schema(type: "string", default: "asc", enum: ["asc", "desc"])
  )]
  #[OA\Parameter(
    parameter: "car_get_fuel_page",
    name: "page",
    description: "Pagination - Page to query",
    in: "query",
    example: 1,
    schema: new OA\Schema(type: "integer", format: "int32", default: 1, minimum: 1)
  )]
  #[OA\Parameter(
    parameter: "car_get_fuel_limit",
    name: "limit",
    description: "Pagination - Page item limit",
    in: "query",
    example: 30,
    schema: new OA\Schema(type: "integer", format: "int32", default: 30, minimum: 1, maximum: 9999)
  )]
  public function rules() {
    return [
      'column' => [new Enum(CarGasOrderColumnsEnum::class)],
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
