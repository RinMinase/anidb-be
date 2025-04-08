<?php

namespace App\Commands;

use Illuminate\Console\Command;

use App\Enums\ExportTypesEnum;
use App\Repositories\ExportRepository;

class BackupDatabase extends Command {

  protected $signature = 'app:backup-database';
  protected $description = 'Backup database daily';

  public function handle() {
    ExportRepository::generate_export(ExportTypesEnum::JSON, true);

    return Command::SUCCESS;
  }
}
