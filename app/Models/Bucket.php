<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "from": "a",
 *     "to": "d",
 *     "size": 2000339066880,
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(property="from", type="string", minLength=1, maxLength=1),
 *   @OA\Property(property="to", type="string", minLength=1, maxLength=1),
 *   @OA\Property(property="size", type="integer", format="int64"),
 * )
 */
class Bucket extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'from',
    'to',
    'size',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];
}

/**
 * @OA\Schema(
 *   example={{
 *     "id": 1,
 *     "from": "a",
 *     "to": "d",
 *     "free": "1.11 TB",
 *     "freeTB": "1.11 TB",
 *     "used": "123.12 GB",
 *     "percent": 10,
 *     "total": "1.23 TB",
 *     "rawTotal": 1000169533440,
 *     "titles": 1,
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="id", type="integer", format="int32"),
 *     @OA\Property(property="from", type="string", minLength=1, maxLength=1),
 *     @OA\Property(property="to", type="string", minLength=1, maxLength=1),
 *     @OA\Property(property="free", type="string"),
 *     @OA\Property(property="freeTB", type="string"),
 *     @OA\Property(property="used", type="string"),
 *     @OA\Property(property="percent", type="integer", format="int32"),
 *     @OA\Property(property="total", type="string"),
 *     @OA\Property(property="rawTotal", type="integer", format="int64"),
 *     @OA\Property(property="titles", type="integer", format="int32"),
 *   ),
 * )
 */
class BucketStatsWithEntry {
}
