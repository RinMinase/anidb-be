<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class AddEditElectricityRequest extends FormRequest {

  #[OA\Parameter(
    parameter: 'electricity_add_edit_datetime',
    name: 'datetime',
    in: 'query',
    required: true,
    example: '2020-01-01 00:00:00',
    schema: new OA\Schema(type: 'string', format: 'date-time'),
  )]
  #[OA\Parameter(
    parameter: 'electricity_add_edit_reading',
    name: 'reading',
    in: 'query',
    required: true,
    example: 100,
    schema: new OA\Schema(type: 'integer'),
  )]
  public function rules() {
    return [
      'datetime' => ['required', 'string', 'datetime'],
      'reading' => ['required', 'integer', 'min:0'],
    ];
  }
}
