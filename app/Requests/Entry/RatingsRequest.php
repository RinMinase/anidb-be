<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RatingsRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_ratings_audio",
   *   name="audio",
   *   in="query",
   *   example=10,
   *   @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   * ),
   * @OA\Parameter(
   *   parameter="entry_ratings_enjoyment",
   *   name="enjoyment",
   *   in="query",
   *   example=10,
   *   @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   * ),
   * @OA\Parameter(
   *   parameter="entry_ratings_graphics",
   *   name="graphics",
   *   in="query",
   *   example=10,
   *   @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   * ),
   * @OA\Parameter(
   *   parameter="entry_ratings_plot",
   *   name="plot",
   *   in="query",
   *   example=10,
   *   @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   * ),
   */
  public function rules() {
    return [
      'audio' => ['integer', 'min:0', 'max:10'],
      'enjoyment' => ['integer', 'min:0', 'max:10'],
      'graphics' => ['integer', 'min:0', 'max:10'],
      'plot' => ['integer', 'min:0', 'max:10'],
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
