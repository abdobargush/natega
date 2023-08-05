<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BookingDeclinedNotification extends Notification implements ShouldQueue
{
  use Queueable;

  protected $event;
  protected $booker_name;
  protected $booked_at_date;
  protected $booked_at_time;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Event $event, string $booker_name, string $booked_at_date, string $booked_at_time)
  {
    $this->event = $event;
    $this->booker_name = $booker_name;
    $this->booked_at_date = $booked_at_date;
    $this->booked_at_time = $booked_at_time;
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
    return (new MailMessage)
      ->subject('Your booking was cancelled!')
      ->greeting("Hello {$this->booker_name}")
      ->line("Your booking with {$this->event->user->name} regarding {$this->event->title} was canceled.")
      ->line("It was scheduled for {$this->booked_at_date}@{$this->booked_at_time}.")
      ->line("You can try booking a new timeslot or contact {$this->event->user->name} for more details.")
      ->line('Thank you for using our application!');
  }
}
