<?php

namespace App\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Enum;

use App\Enums\SeasonsEnum;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="catalog_add_edit_season",
   *   name="season",
   *   in="query",
   *   required=true,
   *   example="Winter",
   *   @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   * ),
   * @OA\Parameter(
   *   parameter="catalog_add_edit_year",
   *   name="year",
   *   in="query",
   *   required=true,
   *   example="2020",
   *   @OA\Schema(ref="#/components/schemas/YearSchema"),
   * ),
   */
  public function rules() {
    return [
      'season' => [new Enum(SeasonsEnum::class)],
      'year' => year_validation(true),
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