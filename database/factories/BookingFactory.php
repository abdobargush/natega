<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'calendar_link' => $this->faker->url(),
      'meet_link' => $this->faker->url(),
      'booked_at_date' => $this->faker->date(),
      'booked_at_time' => $this->faker->time('H:i'),
      'event_id' => Event::inRandomOrder()->first() ?? Event::factory()->create(),
    ];
  }
}
