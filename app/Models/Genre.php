<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  example: ["id" => 1, "genre" => "Comedy"],
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int32"),
    new OA\Property(property: "genre", type: "string"),
  ]
)]
class Genre extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [];

  protected $hidden = [];
}
