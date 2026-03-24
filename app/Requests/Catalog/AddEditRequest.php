<?php

namespace App\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes as OA;

use App\Enums\SeasonsEnum;
use App\Rules\YearRule;

class AddEditRequest extends FormRequest {

  #[OA\Parameter(
    parameter: 'catalog_add_edit_season',
    name: 'season',
    in: 'query',
    required: true,
    example: 'Winter',
    schema: new OA\Schema(type: 'string', enum: ['Winter', 'Spring', 'Summer', 'Fall'])
  )]
  #[OA\Parameter(
    parameter: 'catalog_add_edit_year',
    name: 'year',
    in: 'query',
    required: true,
    example: '2020',
    schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
  )]
  public function rules() {
    return [
      'season' => ['required', new Enum(SeasonsEnum::class)],
      'year' => ['required', new YearRule],
    ];
  }
}
