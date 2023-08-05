<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'title' => $this->faker->sentence(),
      'description' => $this->faker->paragraph(),
      'duration' => $this->faker->randomElement([15, 30, 45, 60]),
      'slug' => $this->faker->unique()->slug(),
      'color' => $this->faker->hexColor(),
      'available_from_date' => $this->faker->date(),
      'available_to_date' => $this->faker->date(),
      'available_from_time' => $this->faker->time('H:i'),
      'available_to_time' => $this->faker->time('H:i'),
      'user_id' => User::inRandomOrder()->first() ?? User::factory()->create()
    ];
  }
}
