<?php

namespace App\Requests\PC;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Rules\JsonRule;

class AddEditInfoRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "pc_add_edit_info_id_owner",
    name: "id_owner",
    in: "query",
    required: true,
    schema: new OA\Schema(type: "string", format: "uuid")
  )]
  #[OA\Parameter(
    parameter: "pc_add_edit_info_label",
    name: "label",
    in: "query",
    required: true,
    schema: new OA\Schema(type: "string", minLength: 1, maxLength: 128)
  )]
  #[OA\Parameter(
    parameter: "pc_add_edit_info_is_active",
    name: "is_active",
    in: "query",
    schema: new OA\Schema(type: "boolean")
  )]
  #[OA\Parameter(
    parameter: "pc_add_edit_info_is_hidden",
    name: "is_hidden",
    in: "query",
    schema: new OA\Schema(type: "boolean")
  )]
  #[OA\Parameter(
    parameter: "pc_add_edit_info_components",
    name: "components",
    description: "PC Setup Components JSON String",
    in: "query",
    required: true,
    example: '[{"id_component":1,"count":1,"is_hidden":false},{"id_component":10,"count":2,"is_hidden":true}]',
    schema: new OA\Schema(type: "string")
  )]
  public function rules() {
    return [
      'id_owner' => ['required', 'string', 'exists:pc_owners,uuid'],
      'label' => ['required', 'string', 'max:128'],
      'is_active' => ['nullable', 'boolean'],
      'is_hidden' => ['nullable', 'boolean'],
      'components' => ['required', 'string', new JsonRule],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'is_active' => to_boolean($this->is_active, true),
      'is_hidden' => to_boolean($this->is_hidden, true),
    ]);
  }
}
