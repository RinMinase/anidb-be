<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", example: 1),
    new OA\Property(property: "priority", type: "string", example: "High", enum: ["High", "Normal", "Low"]),
  ]
)]
class Priority extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [];

  protected $hidden = [];
}
