<?php

namespace App\Requests\BucketSim;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Rules\JsonRule;

class AddEditRequest extends FormRequest {

  #[OA\Parameter(
    parameter: 'bucket_sim_add_edit_description',
    name: 'description',
    description: 'Bucket Sim Description',
    in: 'query',
    example: 'Sample 2 buckets',
    required: true,
    schema: new OA\Schema(type: 'string', minLength: 1, maxLength: 256)
  )]
  #[OA\Parameter(
    parameter: 'bucket_sim_add_edit_buckets',
    name: 'buckets',
    description: 'Bucket JSON String',
    in: 'query',
    required: true,
    example: '[{"from":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":2000339066880}]',
    schema: new OA\Schema(type: 'string')
  )]
  public function rules() {
    return [
      'description' => ['required', 'string', 'max:256'],
      'buckets' => ['required', 'string', new JsonRule],
    ];
  }
}
