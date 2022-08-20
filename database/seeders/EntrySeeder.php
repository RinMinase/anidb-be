<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 1,
        'title' => "title 1",
        'date_finished' => Carbon::parse('2001-01-01')->format('Y-m-d'),
        'filesize' => 21_331_439_861,
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title_id' => 1,
        'prequel_id' => null,
        'sequel_id' => 2,
        'release_year' => 2000,
        'release_season' => "Winter",
        'codec_hdr' => 1,
        'id_codec_video' => 1,
        'id_codec_audio' => 4,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 2,
        'title' => "title 2",
        'date_finished' => Carbon::parse('2011-01-01')->format('Y-m-d'),
        'filesize' => 16_884_590_183,
        'duration' => 1_000_000,
        'season_number' => 2,
        'season_first_title' => 1,
        'prequel_id' => 1,
        'sequel_id' => null,
        'release_year' => 2010,
        'release_season' => "Summer",
        'codec_hdr' => 1,
        'id_codec_video' => 4,
        'id_codec_audio' => 10,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 3,
        'title' => "title 1 offquel",
        'date_finished' => Carbon::parse('2011-01-01')->format('Y-m-d'),
        'filesize' => 65_493_448_000,
        'duration' => 10_000,
        'season_number' => 0,
        'season_first_title_id' => 1,
        'prequel_id' => 1,
        'sequel_id' => null,
        'release_year' => 2010,
        'release_season' => "Spring",
        'codec_hdr' => 0,
        'id_codec_video' => 2,
        'id_codec_audio' => 9,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 2,
        'title' => "another title 3",
        'date_finished' => Carbon::parse('2015-01-01')->format('Y-m-d'),
        'filesize' => 57_989_090_000,
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title_id' => 3,
        'prequel_id' => null,
        'sequel_id' => null,
        'release_year' => 2014,
        'release_season' => "Spring",
        'codec_hdr' => 0,
        'id_codec_video' => 3,
        'id_codec_audio' => 6,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 2,
        'title' => "another title 4",
        'date_finished' => Carbon::parse('2014-05-01')->format('Y-m-d'),
        'filesize' => 74_748_957_000,
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title_id' => 4,
        'prequel_id' => null,
        'sequel_id' => null,
        'release_year' => 2014,
        'release_season' => "Winter",
        'codec_hdr' => 0,
        'id_codec_video' => 4,
        'id_codec_audio' => 12,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 2,
        'title' => "Another title 5",
        'date_finished' => Carbon::parse('2015-04-01')->format('Y-m-d'),
        'filesize' => 75_773_386_000,
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title_id' => 5,
        'prequel_id' => null,
        'sequel_id' => null,
        'release_year' => 2014,
        'release_season' => "Spring",
        'codec_hdr' => 1,
        'id_codec_video' => 4,
        'id_codec_audio' => 7,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => 2,
        'title' => "2title 6",
        'date_finished' => Carbon::parse('2015-04-01')->format('Y-m-d'),
        'filesize' => 26_648_304_000,
        'duration' => 10_000,
        'season_number' => 1,
        'season_first_title_id' => 6,
        'prequel_id' => null,
        'sequel_id' => null,
        'release_year' => 2014,
        'release_season' => "Spring",
        'codec_hdr' => 1,
        'id_codec_video' => 4,
        'id_codec_audio' => 11,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    $testDataOffquel = [
      [
        'id_entries' => 1,          // parent entry
        'id_entries_offquel' => 3,  // offquel entry
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
        'date_rewatched' => Carbon::parse('02-01-2013')->format('Y-m-d'),
      ],
    ];

    DB::table('entries')->insert($testData);
    DB::table('entries_offquel')->insert($testDataOffquel);
    DB::table('entries_rating')->insert($testDataRating);
    DB::table('entries_rewatch')->insert($testDataRewatch);
  }
}
