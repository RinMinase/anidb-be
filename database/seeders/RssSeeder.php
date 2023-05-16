<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RssSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $testData = [
      [
        'uuid' => Str::uuid()->toString(),
        'title' => 'Sample RSS Feed 1',
        'last_updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'update_speed_mins' => 120,
        'url' => 'https://www.nasa.gov/rss/dyn/breaking_news.rss',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
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
