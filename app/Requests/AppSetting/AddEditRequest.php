<?php

namespace App\Requests\AppSetting;

use App\Models\AppSetting;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

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
