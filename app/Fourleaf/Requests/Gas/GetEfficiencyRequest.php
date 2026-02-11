<?php

namespace App\Fourleaf\Requests\Gas;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes as OA;

use App\Fourleaf\Enums\EfficiencyGraphTypeEnum;

#[OA\Parameter(
  parameter: "fourleaf_gas_get_gas_efficiency_type",
  name: "type",
  in: "query",
  schema: new OA\Schema(type: "string", default: "last20data", enum: ["last20data", "last12mos"])
)]
class GetEfficiencyRequest extends FormRequest {
  public function rules() {
    return [
      'type' => [new Enum(EfficiencyGraphTypeEnum::class)],
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
