<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "date", type: "string", example: "2020-10-20 13:00"),
    new OA\Property(property: "reading", type: "integer", format: "int32", minimum: 0, example: 1234),
  ]
)]
class Electricity extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_electricity';
  public $timestamps = null;

  protected $fillable = [
    'datetime',
    'reading',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'datetime' => 'datetime:Y-m-d H:i'
    ];
  }
}
