<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as BaseAuthModel;
use Laravel\Sanctum\HasApiTokens;

use App\Traits\RefreshableAutoIncrements;

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

  use HasApiTokens, RefreshableAutoIncrements;

  protected $fillable = [
    'email',
    'password',
  ];

  protected $hidden = [
    'password',
    'created_at',
    'updated_at',
  ];

  protected $casts = [];

  public function searches() {
    return $this->hasOne(Search::class, 'id_user');
  }
}
