<?php

namespace App\Requests\BucketSim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use App\Requests\JsonRule;

class EditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="bucket_sim_edit_description",
   *   name="description",
   *   description="Bucket Sim Description",
   *   in="query",
   *   example="Sample 2 buckets",
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   * @OA\Parameter(
   *   parameter="bucket_sim_edit_buckets",
   *   name="buckets",
   *   description="Bucket JSON String",
   *   in="query",
   *   example="[{""from"":""a"",""to"":""i"",""size"":2000339066880},{""from"":""j"",""to"":""z"",""size"":2000339066880}]",
   *   @OA\Schema(type="string"),
   * ),
   */
  public function rules() {
    return [
      'description' => ['string', 'max:256'],
      'buckets' => ['string', new JsonRule],
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
