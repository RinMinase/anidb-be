<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('logs', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->string('table_changed', 32)->nullable();
      $table->string('id_changed', 64)->nullable();
      $table->string('description', 256)->nullable();

      // enum not used as it can be anything
      // for now: add, delete, edit is preferred
      $table->string('action', 32)->nullable();

      $table->timestamp('created_at');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('logs');
  }
};
