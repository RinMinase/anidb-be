<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  schema: "FourleafSettings",
  properties: [
    new OA\Property(property: "key", type: "string", example: "some key"),
    new OA\Property(property: "value", type: "string", example: "some value"),
  ]
)]
class Settings extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_settings';
  public $timestamps = null;

  protected $fillable = [
    'key',
    'value',
  ];

  protected $hidden = [];
}
