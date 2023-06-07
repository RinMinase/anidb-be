<?php

namespace App\Requests\Partial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Enum;

use App\Enums\SeasonsEnum;

class AddEditMultipleRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="partial_add_edit_multiple_data",
   *   name="data",
   *   in="query",
   *   required=true,
   *   example="low[0]=Title Low 1&normal[0]=Title Normal 1&normal[1]=Title Normal 2&high[0]=Title High 1",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="partial_add_edit_multiple_season",
   *   name="season",
   *   in="query",
   *   required=true,
   *   example="Winter",
   *   @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   * ),
   * @OA\Parameter(
   *   parameter="partial_add_edit_multiple_year",
   *   name="year",
   *   in="query",
   *   required=true,
   *   example=2021,
   *   @OA\Schema(ref="#/components/schemas/YearSchema"),
   * ),
   */
  public function rules() {
    return [
      'data' => 'required|string',
      'season' => ['required', new Enum(SeasonsEnum::class)],
      'year' => ['required', new YearRule],
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
