<?php

namespace App\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class BackupDatabase extends Command {

  protected $signature = 'app:backup-database';
  protected $description = 'Backup database daily';

  public function handle() {
    return Command::SUCCESS;
  }
}
