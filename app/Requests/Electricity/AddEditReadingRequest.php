<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Rules\PositiveSignedIntRule;

class AddEditReadingRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "electricity_add_edit_reading_date",
    name: "date",
    in: "query",
    required: true,
    example: "2001-01-01T00:00:00.000000Z",
    schema: new OA\Schema(type: "string", format: 'date-time')
  )]
  #[OA\Parameter(
    parameter: "electricity_add_edit_reading_reading",
    name: "reading",
    in: "query",
    example: 1,
    schema: new OA\Schema(type: "integer", format: "int8", minimum: 1, maximum: 2147483647)
  )]
  public function rules() {
    return [
      'date' => ['required', 'date', 'before_or_equal:today'],
      'reading' => ['required', 'integer', 'min:0', new PositiveSignedIntRule],
    ];
  }
}
