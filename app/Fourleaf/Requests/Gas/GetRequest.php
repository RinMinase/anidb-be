<?php

namespace App\Fourleaf\Requests\Gas;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

use App\Fourleaf\Enums\AvgEfficiencyTypeEnum;
use App\Fourleaf\Enums\EfficiencyGraphTypeEnum;

/**
 * @OA\Parameter(
 *   parameter="fourleaf_gas_get_gas_avg_efficiency_type",
 *   name="avg_efficiency_type",
 *   in="query",
 *   @OA\Schema(type="string", default="all", enum={"all","last5", "last10"}),
 * ),
 * @OA\Parameter(
 *   parameter="fourleaf_gas_get_gas_efficiency_graph_type",
 *   name="efficiency_graph_type",
 *   in="query",
 *   @OA\Schema(type="string", default="last20data", enum={"last20data","last12mos"}),
 * ),
 */
class GetRequest extends FormRequest {
  public function rules() {
    return [
      'avg_efficiency_type' => [new Enum(AvgEfficiencyTypeEnum::class)],
      'efficiency_graph_type' => [new Enum(EfficiencyGraphTypeEnum::class)],
    ];
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
