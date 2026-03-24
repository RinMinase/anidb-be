<?php

namespace App\Requests\Codec;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Rules\PositiveSignedTinyIntRule;

class AddEditRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "codec_add_edit_codec",
    name: "codec",
    in: "query",
    required: true,
    example: "Sample Codec",
    schema: new OA\Schema(type: "string", minLength: 1, maxLength: 16)
  )]
  #[OA\Parameter(
    parameter: "codec_add_edit_order",
    name: "order",
    in: "query",
    example: "1",
    schema: new OA\Schema(type: "integer", format: "int8", minimum: 1, maximum: 127)
  )]
  public function rules() {
    return [
      'codec' => ['required', 'string', 'max:16'],
      'order' => ['integer', 'min:1', new PositiveSignedTinyIntRule],
    ];
  }
}
