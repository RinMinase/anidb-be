<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "genre": "Comedy",
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(property="genre", type="string"),
 * )
 */
class Genre extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [];

  protected $hidden = [];

  protected $casts = [];
}
