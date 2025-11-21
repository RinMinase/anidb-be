<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\User;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    // Only seed users on local env
    if (config('app.env') === 'local' && config('app.platform') === 'local') {
      User::create([
        'uuid' => Str::uuid()->toString(),
        'username' => 'admin',
        'password' => bcrypt('pass'),
        'is_admin' => true,
      ]);

      User::create([
        'uuid' => Str::uuid()->toString(),
        'username' => 'user',
        'password' => bcrypt('pass'),
        'is_admin' => false,
      ]);
    }

    $this->call([
      CodecAudioSeeder::class,
      CodecVideoSeeder::class,
      PrioritySeeder::class,
      QualitySeeder::class,
      GenreSeeder::class,
      EntryWatcherSeeder::class,

      BucketSeeder::class,      // test data
      BucketSimSeeder::class,   // test data
      CatalogSeeder::class,     // test data
      EntrySeeder::class,       // test data
      GroupSeeder::class,       // test data
      LogSeeder::class,         // test data
      PartialSeeder::class,     // test data
      SequenceSeeder::class,    // test data

      // Four leaf seeds
      FourleafSettingsSeeder::class,
      FourleafGasSeeder::class,
      FourleafMaintenanceSeeder::class,
      FourleafBillsElectricitySeeder::class,

      FourleafElectricitySeeder::class,     // test data

      // PC Seeds
      PCComponentTypeSeeder::class,
      // PCOwnerSeeder::class,   // test data
      // PCSeeder::class,        // test data
    ]);
  }
}
