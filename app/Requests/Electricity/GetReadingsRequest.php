<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class GetReadingsRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "electricity_get_readings_order",
    name: "order",
    description: "Order - Direction of order column (date column)",
    in: "query",
    schema: new OA\Schema(type: "string", default: "asc", enum: ["asc", "desc"])
  )]
  #[OA\Parameter(
    parameter: "electricity_get_readings_page",
    name: "page",
    description: "Pagination - Page to query",
    in: "query",
    example: 1,
    schema: new OA\Schema(type: "integer", format: "int32", default: 1, minimum: 1)
  )]
  #[OA\Parameter(
    parameter: "electricity_get_readings_limit",
    name: "limit",
    description: "Pagination - Page item limit",
    in: "query",
    example: 100,
    schema: new OA\Schema(type: "integer", format: "int32", default: 100, minimum: 1, maximum: 9999)
  )]
  public function rules() {
    return [
      'order' => ['in:asc,desc,ASC,DESC'],
      'page' => ['integer', 'min:1'],
      'limit' => ['integer', 'min:1', 'max:9999'],
    ];
  }
}
