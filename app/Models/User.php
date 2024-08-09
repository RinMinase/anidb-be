<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *   schema="UserToken",
 *   @OA\Property(property="token", type="string", example="alphanumeric token"),
 * ),
 * @OA\Schema(
 *   schema="UserDetails",
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(
 *     property="email",
 *     type="string",
 *     format="email",
 *     example="test@mail.com",
 *   ),
 * ),
 */
class User extends BaseAuthModel {
  use HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'created_at',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];

  public function searches() {
    return $this->hasOne(Search::class, 'id_user');
  }
}
