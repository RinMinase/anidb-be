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
    'uuid',
    'id_quality',
    'title',
    'date_finished',
    'duration',
    'filesize',
    'episodes',
    'ovas',
    'specials',
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
    'id',
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

  public function season_first_title() {
    return $this->belongsTo(self::class, 'season_first_title_id');
  }

  public function prequel() {
    return $this->belongsTo(self::class, 'prequel_id');
  }

  public function sequel() {
    return $this->belongsTo(self::class, 'sequel_id');
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
