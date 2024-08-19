<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "priority": "High",
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(property="priority", type="string", enum={"High", "Normal", "Low"}),
 * )
 */
class Priority extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [];

  protected $hidden = [];

  protected $casts = [];
}
