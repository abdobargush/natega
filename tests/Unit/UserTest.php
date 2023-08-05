<?php

namespace Tests\Unit;

use App\Models\Booking;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class UserTest extends TestCase
{
  use RefreshDatabase;

  public function test_it_has_events()
  {
    $user = User::factory()->create();
    $event = Event::factory(5)->create([
      'user_id' => $user
    ]);

    $this->assertInstanceOf(HasMany::class, $user->events());
    $this->assertInstanceOf(Collection::class, $user->events);
    $this->assertInstanceOf(Event::class, $user->events->first());
  }

  public function test_it_has_bookings()
  {
    $user = User::factory()->create();
    $event = Event::factory()->create([
      'user_id' => $user
    ]);
    $bookings = Booking::factory(5)->create([
      'event_id' => $event
    ]);

    $this->assertInstanceOf(HasManyThrough::class, $user->bookings());
    $this->assertInstanceOf(Collection::class, $user->bookings);
    $this->assertInstanceOf(Booking::class, $user->bookings->first());
  }
}
