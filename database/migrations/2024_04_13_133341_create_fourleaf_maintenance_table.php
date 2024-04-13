<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('fourleaf_maintenance', function (Blueprint $table) {
      $table->id();

      $table->date('date');
      $table->string('part');
      $table->mediumInteger('odometer');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('fourleaf_maintenance');
  }
};
