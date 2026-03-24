<?php

namespace App\Requests\PC;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class GetOwnersRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "pc_get_owners_show_hidden",
    name: "show_hidden",
    in: "query",
    example: true,
    schema: new OA\Schema(type: "boolean")
  )]
  public function rules() {
    return [
      'show_hidden' => ['nullable', 'boolean'],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'show_hidden' => to_boolean($this->show_hidden),
    ]);
  }
}
