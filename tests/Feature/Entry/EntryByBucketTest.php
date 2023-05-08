<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

use App\Models\Entry;

class EntryByBucketTest extends BaseTestCase {

  private $entry_uuid = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

  private function setup_config() {
    $faker = Faker::create();

    // Clearing possible duplicate data
    $this->setup_clear();

    Entry::create([
      'uuid' => $this->entry_uuid,
      'id_quality' => 1,
      'title' => "title",
      'date_finished' => "2001-01-01",
      'duration' => 10_000,
      'filesize' => 10_000_000,
      'episodes' => 30,
      'ovas' => 20,
      'specials' => 10,
      'encoder_video' => "encoder video",
      'encoder_audio' => "encoder audio",
      'encoder_subs' => "encoder subs",
      'codec_hdr' => 1,
      'codec_video' => 1,
      'codec_audio' => 1,
      'release_year' => 2000,
      'release_season' => "Winter",
      'variants' => "variant",
      'remarks' => "remark",
    ]);

    $uuid_list = [];
    $additional_test_data = [];

    for ($i = 0; $i < 40; $i++) {
      $new_uuid = Str::uuid()->toString();

      while (in_array($new_uuid, $uuid_list)) {
        $new_uuid = Str::uuid()->toString();
      }

      $uuid_list[] = $new_uuid;
      $additional_test_data[] = [
        'uuid' => $new_uuid,
        'id_quality' => 1,
        'title' => 'test data --- ' . $faker->text(20),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ];
    }

    Entry::insert($additional_test_data);
  }

  private function setup_clear() {
    Entry::where('uuid', $this->entry_uuid)->forceDelete();

    $entries = Entry::where('title', 'LIKE', 'test data --- %')->get();

    foreach ($entries as $entry) {
      if ($entry->season_first_title_id) $entry->season_first_title()->dissociate();
      if ($entry->prequel_id) $entry->prequel()->dissociate();
      if ($entry->sequel_id) $entry->sequel()->dissociate();

      $entry->save();
    }

    $entries->map(fn ($entry) => $entry->forceDelete());
  }
}
