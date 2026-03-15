<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class AddEditApplianceRequest extends FormRequest {

  #[OA\Parameter(
    parameter: 'electricity_add_edit_appliance_date',
    name: 'date',
    in: 'query',
    required: true,
    example: '2020-01-01',
    schema: new OA\Schema(type: 'string', format: 'date'),
  )]
  #[OA\Parameter(
    parameter: 'electricity_add_edit_appliance_name',
    name: 'name',
    in: 'query',
    required: true,
    example: 'Sample Appliance',
    schema: new OA\Schema(type: 'string'),
  )]
  public function rules() {
    return [
      'date' => ['required', 'string', 'date'],
      'name' => ['required', 'string', 'max:256'],
    ];
  }
}
