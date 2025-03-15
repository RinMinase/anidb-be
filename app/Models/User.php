<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as BaseAuthModel;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   schema="UserToken",
 *   @OA\Property(property="token", type="string", example="alphanumeric token"),
 * ),
 * @OA\Schema(
 *   schema="UserDetails",
 *   @OA\Property(property="uuid", type="string", format="uuid", example="e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
 *   @OA\Property(property="username", type="string"),
 *   @OA\Property(property="createdAt", type="string"),
 *   @OA\Property(property="updatedAt", type="string"),
 * ),
 */
class User extends BaseAuthModel {

  use HasApiTokens, RefreshableAutoIncrements;

  protected $fillable = [
    'uuid',
    'username',
    'password',
    'is_admin',
  ];

  protected $hidden = [
    'id',
    'password',
  ];

  public function searches() {
    return $this->hasOne(Search::class, 'id_user');
  }
}
