<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "id": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "title": "Sample Title",
 *     "priority": "High",
 *   },
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="priority", type="string", enum={"High", "Normal", "Low"}),
 * )
 */
class Partial extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'title',
    'id_catalogs',
    'id_priority',
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
    'deleted_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];

  public function priority() {
    return $this->belongsTo(Priority::class, 'id_priority');
  }
}
