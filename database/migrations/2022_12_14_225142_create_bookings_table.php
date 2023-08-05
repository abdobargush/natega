<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bookings', function (Blueprint $table) {
      $table->id();
      $table->string('booker_name');
      $table->string('booker_email');
      $table->string('calendar_id')->nullable();
      $table->string('calendar_link')->nullable();
      $table->string('meet_link')->nullable();
      $table->date('booked_at_date');
      $table->time('booked_at_time');
      $table->foreignId('event_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('bookings');
  }
};
