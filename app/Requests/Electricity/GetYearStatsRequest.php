<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Rules\YearRule;

class GetYearStatsRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "electricity_get_year_stats_year",
    name: "year",
    in: "query",
    example: '2020',
    schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
  )]
  public function rules() {
    return [
      'year' => ['nullable', new YearRule],
    ];
  }
}
