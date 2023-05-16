<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RssItemsSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $testData = [
      [
        'uuid' => Str::uuid()->toString(),
        'id_rss' => 1,
        'title' => 'Item 1',
        'link' => 'https://example.com/',
        'date' => '2022-01-01 00:00:00',
        'is_read' => false,
        'is_bookmarked' => false,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_rss' => 1,
        'title' => 'Item 2',
        'link' => 'https://example.com/',
        'date' => '2022-01-02 00:00:00',
        'is_read' => false,
        'is_bookmarked' => false,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_rss' => 1,
        'title' => 'Item 3',
        'link' => 'https://example.com/',
        'date' => '2022-01-03 00:00:00',
        'is_read' => true,
        'is_bookmarked' => false,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_rss' => 1,
        'title' => 'Item 4',
        'link' => 'https://example.com/',
        'date' => '2022-01-04 00:00:00',
        'is_read' => false,
        'is_bookmarked' => true,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_rss' => 2,
        'title' => 'Item 2',
        'link' => 'https://example.com/',
        'date' => '2022-01-01 00:00:00',
        'is_read' => true,
        'is_bookmarked' => true,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('rss_items')->insert($testData);
  }
}
