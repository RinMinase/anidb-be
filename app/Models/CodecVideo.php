<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", example: 1),
    new OA\Property(property: "codec", type: "string", example: "x264 8bit"),
    new OA\Property(property: "order", type: "integer", nullable: true, example: null),
  ]
)]
class CodecVideo extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'codec',
    'order',
  ];

  protected $hidden = [
    'created_at',
    'updated_at',
  ];
}
