<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class BookingReminderNotification extends Notification implements ShouldQueue
{
  use Queueable;


  protected Booking $booking;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Booking $booking)
  {
    $this->booking = $booking;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    if ($notifiable instanceof AnonymousNotifiable) {
      return (new MailMessage)
        ->subject("You have a booking or {$this->booking->event->user->name} on event {$this->booking->event->title} after an hour!")
        ->greeting("Hello {$this->booking->booker_name}")
        ->line("Your booking for {$this->booking->event->user->name} is after an hour from now.")
        ->line("The booking is scheduled for {$this->booking->booked_at_date}@{$this->booking->booked_at_time}.")
        ->line("Click the following button to view the link in your calendar")
        ->action("View event", $this->booking->calendar_link)
        ->line('Thank you for using our application!');
    }

    return (new MailMessage)
      ->subject("You are meeting with {$this->booking->booker_name} after an hour!")
      ->greeting("Hello {$notifiable->name}")
      ->line("You are meeting with {$this->booking->booker_name} on event {$this->booking->event->title} after an hour from now.")
      ->line("The booking is scheduled for {$this->booking->booked_at_date}@{$this->booking->booked_at_time}.")
      ->line("Click the following button to view the link in your calendar.")
      ->action("View event", $this->booking->calendar_link)
      ->line('Thank you for using our application!');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
