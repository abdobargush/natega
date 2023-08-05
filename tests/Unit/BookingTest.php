<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{

  use RefreshDatabase;

  public function test_it_has_event()
  {
    $booking = Booking::factory()->create();

    $this->assertInstanceOf(Event::class, $booking->event);
  }
}
