<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as BaseAuthModel;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

use App\Middleware\CustomResetPassword;
use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  schema: "UserToken",
  properties: [
    new OA\Property(property: "token", type: "string", example: "alphanumeric token"),
  ]
)]

#[OA\Schema(
  schema: "UserDetails",
  properties: [
    new OA\Property(property: "uuid", type: "string", format: "uuid", example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
    new OA\Property(property: "username", type: "string"),
    new OA\Property(property: "email", type: "string"),
    new OA\Property(property: "createdAt", type: "string"),
    new OA\Property(property: "updatedAt", type: "string"),
  ]
)]
class User extends BaseAuthModel {

  use HasApiTokens, Notifiable, RefreshableAutoIncrements;

  protected $fillable = [
    'uuid',
    'username',
    'email',
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

  public function sendPasswordResetNotification($token): void {
    $this->notify(new CustomResetPassword($token));
  }
}
