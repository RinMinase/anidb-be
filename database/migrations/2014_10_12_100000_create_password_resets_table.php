<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('password_resets', function (Blueprint $table) {
      $table->unlogged();

      $table->string('username')->index();
      $table->string('token');
      $table->timestamp('created_at')->nullable();

      $table->unlogged(false);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('password_resets');
  }
}
