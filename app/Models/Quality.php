<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "quality": "4K 2160p",
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(
 *     property="quality",
 *     type="string",
 *     enum={"4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"}
 *   ),
 * )
 */
class Quality extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [];

  protected $hidden = [];
}
