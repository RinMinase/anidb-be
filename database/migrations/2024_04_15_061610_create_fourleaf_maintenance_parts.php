<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('fourleaf_maintenance_parts', function (Blueprint $table) {
      $table->id();

      $table->integer('id_fourleaf_maintenance')->unsigned();
      $table->foreign('id_fourleaf_maintenance')->references('id')->on('fourleaf_maintenance')->onDelete('cascade');

      $table->enum(
        'part',
        [
          'others',
          'ac_coolant',
          'battery',
          'brake_fluid',
          'engine_oil',
          'power_steering_fluid',
          'radiator_fluid',
          'spark_plugs',
          'tires',
          'transmission',
        ],
      );
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('fourleaf_maintenance_parts');
  }
};
