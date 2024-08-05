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
    Schema::create('entries_offquel', function (Blueprint $table) {
      $table->id();

      // parent entry
      $table->integer('id_entries')->unsigned()->nullable();
      $table->foreign('id_entries')->references('id')->on('entries')->onDelete('cascade');

      // child / offquel entry
      $table->integer('id_entries_offquel')->unsigned()->nullable();
      $table->foreign('id_entries_offquel')->references('id')->on('entries')->onDelete('cascade');

      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('entries_offquel');
  }
};
