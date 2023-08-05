<?php

namespace App\Http\Controllers;

use Str;
use Exception;
use Notification;
use Carbon\Carbon;
use Google\Client;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\BookingDeclinedNotification;

class BookingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /** @var \App\Models\User */
    $user = auth()->user();
    $bookings = $user->events()
      ->with('bookings')
      ->having('bookings_count', '>', 0)
      ->groupBy('id') // specifically for sqlite to be able to use having
      ->latest()->get();

    return Inertia::render('Bookings/Index', compact('bookings'));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreBookingRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreBookingRequest $request, Event $event)
  {
    // ignore for testing as it interacts with google api
    if (!app()->runningUnitTests()) {
      try {
        $calendarEvent = $this->createGoogleEvent(
          $event,
          $request->validated('booked_at_date'),
          $request->validated('booked_at_time'),
          $request->validated('booker_name'),
          $request->validated('booker_email'),
        );
      } catch (Exception $ex) {
        dd($ex);

        return redirect()->back()->with([
          'alert_type' => 'error',
          'alert_message' => "Something happened please try again!"
        ]);
      }
    }

    $booking = $event->bookings()->create([
      ...$request->validated(),
      ...$calendarEvent ?? []
    ]);

    // Notify uesr
    $event->user->notify(new BookingCreatedNotification($booking));

    // Notify the booker
    Notification::route('mail', [
      $booking->booker_email => $booking->booker_name,
    ])->notify(new BookingCreatedNotification($booking));

    return redirect()->back()->with([
      'alert_type' => 'success',
      'alert_message' => "Slot booked successfully!"
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Booking  $booking
   * @return \Illuminate\Http\Response
   */
  public function destroy(Booking $booking)
  {
    $this->authorize('delete', $booking);

    // ignore in testing as it interacts with google calendar api
    if (!app()->runningUnitTests()) {
      try {
        $this->deleteGoogleEvent($booking->event, $booking);
      } catch (Exception $ex) {
        dd($ex);

        return redirect()->back()->with([
          'alert_type' => 'error',
          'alert_message' => "Something happened please try again!"
        ]);
      }
    }

    if ($booking->delete()) {
      // Queue notification
      Notification::route('mail', [
        $booking->booker_email => $booking->booker_name,
      ])->notify(new BookingDeclinedNotification(
        $booking->event,
        $booking->booker_name,
        $booking->booked_at_date,
        $booking->booked_at_time
      ));

      return redirect()->back()->with([
        'alert_type' => 'success',
        'alert_message' => "Booking declined!"
      ]);
    };
  }


  /**
   * Add event to google calendar
   *
   * @param Event $event
   * @param string $booked_date
   * @param string $booked_time
   * @param string $booker_name
   * @param string $booker_email
   * @return array
   */
  protected function createGoogleEvent(Event $event, $booked_date, $booked_time, $booker_name, $booker_email)
  {
    $client = new Client();

    if (Carbon::now()->greaterThan($event->user->google_auth_metadata['token_expiry'])) {
      $client->setClientId(config('services.google.client_id'));
      $client->setClientSecret(config('services.google.client_secret'));
      $newToken = $client->fetchAccessTokenWithRefreshToken($event->user->google_auth_metadata['refresh_token']);
      $event->user->setGoogleAuthMetadata(null, $newToken['access_token'], $newToken['refresh_token'], $newToken['expires_in']);
    }

    $client->setAccessToken($event->user->google_auth_metadata['token']);
    $service = new \Google\Service\Calendar($client);

    $parsed_booked_time = Carbon::parse($booked_time);
    $calendarEvent = new \Google\Service\Calendar\Event(array(
      'summary' => $event->title,
      'location' => 'Google Meet',
      'start' => array(
        'dateTime' => Carbon::parse($booked_date)
          ->setTimeFrom($parsed_booked_time),
      ),
      'end' => array(
        'dateTime' => Carbon::parse($booked_date)
          ->setTimeFrom($parsed_booked_time)
          ->addMinutes($event->duration),
      ),
      'attendees' => [
        [
          'email' => $booker_email,
          'displayName' => $booker_name
        ],
        [
          'email' => $event->user->email,
          'displayName' => $event->user->name
        ]
      ],
      'reminders' => array(
        'useDefault' => FALSE,
        'overrides' => array(
          array('method' => 'email', 'minutes' => 60),
          array('method' => 'popup', 'minutes' => 10),
        ),
      ),
      'conferenceData' => [
        'createRequest' => [
          'conferenceSolutionKey' => [
            'type' => 'hangoutsMeet'
          ],
          'requestId' => Str::random(),
        ],
      ]
    ));

    $calendarId = 'primary';
    $calendarEvent = $service->events->insert($calendarId, $calendarEvent, [
      "conferenceDataVersion" => 1,
      'sendUpdates' => "all"
    ]);

    return [
      'calendar_id' => $calendarEvent->id,
      'calendar_link' => $calendarEvent->htmlLink,
      'meet_link' => $calendarEvent->hangoutLink
    ];
  }


  /**
   * Delete event from google calendar
   *
   * @param Event $event
   * @param Booking $booking
   * @return boolean
   */
  protected function deleteGoogleEvent(Event $event, Booking $booking)
  {
    $client = new Client();

    if (Carbon::now()->greaterThan($event->user->google_auth_metadata['token_expiry'])) {
      $client->setClientId(config('services.google.client_id'));
      $client->setClientSecret(config('services.google.client_secret'));
      $newToken = $client->fetchAccessTokenWithRefreshToken($event->user->google_auth_metadata['refresh_token']);
      $event->user->setGoogleAuthMetadata(null, $newToken['access_token'], $newToken['refresh_token'], $newToken['expires_in']);
    }

    $client->setAccessToken($event->user->google_auth_metadata['token']);
    $service = new \Google\Service\Calendar($client);
    return $service->events->delete('primary', $booking->calendar_id);
  }
}
