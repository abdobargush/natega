<?php

namespace Tests\Feature\Controllers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventControllerTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  protected User $user;

  public function setUp(): void
  {
    parent::setUp();
    /** @var \Illuminate\Contracts\Auth\Authenticatable */
    $user = User::factory()->create();
    $this->user = $user;
    $this->actingAs($user);
  }

  public function test_index_does_render_with_user_events()
  {
    $events = Event::factory(5)->create(['user_id' => $this->user])
      ->each(function ($event) {
        $event->loadCount('bookings');
      });
    $user2 = User::factory()->create();
    $events_ignored = Event::factory(5)->create(['user_id' => $user2]);

    $this->get(route('events.index'))
      ->assertStatus(200)
      ->assertInertia(
        fn ($page) =>
        $page->component('Events/Index')
          ->has('events', 5)
          ->where('events', $events)
      );
  }

  public function test_create_renders_create_form()
  {
    $this->get(route('events.create'))
      ->assertStatus(200)
      ->assertInertia(
        fn ($page) =>
        $page->component('Events/Create')
      );
  }

  public function test_store_does_presist_event_to_database()
  {
    $payload = [
      'title' => $this->faker->sentence(),
      'description' => $this->faker->paragraph(),
      'duration' => $this->faker->randomElement([15, 30, 45, 60]),
      'slug' => $this->faker->unique()->slug(),
      'color' => $this->faker->hexColor(),
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setHour(8)->format('H:i'),
      'available_to_time' => Carbon::now()->setHour(9)->format('H:i'),
    ];

    $this->post(route('events.store'), $payload)
      ->assertRedirect()
      ->assertSessionHasNoErrors()
      ->assertSessionHas([
        'alert_type' => 'success',
        'alert_message' => "Event created successfully!"
      ]);
    $this->assertDatabaseHas(Event::class, $payload);
  }

  public function test_show_public_renders_public_event_page()
  {
    $event = Event::factory()->create(['user_id' => $this->user]);

    $this->get(route('events.show.public', $event))
      ->assertStatus(200)
      ->assertInertia(
        fn ($page) =>
        $page->component('Events/ShowPublic')
          ->has('event')
          ->where('event.id', $event->id)
          ->where('event.title', $event->title)
          ->where('event.user', $this->user)
          ->has('event.timeslots')
      );
  }

  public function test_edit_renders_edit_form()
  {
    $event = Event::factory()->create(['user_id' => $this->user]);

    $this->get(route('events.edit', $event))
      ->assertStatus(200)
      ->assertInertia(
        fn ($page) =>
        $page->component('Events/Edit')
          ->has('event')
          ->where('event.id', $event->id)
          ->where('event.title', $event->title)
      );
  }

  public function test_update_does_presist_edited_event_data_to_database()
  {
    $event = Event::factory()->create([
      'title' => 'Old title',
      'description' => 'Old description',
      'slug' => 'old_slug',
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setHour(8)->format('H:i'),
      'available_to_time' => Carbon::now()->setHour(9)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $updated_data = [
      'title' => 'Updated title',
      'description' => 'Updated description',
      'slug' => 'updated_slug',
    ];
    $payload = array_merge($event->toArray(), $updated_data);

    $this->patch(route('events.update', $event), $payload)
      ->assertRedirect()
      ->assertSessionHasNoErrors()
      ->assertSessionHas([
        'alert_type' => 'success',
        'alert_message' => "Event updated successfully!"
      ]);
    $this->assertDatabaseHas(Event::class, $updated_data);
  }

  public function test_user_can_decline_booking()
  {
    $event = Event::factory()->create(['user_id' => $this->user]);

    $this->delete(route('events.destroy', $event))
      ->assertRedirect()
      ->assertSessionHas(['alert_type' => 'success']);
    $this->assertDatabaseMissing('events', $event->toArray());
  }

  public function test_only_event_creator_can_update_and_delete()
  {
    $otherUser = User::factory()->create(['id' => '2']);
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setHour(8)->format('H:i'),
      'available_to_time' => Carbon::now()->setHour(9)->format('H:i'),
      'user_id' => $otherUser
    ]);

    $this->patch(route('events.update', $event), $event->toArray())
      ->assertForbidden();
    $this->delete(route('events.destroy', $event))
      ->assertForbidden();
  }

  public function test_store_and_update_requires_available_to_date_after_from_date()
  {
    $event = Event::factory()->create(['user_id' => $this->user]);
    $testedFields = [
      'available_from_date' => Carbon::tomorrow()->toDateString(),
      'available_to_date' => Carbon::today()->toDateString(),
    ];
    $otherFields = [
      'title' => $this->faker->sentence(),
      'description' => $this->faker->paragraph(),
      'duration' => $this->faker->randomElement([15, 30, 45, 60]),
      'slug' => $this->faker->unique()->slug(),
      'color' => $this->faker->hexColor(),
      'available_from_time' => Carbon::now()->setHour(8)->format('H:i'),
      'available_to_time' => Carbon::now()->setHour(9)->format('H:i'),
    ];
    $payload = [...$testedFields, ...$otherFields];

    $this->post(route('events.store'), $payload)
      ->assertSessionHasErrors('available_to_date');
    $this->patch(route('events.update', $event), $payload)
      ->assertSessionHasErrors('available_to_date');
  }

  public function test_store_and_update_requires_available_to_time_after_from_time()
  {
    $event = Event::factory()->create(['user_id' => $this->user]);
    $testedFields = [
      'available_from_time' => Carbon::now()->setHour(9)->format('H:i'),
      'available_to_time' => Carbon::now()->setHour(8)->format('H:i'),
    ];
    $otherFields = [
      'title' => $this->faker->sentence(),
      'description' => $this->faker->paragraph(),
      'duration' => $this->faker->randomElement([15, 30, 45, 60]),
      'slug' => $this->faker->unique()->slug(),
      'color' => $this->faker->hexColor(),
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
    ];
    $payload = [...$testedFields, ...$otherFields];

    $this->post(route('events.store'), $payload)
      ->assertSessionHasErrors('available_to_time');
    $this->patch(route('events.update', $event), $payload)
      ->assertSessionHasErrors('available_to_time');
  }
}
