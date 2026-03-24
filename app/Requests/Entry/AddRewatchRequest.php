<?php

namespace App\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

use App\Models\Entry;

class AddRewatchRequest extends FormRequest {

  #[OA\Parameter(
    parameter: 'entry_add_rewatch_date_rewatched',
    name: 'date_rewatched',
    in: 'query',
    required: true,
    example: '2022-01-23',
    schema: new OA\Schema(type: 'string', format: 'date')
  )]
  public function rules() {
    if ($this->route('uuid')) {
      Entry::where('uuid', $this->route('uuid'))->firstOrFail();
    }

    $today = date("Y-m-d H:i:s", strtotime("+8 hours"));
    $date_validation = 'before_or_equal:' . $today;

    return [
      'date_rewatched' => ['required', 'date', $date_validation],
    ];
  }
}
