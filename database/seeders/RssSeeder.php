<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RssSeeder extends Seeder {
  public $uuid_1 = '9b0f4937-afeb-4a78-9170-79fdf7047bd8';
  public $uuid_2 = 'e2d30a82-495c-4016-ae02-c36d5b1e9ab4';

  /**
   * Run the database seeds.
   */
  public function run(): void {
    $testData = [
      [
        'uuid' => $this->uuid_1,
        'title' => 'Sample RSS Feed 1',
        'last_updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'update_speed_mins' => 120,
        'url' => 'https://www.nasa.gov/rss/dyn/breaking_news.rss',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => $this->uuid_2,
        'title' => 'Sample RSS Feed 2',
        'last_updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'update_speed_mins' => 120,
        'url' => 'https://www.nasa.gov/rss/dyn/educationnews.rss',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('rss')->insert($testData);
  }
}
