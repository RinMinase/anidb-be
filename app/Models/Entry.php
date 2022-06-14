<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model {

  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id_quality',
    'title',
    'date_finished',
    'duration',
    'filesize',
    'episodes',
    'ovas',
    'specials',
    'season_number',
    'season_first_title',
    'prequel',
    'sequel',
    'encoder_video',
    'encoder_audio',
    'encoder_subs',
    'release_year',
    'release_season',
    'variants',
    'remarks',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id_quality',
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

  public function quality() {
    return $this->belongsTo(Quality::class, 'id_quality');
  }

  public function rating() {
    return $this->hasOne(EntryRating::class, 'id_entries');
  }

  public function offquels() {
    return $this->hasMany(EntryOffquel::class, 'id_entries');
  }

  public function rewatches() {
    return $this->hasMany(EntryRewatch::class, 'id_entries');
  }
}
