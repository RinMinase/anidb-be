<?php

namespace App\Requests\Management;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\YearRule;

class GetByYearRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="management_get_by_year_year",
   *   name="year",
   *   in="query",
   *   example="2020",
   *   @OA\Schema(ref="#/components/schemas/YearSchema"),
   * ),
   */
  public function rules() {
    return [
      'year' => ['nullable', new YearRule],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'show_hidden' => to_boolean($this->show_hidden),
    ]);
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
