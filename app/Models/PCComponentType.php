<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int64", example=1),
 *   @OA\Property(property="type", type="string", example="cpu"),
 *   @OA\Property(property="name", type="string", example="CPU"),
 * )
 */
class PCComponentType extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'pc_component_types';
  public $timestamps = null;

  protected $fillable = [
    'id',
    'type',
    'name',
  ];

  protected $hidden = [];

  protected $casts = [];
}
