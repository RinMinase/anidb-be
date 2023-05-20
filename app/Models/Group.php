<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "uuid": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "name": "Sample Group Name",
 *   },
 *   @OA\Property(property="uuid", type="string", format="uuid"),
 *   @OA\Property(property="name", type="string"),
 * )
 */
class Group extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'name',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id',
    'created_at',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];
}
