<?php

namespace App\Resources\Bucket;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="from", type="string", minLength=1, maxLength=1, example="a"),
 *   @OA\Property(property="to", type="string", minLength=1, maxLength=1, example="d"),
 *   @OA\Property(property="free", type="string", example="1.11 TB"),
 *   @OA\Property(property="freeTB", type="string", example="1.11 TB"),
 *   @OA\Property(property="used", type="string", example="123.12 GB"),
 *   @OA\Property(property="percent", type="integer", format="int32", example=10),
 *   @OA\Property(property="total", type="string", example="1.23 TB"),
 *   @OA\Property(
 *     property="rawTotal",
 *     type="integer",
 *     format="int64",
 *     example=1000169533440,
 *   ),
 *   @OA\Property(property="titles", type="integer", format="int32", example=1),
 * )
 */
class BucketStatsWithEntryResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this['id'],
      'from' => $this['from'],
      'to' => $this['to'],
      'free' => $this['free'],
      'freeTB' => $this['freeTB'],
      'used' => $this['used'],
      'percent' => $this['percent'],
      'total' => $this['total'],
      'rawTotal' => $this['rawTotal'],
      'titles' => $this['titles'],
    ];
  }
}
