<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\Group;

class GroupSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $groups = [
      'A&C',
      'Anbu',
      'ANE',
      'AonE',
      'Asakura',
      'Asenshi',
      'Beatrice-Raws',
      'BlurayDesuYo',
      'Chihiro',
      'Coalgirls',
      'Commie',
      'Cyan',
      'DameDesuYo',
      'DeadNews',
      'DmonHiro',
      'Doki',
      'Erai-raws',
      'EMBER',
      'GSK_kun',
      'HorribleSubs',
      'iAHD',
      'IrizaRaws',
      'Kametsu',
      'kBaraka',
      'LostYears',
      'LowPower-Raws',
      'Moozzi2',
      'MTBB',
      'neko-raws',
      'Nep_Blanc',
      'Nii-sama',
      'Project-GXS',
      'Rasetsu',
      'Raws-Maji',
      'Reinforce',
      'sam',
      'SallySubs',
      'sergey_krs',
      'SCP-2223',
      'ShowY',
      'Snow-Raws',
      'SubsPlease',
      'THORA',
      'tlacatlc6',
      'Tsundere',
      'UCCUSS',
      'VCB-Studio',
      'Vivid',
      'Yousei-raws',
      'Zurako',
    ];

    foreach ($groups as $group) {
      Group::create([
        'uuid' => Str::uuid()->toString(),
        'name' => $group,
      ]);
    }
  }
}
