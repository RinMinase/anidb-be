<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_component_types', function (Blueprint $table) {
      $table->id();
      $table->string('type', 32)->unique();
      $table->string('name', 32);
      $table->boolean('is_peripheral')->default(true);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_component_types');
  }
};
