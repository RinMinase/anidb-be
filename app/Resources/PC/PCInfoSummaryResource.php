<?php

namespace App\Resources\PC;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Resources\Genre\GenreResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(
 *     property="quality",
 *     type="string",
 *     enum={"4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"},
 *     example="4K 2160p",
 *   ),
 *   @OA\Property(property="title", type="string", example="Sample Title"),
 *   @OA\Property(property="dateFinished", type="string", example="Mar 01, 2011"),
 *   @OA\Property(property="filesize", type="string", example="10.25 GB"),
 *   @OA\Property(property="rewatched", type="boolean", example=false),
 *   @OA\Property(property="rewatchCount", type="integer", format="int32", example=1),
 *   @OA\Property(property="episodes", type="integer", format="int32", example=25),
 *   @OA\Property(property="ovas", type="integer", format="int32", example=1),
 *   @OA\Property(property="specials", type="integer", format="int32", example=1),
 *   @OA\Property(property="encoder", type="string", example="encoder—encoder2"),
 *   @OA\Property(property="release", type="string", example="Spring 2017"),
 *   @OA\Property(property="remarks", type="string", example="Some remarks"),
 *   @OA\Property(property="rating", type="number", example=7.5),
 *   @OA\Property(property="ratingOver5", type="number", example=3.75),
 *   @OA\Property(
 *     property="genres",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/GenreResource")
 *   ),
 * ),
 */
class PCInfoSummaryResource extends JsonResource {

  public function toArray($request) {
    return [
      'owner' => $this->owner,
      'label' => $this->label,

      'isActive' => $this->is_active,
      'isHidden' => $this->is_hidden,

      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
      'deletedAt' => $this->deleted_at,
    ];
  }
}
