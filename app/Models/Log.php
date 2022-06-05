<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'table_changed',
    'id_changed',
    'description',
    'action',
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