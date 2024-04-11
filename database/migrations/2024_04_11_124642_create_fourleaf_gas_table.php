<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('fourleaf_gas', function (Blueprint $table) {
      $table->id();

      $table->date('date');
      $table->tinyInteger('from_bars');
      $table->tinyInteger('to_bars');
      $table->mediumInteger('odometer');
      $table->float('price_per_liter')->nullable();
      $table->float('liters_filled')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('fourleaf_gas');
  }
};
