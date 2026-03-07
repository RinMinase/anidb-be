<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

use App\Models\Helpers\ArrayCast;
use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", example: 1),
    new OA\Property(property: "title", type: "string", example: 'Sample Title'),
    new OA\Property(property: "description", type: "string", example: 'Sample Description'),
    new OA\Property(
      property: "ingredients",
      type: "array",
      items: new OA\Items(type: 'string', example: 'Ingredient 1')
    ),
    new OA\Property(property: "instructions", type: "string", example: '## Sample Instructions Markdown'),
    new OA\Property(property: "imageUrl", type: "string", example: 'http://example.com/image-url'),
    new OA\Property(property: "imageUrlLg", type: "string", example: 'http://example.com/image-url-lg'),
  ]
)]
class Recipe extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $fillable = [
    'title',
    'description',
    'ingredients',
    'instructions',
    'image_id'
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'ingredients' => ArrayCast::class,
      'updated_at' => 'datetime:Y-m-d H:i:s',
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
