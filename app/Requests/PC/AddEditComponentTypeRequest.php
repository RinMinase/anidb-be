<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

use App\Models\PCComponentType;

class AddEditComponentTypeRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_type_type",
   *   name="type",
   *   in="query",
   *   required=true,
   *   example="sample type",
   *   @OA\Schema(type="string", minLength=1, maxLength=32),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_type_name",
   *   name="name",
   *   in="query",
   *   required=true,
   *   example="sample type",
   *   @OA\Schema(type="string", minLength=1, maxLength=32),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_type_is_peripheral",
   *   name="is_peripheral",
   *   in="query",
   *   example=true,
   *   @OA\Schema(type="boolean"),
   * ),
   */
  public function rules() {
    if ($this->route('id')) {
      $id = PCComponentType::where('id', $this->route('id'))
        ->firstOrFail()
        ->id;
    }

    return [
      'type' => ['required', 'string', 'max:32', Rule::unique('pc_component_types')->ignore($id ?? null)],
      'name' => ['required', 'string', 'max:32'],
      'is_peripheral' => ['nullable', 'boolean'],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'is_peripheral' => to_boolean($this->is_peripheral, true),
    ]);
  }

  public function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }

  public function messages() {
    $validation = require config_path('validation.php');

    return array_merge($validation, []);
  }
}
