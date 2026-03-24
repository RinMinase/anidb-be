<?php

namespace App\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Models\Entry;

class OffquelsRequest extends FormRequest {

  // Possible modification / improvement using deepObject
  #[OA\Parameter(
    parameter: 'entry_offquels_data_deepobject',
    name: 'data',
    in: 'query',
    required: true,
    style: 'deepObject',
    explode: true,
    description: 'Array of offquel UUIDs',
    schema: new OA\Schema(
      type: 'array',
      items: new OA\Items(type: 'string', format: 'uuid'),
      example: ["e9597119-8452-4f2b-96d8-f2b1b1d2f158", "786f90ce-87a6-4096-833b-a4a1db740f3d"]
    )
  )]

  // Original parameter
  #[OA\Parameter(
    parameter: 'entry_offquels_data',
    name: 'data',
    in: 'query',
    required: true,
    example: 'data[0]=e9597119-8452-4f2b-96d8-f2b1b1d2f158&data[1]=786f90ce-87a6-4096-833b-a4a1db740f3d',
    schema: new OA\Schema(type: 'string')
  )]

  public function rules() {
    vdd($this->route('uuid'));

    if ($this->route('uuid')) {
      Entry::where('uuid', $this->route('uuid'))->firstOrFail();
    }

    return [
      'offquel_uuid' => ['required', 'string', 'uuid'],
    ];
  }
}
