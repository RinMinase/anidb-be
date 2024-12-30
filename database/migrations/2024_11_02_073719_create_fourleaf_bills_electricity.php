<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('fourleaf_bills_electricity', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();

      // unique string for monthly readings
      // format yyyymm, e.g. 202010
      $table->integer('uid')->unique();

      $table->smallInteger('kwh');
      $table->float('cost')->nullable();
      $table->boolean('estimated_kwh')->nullable()->default(false);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('fourleaf_bills_electricity');
  }
};
