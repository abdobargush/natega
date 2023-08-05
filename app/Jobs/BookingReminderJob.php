<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Notifications\BookingReminderNotification;

class BookingReminderJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $bookings = Booking::where('booked_at_date', Carbon::today()->toDateString())
      ->whereBetween(
        'booked_at_time',
        Carbon::now()->addHour()->format('H:i'),
        Carbon::now()->addHours(2)->format('H:i')
      );

    foreach ($bookings as $booking) {
      // notify booker
      Notification::route('mail', [
        $booking->booker_email => $booking->booker_name,
      ])->notify(new BookingReminderNotification($booking));

      // notify event creator
      $booking->event->user->notify(new BookingReminderNotification($booking));
    }
  }
}
