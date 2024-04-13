<?php

namespace App\Fourleaf\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

use App\Fourleaf\Enums\AvgEfficiencyTypeEnum;
use App\Fourleaf\Enums\EfficiencyGraphTypeEnum;

class GetGasRequest extends FormRequest {
  public function rules() {
    return [
      'avgEfficiencyType' => [new Enum(AvgEfficiencyTypeEnum::class)],
      'efficiencyGraphType' => [new Enum(EfficiencyGraphTypeEnum::class)],
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
