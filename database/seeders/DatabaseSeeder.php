<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    $this->call([
      PrioritySeeder::class,
      QualitySeeder::class,

      CatalogSeeder::class,     // test data
      EntrySeeder::class,       // test data
      HddSeeder::class,         // test data
      LogSeeder::class,         // test data
      MarathonSeeder::class,    // test data
      PartialSeeder::class,     // test data
    ]);
  }
}
