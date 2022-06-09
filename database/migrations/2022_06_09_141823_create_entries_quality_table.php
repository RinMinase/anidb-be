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
    Schema::create('entries_quality', function (Blueprint $table) {
      $table->id();

      $table->integer('id_entries')->unsigned()->nullable();
      $table->foreign('id_entries')->references('id')->on('entries');

      $table->string('quality', 16)->nullable();

      $table->timestamp('created_at');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('entries_quality');
  }
};
