<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'uuid' => Str::uuid()->toString(),
        'table_changed' => 'hdd',
        'id_changed' => 1,
        'description' => 'some description',
        'action' => 'add',
        'created_at' => '2022-01-01 00:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'table_changed' => 'marathon',
        'id_changed' => 1,
        'description' => 'title from "old" to "new"',
        'action' => 'edit',
        'created_at' => '2022-01-02 00:00:00',
      ],
    ];

    DB::table('logs')->insert($testData);
  }
}
