<?php

namespace Tests\Feature\Controllers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\BookingDeclinedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingsControllerTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  protected User $user;

  public function setUp(): void
  {
    parent::setUp();

    /** @var \Illuminate\Contracts\Auth\Authenticatable */
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->user = $user;
  }

  public function test_index_does_render_with_events_and_bookings()
  {
    $event1 = Event::factory()->create(['user_id' => $this->user]);
    $event2 = Event::factory()->create(['user_id' => $this->user]);
    $booking1 = Booking::factory()->create(['event_id' => $event1->id]);
    $booking2 = Booking::factory()->create(['event_id' => $event2->id]);

    $this->get(route('bookings.index'))
      ->assertStatus(200)
      ->assertInertia(
        fn ($page) =>
        $page->component('Bookings/Index')
          ->has('bookings', 2)
          ->where('bookings.0.title', $event1->title)
          ->where('bookings.1.title', $event2->title)
          ->where('bookings.0.bookings.0.booker_name', $booking1->booker_name)
          ->where('bookings.1.bookings.0.booker_name', $booking2->booker_name)
      );
  }

  public function test_index_does_not_show_events_without_bookings()
  {
    $event1 = Event::factory()->create();
    $event2 = Event::factory()->create();
    $booking1 = Booking::factory()->create(['event_id' => $event1->id]);

    $this->get(route('bookings.index'))
      ->assertStatus(200)
      ->assertInertia(
        fn ($page) =>
        $page->component('Bookings/Index')
          ->has('bookings', 1)
          ->where('bookings.0.title', $event1->title)
          ->missing('bookings.1')
      );
  }

  public function test_booker_can_book_an_booking()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::tomorrow()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(8, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertRedirect()
      ->assertSessionHas([
        'alert_type' => 'success',
        'alert_message' => "Slot booked successfully!"
      ]);
    $this->assertDatabaseHas(Booking::class, $payload);
  }

  public function test_notifications_being_sent_after_booking_is_stored()
  {
    Notification::fake();

    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::tomorrow()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(8, 00)->format('H:i'),
    ];
    $this->post(route('bookings.store', $event), $payload);

    Notification::assertSentTo($event->user, BookingCreatedNotification::class);
    Notification::assertSentOnDemand(
      BookingCreatedNotification::class,
      function ($notification, $channels, $notifiable) use ($payload) {
        return $notifiable->routes['mail'] === [
          $payload['booker_email'] => $payload['booker_name'],
        ];
      }
    );
  }

  public function test_store_requires_booked_at_date_after_or_equal_to_event_available_from_date()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::tomorrow()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->addDay()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::today()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(8, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertSessionHasErrors('booked_at_date');
  }

  public function test_store_requires_booked_at_date_after_or_equal_to_today_date()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::yesterday()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->addDay()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::yesterday()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(8, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertSessionHasErrors('booked_at_date');
  }

  public function test_store_requires_booked_at_date_before_or_equal_to_event_available_to_date()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::tomorrow()->addDay()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(8, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertSessionHasErrors('booked_at_date');
  }

  public function test_store_requires_booked_at_time_after_or_equal_to_event_available_from_time()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::tomorrow()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(5, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertSessionHasErrors('booked_at_time');
  }

  public function test_store_requires_booked_at_time_before_or_equal_to_event_available_to_time()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(10, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::tomorrow()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(11, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertSessionHasErrors('booked_at_time');
  }

  public function test_booker_can_not_book_already_booked_slot()
  {
    $this->app['auth']->logout();
    $event = Event::factory()->create([
      'available_from_date' => Carbon::today()->toDateString(),
      'available_to_date' => Carbon::tomorrow()->toDateString(),
      'available_from_time' => Carbon::now()->setTime(6, 00)->format('H:i'),
      'available_to_time' => Carbon::now()->setTime(7, 00)->format('H:i'),
      'user_id' => $this->user,
    ]);
    $booking = Booking::factory()->create([
      'booked_at_date' => Carbon::tomorrow()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(7, 00)->format('H:i'),
      'event_id' => $event
    ]);
    $payload = [
      'booker_name' => $this->faker->name(),
      'booker_email' => $this->faker->email(),
      'booked_at_date' => Carbon::tomorrow()->toDateString(),
      'booked_at_time' => Carbon::now()->setTime(7, 00)->format('H:i'),
    ];

    $this->post(route('bookings.store', $event), $payload)
      ->assertSessionHasErrors('booked_at_time');
  }

  public function test_user_can_decline_booking()
  {
    $event = Event::factory()->create(['user_id' => $this->user]);
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    $this->delete(route('bookings.destroy', $booking))
      ->assertRedirect()
      ->assertSessionHas(['alert_type' => 'success']);
    $this->assertDatabaseMissing('bookings', ['id', $booking->id]);
  }

  public function test_notification_gets_sent_to_booker_after_decline()
  {
    Notification::fake();

    $event = Event::factory()->create(['user_id' => $this->user]);
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    $this->delete(route('bookings.destroy', $booking));

    Notification::assertSentOnDemand(
      BookingDeclinedNotification::class,
      function ($notification, $channels, $notifiable) use ($booking) {
        return $notifiable->routes['mail'] === [
          $booking->booker_email => $booking->booker_name,
        ];
      }
    );
  }

  public function test_only_event_creator_can_decline_booking()
  {
    $otherUser = User::factory()->create(['id' => 2]);
    $event = Event::factory()->create(['user_id' => $otherUser]);
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    $this->delete(route('bookings.destroy', $booking))->assertForbidden();
  }
}
