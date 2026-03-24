<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "uuid", type: "string", format: "uuid", example: "9ef81943-78f0-4d1c-a831-a59fb5af339c"),
    new OA\Property(property: "tableChanged", type: "string", example: "marathon"),
    new OA\Property(property: "idChanged", type: "string", example: 1),
    new OA\Property(property: "description", type: "string", example: "title changed from 'old' to 'new'"),
    new OA\Property(property: "action", type: "string", example: "add"),
    new OA\Property(property: "metadata", type: "object", example: ['additonal_data' => 'sample value']),
    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "2020-01-01 00:00:00"),
  ]
)]
class Log extends Model {

  use RefreshableAutoIncrements;

  // disables updated_at timestamp
  public $timestamps = ["created_at"];
  const UPDATED_AT = null;

  protected $fillable = [
    'uuid',
    'table_changed',
    'id_changed',
    'description',
    'action',
    'metadata',
    'created_at',
  ];

  protected $hidden = [
    'id',
  ];

  protected function casts(): array {
    return [
      'metadata' => 'array',
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
