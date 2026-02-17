<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int32", example: 1),
    new OA\Property(property: "key", type: "string", example: "some key"),
    new OA\Property(property: "value", type: "string", example: "some value"),
    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "2020-01-01 00:00:00"),
    new OA\Property(property: "updatedAt", type: "string", format: "date-time", example: "2020-01-01 00:00:00"),
  ]
)]
class AppSetting extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'key',
    'value',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
