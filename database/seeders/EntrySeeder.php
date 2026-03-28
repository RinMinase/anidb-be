<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\EntryGenre;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\EntryWatcher;
use App\Models\Genre;
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
    $uuid_8 = Str::uuid()->toString();
    $uuid_9 = Str::uuid()->toString();

    $id_quality_2160 = Quality::where('quality', '4K 2160p')->first()->id;
    $id_quality_1080 = Quality::where('quality', 'FHD 1080p')->first()->id;
    $id_quality_720 = Quality::where('quality', 'HD 720p')->first()->id;
    $id_quality_480 = Quality::where('quality', 'HQ 480p')->first()->id;
    $id_quality_360 = Quality::where('quality', 'LQ 360p')->first()->id;

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

    $id_watcher_1 = EntryWatcher::where('label', 'Pamm')->first()->id;
    $id_watcher_2 = EntryWatcher::where('label', 'Together')->first()->id;

    $testData = [
      [
        'uuid' => $uuid_1,
        'id_quality' => $id_quality_2160,
        'title' => 'title 1',
        'date_finished' => Carbon::parse('2001-01-01')->format('Y-m-d'),
        'filesize' => 21_331_439_861,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2000,
        'release_season' => 'Winter',
        'episodes' => 1,
        'ovas' => 2,
        'specials' => 3,
        'encoder_video' => 'Alpha',
        'encoder_audio' => 'Alpha',
        'encoder_subs' => 'Alpha',
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_1,
        'id_codec_audio' => $id_codec_audio_4,
        'image' => 'https://res.cloudinary.com/rin-minase/image/upload/v1722926844/entries/n2aipkefesx9ddb1wifd.jpg',
        'id_watcher' => $id_watcher_1,
      ],
      [
        'uuid' => $uuid_2,
        'id_quality' => $id_quality_1080,
        'title' => 'title 2',
        'date_finished' => Carbon::parse('2011-01-01')->format('Y-m-d'),
        'filesize' => 16_884_590_183,
        'duration' => 1_000_000,
        'season_number' => 2,
        'release_year' => 2010,
        'release_season' => 'Summer',
        'episodes' => 2,
        'ovas' => 4,
        'specials' => 6,
        'encoder_video' => 'Alpha',
        'encoder_audio' => 'Alpha',
        'encoder_subs' => 'Alpha',
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_10,
        'image' => null,
        'id_watcher' => $id_watcher_1,
      ],
      [
        'uuid' => $uuid_3,
        'id_quality' => $id_quality_720,
        'title' => 'title 1 offquel',
        'date_finished' => Carbon::parse('2011-01-01')->format('Y-m-d'),
        'filesize' => 65_493_448_000,
        'duration' => 10_000,
        'season_number' => 0,
        'release_year' => 2010,
        'release_season' => 'Spring',
        'episodes' => 1,
        'ovas' => 2,
        'specials' => 3,
        'encoder_video' => 'Beta',
        'encoder_audio' => 'Beta',
        'encoder_subs' => 'Beta',
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_2,
        'id_codec_audio' => $id_codec_audio_9,
        'image' => null,
        'id_watcher' => $id_watcher_2,
      ],
      [
        'uuid' => $uuid_4,
        'id_quality' => $id_quality_1080,
        'title' => 'another title 3',
        'date_finished' => Carbon::parse('2015-01-01')->format('Y-m-d'),
        'filesize' => 57_989_090_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => 'Spring',
        'episodes' => 1,
        'ovas' => 2,
        'specials' => 3,
        'encoder_video' => 'Gamma',
        'encoder_audio' => 'Gamma',
        'encoder_subs' => 'Gamma',
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_3,
        'id_codec_audio' => $id_codec_audio_6,
        'image' => null,
        'id_watcher' => $id_watcher_2,
      ],
      [
        'uuid' => $uuid_5,
        'id_quality' => $id_quality_720,
        'title' => 'another title 4',
        'date_finished' => Carbon::parse('2014-05-01')->format('Y-m-d'),
        'filesize' => 74_748_957_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => 'Winter',
        'episodes' => 10,
        'ovas' => 20,
        'specials' => 30,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_12,
        'image' => null,
        'id_watcher' => null,
      ],
      [
        'uuid' => $uuid_6,
        'id_quality' => $id_quality_480,
        'title' => 'Another title 5',
        'date_finished' => Carbon::parse('2015-04-01')->format('Y-m-d'),
        'filesize' => 75_773_386_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => 'Spring',
        'episodes' => 1,
        'ovas' => null,
        'specials' => null,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_7,
        'image' => null,
        'id_watcher' => null,
      ],
      [
        'uuid' => $uuid_7,
        'id_quality' => $id_quality_480,
        'title' => '2title 6',
        'date_finished' => Carbon::parse('2015-06-20')->format('Y-m-d'),
        'filesize' => 26_648_304_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2014,
        'release_season' => 'Spring',
        'episodes' => 1,
        'ovas' => null,
        'specials' => null,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_4,
        'id_codec_audio' => $id_codec_audio_11,
        'image' => null,
        'id_watcher' => null,
      ],
      [
        'uuid' => $uuid_8,
        'id_quality' => $id_quality_480,
        'title' => 'uncategorized title 1',
        'date_finished' => Carbon::parse('2015-07-01')->format('Y-m-d'),
        'filesize' => 26_648_304_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => 2020,
        'release_season' => null,
        'episodes' => 1,
        'ovas' => null,
        'specials' => null,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => false,
        'id_codec_video' => null,
        'id_codec_audio' => null,
        'image' => null,
        'id_watcher' => null,
      ],
      [
        'uuid' => $uuid_9,
        'id_quality' => $id_quality_360,
        'title' => 'uncategorized title 2',
        'date_finished' => Carbon::parse('2016-08-01')->format('Y-m-d'),
        'filesize' => 26_648_304_000,
        'duration' => 10_000,
        'season_number' => 1,
        'release_year' => null,
        'release_season' => null,
        'episodes' => null,
        'ovas' => null,
        'specials' => null,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => false,
        'id_codec_video' => null,
        'id_codec_audio' => null,
        'image' => null,
        'id_watcher' => null,
      ],
    ];

    $testData = collect($testData)
      ->map(function (array $item) {
        return array_merge($item, [
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      })->toArray();

    Entry::insert($testData);

    $addtl_test_data = [];

    for ($i = 1; $i <= 15; $i++) {
      array_push($addtl_test_data, [
        'id_quality' => $id_quality_480,
        'uuid' => Str::uuid(),
        'title' => 'additional test data ' . $i,
        'date_finished' => '2000-01-' . $i,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }

    Entry::insert($addtl_test_data);

    $id_entries_1 = Entry::where('uuid', $uuid_1)->first()->id;
    $id_entries_2 = Entry::where('uuid', $uuid_2)->first()->id;
    $id_entries_3 = Entry::where('uuid', $uuid_3)->first()->id;
    $id_entries_4 = Entry::where('uuid', $uuid_4)->first()->id;
    $id_entries_5 = Entry::where('uuid', $uuid_5)->first()->id;
    $id_entries_6 = Entry::where('uuid', $uuid_6)->first()->id;
    $id_entries_7 = Entry::where('uuid', $uuid_7)->first()->id;

    $id_genre_action = Genre::where('genre', 'Action')->first()->id;
    $id_genre_comedy = Genre::where('genre', 'Comedy')->first()->id;

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
    $test_data_offquel = [
      [
        'id_entries' => $id_entries_1,          // parent entry
        'id_entries_offquel' => $id_entries_3,  // offquel entry
      ],
    ];

    $test_data_rating = [
      [
        'id_entries' => $id_entries_1,
        'audio' => 2,
        'enjoyment' => 2,
        'graphics' => 1,
        'plot' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
      [
        'id_entries' => $id_entries_2,
        'audio' => 3,
        'enjoyment' => 4,
        'graphics' => 5,
        'plot' => 5,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    $test_data_rewatch = [
      [
        'id_entries' => $id_entries_1,
        'uuid' => Str::uuid()->toString(),
        'date_rewatched' => Carbon::parse('01-02-2011')->format('Y-m-d'),
      ],
      [
        'id_entries' => $id_entries_1,
        'uuid' => Str::uuid()->toString(),
        'date_rewatched' => Carbon::parse('01-03-2011')->format('Y-m-d'),
      ],
      [
        'id_entries' => $id_entries_2,
        'uuid' => Str::uuid()->toString(),
        'date_rewatched' => Carbon::parse('02-01-2013')->format('Y-m-d'),
      ],
    ];

    $test_data_genre = [[
      'id_entries' => $id_entries_1,
      'id_genres' => $id_genre_action,
    ], [
      'id_entries' => $id_entries_1,
      'id_genres' => $id_genre_comedy,
    ]];

    EntryOffquel::insert($test_data_offquel);
    EntryRating::insert($test_data_rating);
    EntryRewatch::insert($test_data_rewatch);
    EntryGenre::insert($test_data_genre);


    /**
     * Additional Entry-relational data
     */
    $main_1_id = Str::uuid()->toString();
    $main_2_id = Str::uuid()->toString();
    $main_3_id = Str::uuid()->toString();
    $main_4_id = Str::uuid()->toString();
    $main_5_id = Str::uuid()->toString();
    $spin_1a_id = Str::uuid()->toString(); // connected to main 2
    $spin_1b_id = Str::uuid()->toString(); // connected to spin 1a
    $spin_2a_id = Str::uuid()->toString(); // connected to main 3
    $spin_2b_id = Str::uuid()->toString(); // connected to spin 2a
    $spin_2c_id = Str::uuid()->toString(); // connected to spin 2b
    $spin_3a_id = Str::uuid()->toString(); // connected to spin 2a
    $spin_3b_id = Str::uuid()->toString(); // connected to spin 2b
    $spin_4a_id = Str::uuid()->toString(); // connected to main 1
    $spin_4b_id = Str::uuid()->toString(); // connected to spin 4a
    $spin_5a_id = Str::uuid()->toString(); // connected to spin 2b
    $spin_5b_id = Str::uuid()->toString(); // connected to spin 5a

    /**
     * Diagram:
     * =======================
     * Main 1 -> Main 2 -> Main 3 -> Main 4 -> Main 5
     *  |        |         └-> Spin 2A -> Spin 2B -> Spin 2C
     *  |        |              |          └-> Spin 5A -> Spin 5B
     *  |        |              └-> Spin 3A -> Spin 3B
     *  |        └-> Spin 1A -> Spin 1B
     *  └-> Spin 4A -> Spin 4B
     */

    $date_value = Carbon::parse('2001-01-01')->format('Y-m-d');
    $datetime_value = Carbon::now()->format('Y-m-d H:i:s');
    $addtl_relational_data = [
      ['uuid' => $main_1_id, 'title' => 'Main 1'],
      ['uuid' => $main_2_id, 'title' => 'Main 2'],
      ['uuid' => $main_3_id, 'title' => 'Main 3'],
      ['uuid' => $main_4_id, 'title' => 'Main 4'],
      ['uuid' => $main_5_id, 'title' => 'Main 5'],
      ['uuid' => $spin_1a_id, 'title' => 'Spin 1A'],
      ['uuid' => $spin_1b_id, 'title' => 'Spin 1B'],
      ['uuid' => $spin_2a_id, 'title' => 'Spin 2A'],
      ['uuid' => $spin_2b_id, 'title' => 'Spin 2B'],
      ['uuid' => $spin_2c_id, 'title' => 'Spin 2C'],
      ['uuid' => $spin_3a_id, 'title' => 'Spin 3A'],
      ['uuid' => $spin_3b_id, 'title' => 'Spin 3B'],
      ['uuid' => $spin_4a_id, 'title' => 'Spin 4A'],
      ['uuid' => $spin_4b_id, 'title' => 'Spin 4B'],
      ['uuid' => $spin_5a_id, 'title' => 'Spin 5A'],
      ['uuid' => $spin_5b_id, 'title' => 'Spin 5B'],
    ];

    foreach ($addtl_relational_data as &$item) {
      $item['id_quality'] = $id_quality_1080;
      $item['date_finished'] = $date_value;
      $item['episodes'] = 1;
      $item['created_at'] = $datetime_value;
      $item['updated_at'] = $datetime_value;
    }
    unset($item);

    Entry::insert($addtl_relational_data);

    $main_1 = Entry::where('uuid', $main_1_id)->first();
    $main_2 = Entry::where('uuid', $main_2_id)->first();
    $main_3 = Entry::where('uuid', $main_3_id)->first();
    $main_4 = Entry::where('uuid', $main_4_id)->first();
    $main_5 = Entry::where('uuid', $main_5_id)->first();
    $spin_1a = Entry::where('uuid', $spin_1a_id)->first();
    $spin_1b = Entry::where('uuid', $spin_1b_id)->first();
    $spin_2a = Entry::where('uuid', $spin_2a_id)->first();
    $spin_2b = Entry::where('uuid', $spin_2b_id)->first();
    $spin_2c = Entry::where('uuid', $spin_2c_id)->first();
    $spin_3a = Entry::where('uuid', $spin_3a_id)->first();
    $spin_3b = Entry::where('uuid', $spin_3b_id)->first();
    $spin_4a = Entry::where('uuid', $spin_4a_id)->first();
    $spin_4b = Entry::where('uuid', $spin_4b_id)->first();
    $spin_5a = Entry::where('uuid', $spin_5a_id)->first();
    $spin_5b = Entry::where('uuid', $spin_5b_id)->first();

    $main_1->update(['sequel_id' => $main_2->id]);
    $main_2->update(['prequel_id' => $main_1->id, 'sequel_id' => $main_3->id]);
    $main_3->update(['prequel_id' => $main_2->id, 'sequel_id' => $main_4->id]);
    $main_4->update(['prequel_id' => $main_3->id, 'sequel_id' => $main_5->id]);
    $main_5->update(['prequel_id' => $main_4->id]);

    $spin_1a->update(['prequel_id' => $main_2->id, 'sequel_id' => $spin_1b->id]);
    $spin_1b->update(['prequel_id' => $spin_1a->id]);

    $spin_2a->update(['prequel_id' => $main_3->id, 'sequel_id' => $spin_2b->id]);
    $spin_2b->update(['prequel_id' => $spin_2a->id, 'sequel_id' => $spin_2c->id]);
    $spin_2c->update(['prequel_id' => $spin_2b->id]);

    $spin_3a->update(['prequel_id' => $spin_2a->id, 'sequel_id' => $spin_3b->id]);
    $spin_3b->update(['prequel_id' => $spin_3a->id]);

    $spin_4a->update(['prequel_id' => $main_1->id, 'sequel_id' => $spin_4b->id]);
    $spin_4b->update(['prequel_id' => $spin_4a->id]);

    $spin_5a->update(['prequel_id' => $spin_2b->id, 'sequel_id' => $spin_5b->id]);
    $spin_5b->update(['prequel_id' => $spin_5a->id]);

    $addtl_relational_data_offquels = [
      ['id_entries' => $main_1->id, 'id_entries_offquel' => $spin_4a->id],
      ['id_entries' => $main_2->id, 'id_entries_offquel' => $spin_1a->id],
      ['id_entries' => $main_3->id, 'id_entries_offquel' => $spin_2a->id],
      ['id_entries' => $spin_2a->id, 'id_entries_offquel' => $spin_3a->id],
      ['id_entries' => $spin_2b->id, 'id_entries_offquel' => $spin_5a->id],
    ];

    EntryOffquel::insert($addtl_relational_data_offquels);
  }
}
