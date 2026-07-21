<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Rules\YearRule;

class GetMonthStatsRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "electricity_get_month_stats_year",
    name: "year",
    in: "query",
    example: '2020',
    schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
  )]
  #[OA\Parameter(
    parameter: "electricity_get_month_stats_month",
    name: "month",
    in: "query",
    required: true,
    example: 1,
    schema: new OA\Schema(type: "integer", format: "int8", minimum: 1, maximum: 12)
  )]
  public function rules() {
    return [
      'year' => ['nullable', new YearRule],
      'month' => ['required', 'integer', 'min:1', 'max:12'],
    ];
  }
}
