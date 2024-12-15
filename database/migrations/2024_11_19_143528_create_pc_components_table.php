<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_components', function (Blueprint $table) {
      $table->id();

      $table->integer('id_type')->nullable();
      $table->foreign('id_type')->references('id')->on('pc_component_types');

      $table->string('name', 64);
      $table->string('description', 64)->nullable();

      $table->integer('price')->nullable();
      $table->date('purchase_date')->nullable();
      $table->string('purchase_location', 64)->nullable();
      $table->string('purchase_notes', 64)->nullable();

      $table->boolean('is_onhand')->nullable();
      $table->boolean('is_purchased')->nullable()->default(false);

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_components');
  }
};
