<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('electricity_bills', function (Blueprint $table) {
      $table->id();
      $table->timestamps();

      // Date is YYYY-MM (date is always at 01)
      $table->date('bill_date'); // required

      $table->date('period_from')->nullable();
      $table->date('period_to')->nullable();

      $table->float('calculated_kwh_cost')->nullable();
      $table->float('avg_kwh')->nullable();
      $table->integer('reading_from')->nullable();
      $table->integer('reading_to')->nullable();

      $table->integer('reading_total'); // required
      $table->float('total_cost'); //required

      // Total prices, not price per kWh
      $table->float('price_generation')->nullable();
      $table->float('price_distribution')->nullable();
      $table->float('price_govt')->nullable();

      // Per kWh pricing
      $table->float('kwh_price_generation_charge')->nullable();
      $table->float('kwh_price_transmission_charge')->nullable();
      $table->float('kwh_price_distribution_charge')->nullable();

      $table->string('notes')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('electricity_bills');
  }
};
