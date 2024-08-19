<?php

namespace App\Models;

class Log extends BaseModel {

  // disables updated_at timestamp
  public $timestamps = ["created_at"];
  const UPDATED_AT = null;

  protected $fillable = [
    'uuid',
    'table_changed',
    'id_changed',
    'description',
    'action',
    'created_at',
  ];

  protected $hidden = [
    'id',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];
}
