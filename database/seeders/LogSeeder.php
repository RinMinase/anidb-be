<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LogSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'table_changed' => 'hdd',
        'id_changed' => 1,
        'description' => 'some description',
        'action' => 'add',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
      [
        'table_changed' => 'marathon',
        'id_changed' => 1,
        'description' => 'title from "old" to "new"',
        'action' => 'edit',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('logs')->insert($testData);
  }
}
