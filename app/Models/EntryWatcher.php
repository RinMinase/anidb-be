<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int32", example: 1),
    new OA\Property(property: "label", type: "string", example: 'label'),
    new OA\Property(property: "color", type: "string", description: "Color hex code", example: '#ffffff'),
  ]
)]
class EntryWatcher extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'entries_watchers';
  public $timestamps = null;

  protected $fillable = [
    'id',
    'label',
    'color',
  ];

  protected $hidden = [];
}
