<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $withCount = ['bookings'];


  /**
   * Format available from time in hours and minutes only (H:i)
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function availableFromTime(): Attribute
  {
    return Attribute::make(
      get: fn ($value) => Carbon::parse($value)->format('H:i'),
    );
  }


  /**
   * Format available to time in hours and minutes only (H:i)
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function availableToTime(): Attribute
  {
    return Attribute::make(
      get: fn ($value) => Carbon::parse($value)->format('H:i'),
    );
  }


  /**
   * Timeslots
   *
   * @return array
   */
  public function getTimeslotsAttribute()
  {
    $startTime = Carbon::parse($this->available_from_time);
    $endTime = Carbon::parse($this->available_to_time);
    $timeSlots = [];

    while ($startTime->lessThan($endTime)) {
      $timeSlots[] = [
        'start' => Carbon::parse($startTime)->format('H:i'),
        'end' => Carbon::parse($startTime)->addMinutes($this->duration)->format('H:i'),
      ];

      $startTime->addMinutes($this->duration);
    }

    return $timeSlots;
  }


  /**
   * The user created the event
   *
   * @return \App\Models\User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }


  /**
   * The bookings associated with this event
   *
   * @return \Illuminate\Support\Collection<\App\Models\Booking>
   */
  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }
}
