<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('entries', function (Blueprint $table) {
      $table->integer('id_watcher')->nullable()->after('image');
      $table->foreign('id_watcher')->references('id')->on('entries_watchers');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('entries', function (Blueprint $table) {
      //
    });
  }
};
