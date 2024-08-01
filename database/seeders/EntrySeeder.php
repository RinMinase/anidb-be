<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\Quality;

class EntrySeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $uuid_1 = Str::uuid()->toString();
    $uuid_2 = Str::uuid()->toString();
    $uuid_3 = Str::uuid()->toString();
    $uuid_4 = Str::uuid()->toString();
    $uuid_5 = Str::uuid()->toString();
    $uuid_6 = Str::uuid()->toString();
    $uuid_7 = Str::uuid()->toString();

    $id_quality_2160 = Quality::where('quality', '4K 2160p')->first()->id;
    $id_quality_1080 = Quality::where('quality', 'FHD 1080p')->first()->id;
    $id_quality_720 = Quality::where('quality', 'HD 720p')->first()->id;

    $id_codec_audio_4 = CodecAudio::where('codec', 'FLAC 7.1')->first()->id;
    $id_codec_audio_6 = CodecAudio::where('codec', 'DTS-HD MA 5.1')->first()->id;
    $id_codec_audio_7 = CodecAudio::where('codec', 'DTS-HD MA 7.1')->first()->id;
    $id_codec_audio_9 = CodecAudio::where('codec', 'TrueHD 5.1')->first()->id;
    $id_codec_audio_10 = CodecAudio::where('codec', 'TrueHD 7.1')->first()->id;
    $id_codec_audio_11 = CodecAudio::where('codec', 'TrueHD 5.1 Atmos')->first()->id;
    $id_codec_audio_12 = CodecAudio::where('codec', 'TrueHD 7.1 Atmos')->first()->id;

    $id_codec_video_1 = CodecVideo::where('codec', 'x264 8bit')->first()->id;
    $id_codec_video_2 = CodecVideo::where('codec', 'x264 10bit')->first()->id;
    $id_codec_video_3 = CodecVideo::where('codec', 'x265 8bit')->first()->id;
    $id_codec_video_4 = CodecVideo::where('codec', 'x265 10bit')->first()->id;

    $testData = [
      [
        'uuid' => $uuid_1,
        'id_quality' => $id_quality_2160,
        'title' => "title 1",
        'date_finished' => Carbon::parse('2001-01-01')->format('Y-m-d'),
        'filesize' => 21_331_439_861,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2000,
        'release_season' => "Winter",
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_1,
        'id_codec_audio' => $id_codec_audio_4,
      ], [
        'uuid' => $uuid_2,
        'id_quality' => $id_quality_1080,
        'title' => "title 2",
        'date_finished' => Carbon::parse('2011-01-01')->format('Y-m-d'),
        'filesize' => 16_884_590_183,
        'duration' => 1_000_000,
        'season_number' => 2,
        'release_year' => 2010,
        'release_season' => "Summer",
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_10,
      ], [
        'uuid' => $uuid_3,
        'id_quality' => $id_quality_720,
        'title' => "title 1 offquel",
        'date_finished' => Carbon::parse('2011-01-01')->format('Y-m-d'),
        'filesize' => 65_493_448_000,
        'duration' => 10_000,
        'season_number' => 0,
        'release_year' => 2010,
        'release_season' => "Spring",
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_2,
        'id_codec_audio' => $id_codec_audio_9,
      ], [
        'uuid' => $uuid_4,
        'id_quality' => $id_quality_1080,
        'title' => "another title 3",
        'date_finished' => Carbon::parse('2015-01-01')->format('Y-m-d'),
        'filesize' => 57_989_090_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => "Spring",
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_3,
        'id_codec_audio' => $id_codec_audio_6,
      ], [
        'uuid' => $uuid_5,
        'id_quality' => $id_quality_1080,
        'title' => "another title 4",
        'date_finished' => Carbon::parse('2014-05-01')->format('Y-m-d'),
        'filesize' => 74_748_957_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => "Winter",
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_12,
      ], [
        'uuid' => $uuid_6,
        'id_quality' => $id_quality_1080,
        'title' => "Another title 5",
        'date_finished' => Carbon::parse('2015-04-01')->format('Y-m-d'),
        'filesize' => 75_773_386_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => "Spring",
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_7,
      ], [
        'uuid' => $uuid_7,
        'id_quality' => $id_quality_1080,
        'title' => "2title 6",
        'date_finished' => Carbon::parse('2015-04-01')->format('Y-m-d'),
        'filesize' => 26_648_304_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => "Spring",
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_11,
      ],
    ];

    $testData = collect($testData)
      ->map(function (array $item) {
        return array_merge($item, [
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      })->toArray();

    DB::table('entries')->insert($testData);

    $id_entries_1 = Entry::where('uuid', $uuid_1)->first()->id;
    $id_entries_2 = Entry::where('uuid', $uuid_2)->first()->id;
    $id_entries_3 = Entry::where('uuid', $uuid_3)->first()->id;
    $id_entries_4 = Entry::where('uuid', $uuid_4)->first()->id;
    $id_entries_5 = Entry::where('uuid', $uuid_5)->first()->id;
    $id_entries_6 = Entry::where('uuid', $uuid_6)->first()->id;
    $id_entries_7 = Entry::where('uuid', $uuid_7)->first()->id;

    // Handle relations
    $entry = Entry::where('id', $id_entries_1)->first();
    $entry->season_first_title_id = $id_entries_1;
    $entry->sequel_id = $id_entries_2;
    $entry->save();

    $entry = Entry::where('id', $id_entries_2)->first();
    $entry->season_first_title_id = $id_entries_1;
    $entry->prequel_id = $id_entries_1;
    $entry->save();

    $entry = Entry::where('id', $id_entries_3)->first();
    $entry->season_first_title_id = $id_entries_1;
    $entry->prequel_id = $id_entries_1;
    $entry->save();

    $entry = Entry::where('id', $id_entries_4)->first();
    $entry->season_first_title_id = $id_entries_4;
    $entry->save();

    $entry = Entry::where('id', $id_entries_5)->first();
    $entry->season_first_title_id = $id_entries_5;
    $entry->save();

    $entry = Entry::where('id', $id_entries_6)->first();
    $entry->season_first_title_id = $id_entries_6;
    $entry->save();

    $entry = Entry::where('id', $id_entries_7)->first();
    $entry->season_first_title_id = $id_entries_7;
    $entry->save();

    // Handle other entry related information
    $testDataOffquel = [
      [
        'id_entries' => $id_entries_1,          // parent entry
        'id_entries_offquel' => $id_entries_3,  // offquel entry
      ],
    ];

    $testDataRating = [
      [
        'id_entries' => $id_entries_1,
        'audio' => 6,
        'enjoyment' => 5,
        'graphics' => 4,
        'plot' => 3,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'id_entries' => $id_entries_2,
        'audio' => 7,
        'enjoyment' => 8,
        'graphics' => 9,
        'plot' => 10,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    $testDataRewatch = [
      [
        'id_entries' => $id_entries_1,
        'uuid' => Str::uuid()->toString(),
        'date_rewatched' => Carbon::parse('01-02-2011')->format('Y-m-d'),
      ], [
        'id_entries' => $id_entries_1,
        'uuid' => Str::uuid()->toString(),
        'date_rewatched' => Carbon::parse('01-03-2011')->format('Y-m-d'),
      ], [
        'id_entries' => $id_entries_2,
        'uuid' => Str::uuid()->toString(),
        'date_rewatched' => Carbon::parse('02-01-2013')->format('Y-m-d'),
      ],
    ];

    DB::table('entries_offquel')->insert($testDataOffquel);
    DB::table('entries_rating')->insert($testDataRating);
    DB::table('entries_rewatch')->insert($testDataRewatch);
  }
}
