<?php

namespace App\Requests\AppSetting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

use App\Models\AppSetting;

class AddEditRequest extends FormRequest {

  #[OA\Parameter(
    parameter: 'app_setting_add_edit_key',
    name: 'key',
    description: 'Application Settings Key',
    in: 'query',
    example: 'unique-key-name',
    required: true,
    schema: new OA\Schema(type: 'string', minLength: 1, maxLength: 256)
  )]
  #[OA\Parameter(
    parameter: 'app_setting_add_edit_value',
    name: 'value',
    description: 'Application Settings Value',
    in: 'query',
    required: true,
    example: 'value',
    schema: new OA\Schema(type: 'string', minLength: 1, maxLength: 256)
  )]
  public function rules() {
    if ($this->route('id')) {
      $id = AppSetting::where('id', $this->route('id'))
        ->firstOrFail()
        ->id;
    }

    return [
      'key' => [
        'sometimes',
        'string',
        'max:256',
        Rule::unique('app_settings', 'key')->ignore($id ?? null)
      ],
      'value' => ['required', 'string', 'max:256'],
    ];
  }
}
