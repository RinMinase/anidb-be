<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EntrySeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'id_quality' => 1,
        'title' => "title 1",
        'date_finished' => Carbon::parse('01-01-2001')->format('Y-m-d'),
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title' => 1,
        'prequel' => null,
        'sequel' => 2,
        'release_year' => 2000,
        'release_season' => "Winter",
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'id_quality' => 2,
        'title' => "title 2",
        'date_finished' => Carbon::parse('01-01-2011')->format('Y-m-d'),
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title' => 1,
        'prequel' => 1,
        'sequel' => null,
        'release_year' => 2010,
        'release_season' => "Summer",
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'id_quality' => 3,
        'title' => "title 1 offquel",
        'date_finished' => Carbon::parse('01-01-2011')->format('Y-m-d'),
        'duration' => 10_000,
        'season_number' => 0,
        'season_first_title' => 1,
        'prequel' => 1,
        'sequel' => null,
        'release_year' => 2010,
        'release_season' => "Spring",
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    $testDataOffquel = [
      [
        'id_entries' => 1,
        'id_entries_offquel' => 3,
      ],
    ];

    $testDataRating = [
      [
        'id_entries' => 1,
        'audio' => 6,
        'enjoyment' => 5,
        'graphics' => 4,
        'plot' => 3,
      ], [
        'id_entries' => 2,
        'audio' => 7,
        'enjoyment' => 8,
        'graphics' => 9,
        'plot' => 10,
      ],
    ];

    $testDataRewatch = [
      [
        'id_entries' => 1,
        'date_rewatched' => Carbon::parse('01-02-2011')->format('Y-m-d'),
      ], [
        'id_entries' => 1,
        'date_rewatched' => Carbon::parse('01-03-2011')->format('Y-m-d'),
      ], [
        'id_entries' => 2,
        'date_rewatched' => Carbon::parse('02-01-2011')->format('Y-m-d'),
      ],
    ];

    DB::table('entries')->insert($testData);
    DB::table('entries_offquel')->insert($testDataOffquel);
    DB::table('entries_rating')->insert($testDataRating);
    DB::table('entries_rewatch')->insert($testDataRewatch);
  }
}