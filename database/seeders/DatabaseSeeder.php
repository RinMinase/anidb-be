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
      CodecAudioSeeder::class,
      CodecVideoSeeder::class,
      PrioritySeeder::class,
      QualitySeeder::class,
      GenreSeeder::class,

      BucketSeeder::class,      // test data
      BucketSimSeeder::class,   // test data
      CatalogSeeder::class,     // test data
      EntrySeeder::class,       // test data
      GroupSeeder::class,       // test data
      LogSeeder::class,         // test data
      PartialSeeder::class,     // test data
      SequenceSeeder::class,    // test data

      RssSeeder::class,         // test data
      RssItemsSeeder::class,    // test data

      // Four leaf seeds
      FourleafSettingsSeeder::class,
      FourleafGasSeeder::class,
      FourleafMaintenanceSeeder::class,
      FourleafBillsElectricitySeeder::class,

      FourleafElectricitySeeder::class,     // test data

      // PC Seeds
      PCOwnerSeeder::class,
      PCComponentTypeSeeder::class,
      PCSeeder::class,        // test data
    ]);
  }
}
