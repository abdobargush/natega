<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTest extends TestCase
{
  use RefreshDatabase;

  public function test_it_has_timeslots_attribute()
  {
    $event = Event::factory()->make([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(8, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(9, 00)->format('H:i'),
      'duration' => 30,
    ]);

    $this->assertCount(2, $event->timeslots);
    $this->assertEquals([
      ['start' => '08:00', 'end' => '08:30'],
      ['start' => '08:30', 'end' => '09:00']
    ], $event->timeslots);
  }

  public function test_it_has_user()
  {
    $event = Event::factory()->create();

    $this->assertInstanceOf(User::class, $event->user);
  }

  public function test_it_has_bookings()
  {
    $event = Event::factory()->create();
    $bookings = Booking::factory(5)->create(['event_id' => $event]);

    $this->assertInstanceOf(HasMany::class, $event->bookings());
    $this->assertInstanceOf(Collection::class, $event->bookings);
    $this->assertInstanceOf(Booking::class, $event->bookings->first());
  }
}
