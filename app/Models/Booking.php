<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
  use HasFactory;

  protected $guarded = [];

  /**
   * Format booked_at_time in hours and minutes only (H:i)
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function bookedAtTime(): Attribute
  {
    return Attribute::make(
      get: fn ($value) => Carbon::parse($value)->format('H:i'),
    );
  }

  /**
   * The event this bookin is associated with
   *
   * @return \App\Models\Event
   */
  public function event()
  {
    return $this->belongsTo(Event::class);
  }
}
