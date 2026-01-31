<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int32", example: 1),
    new OA\Property(property: "from", type: "string", minLength: 1, maxLength: 1, example: "a"),
    new OA\Property(property: "to", type: "string", minLength: 1, maxLength: 1, example: "d"),
    new OA\Property(property: "size", type: "integer", format: "int64", example: 2000339066880),
    new OA\Property(property: "purchaseDate", type: "string", format: "date", example: "2020-01-20"),
  ]
)]
class Bucket extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'from',
    'to',
    'size',
    'purchase_date',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
