<?php

namespace App\Requests\BucketSim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="bucket_sim_add_description",
   *   name="description",
   *   description="Bucket Sim Description",
   *   in="query",
   *   required=true,
   *   example="Sample 2 buckets",
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   * @OA\Parameter(
   *   parameter="bucket_sim_add_buckets",
   *   name="buckets",
   *   description="Bucket JSON String",
   *   in="query",
   *   required=true,
   *   example="[{""from"":""a"",""to"":""i"",""size"":2000339066880},{""from"":""j"",""to"":""z"",""size"":2000339066880}]",
   *   @OA\Schema(type="string"),
   * ),
   */
  public function rules() {
    return array_merge_recursive((new EditRequest())->rules(), [
      'description' => ['required'],
      'buckets' => ['required'],
    ]);
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
