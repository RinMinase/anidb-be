<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration {

  public function up() {
    Schema::create('password_resets', function (Blueprint $table) {
      $table->unlogged();

      $table->string('username')->index();
      $table->string('token');
      $table->timestamp('created_at')->nullable();

      $table->unlogged(false);
    });
  }

  public function down() {
    Schema::dropIfExists('password_resets');
  }
}
