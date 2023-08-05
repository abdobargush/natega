<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      'booker_name' => 'required|string',
      'booker_email' => 'required|email',
      'booked_at_date' => 'required|date|after_or_equal:today|after_or_equal:' . request('event')->available_from_date . '|before_or_equal:' . request('event')->available_to_date,
      'booked_at_time' => [
        'required',
        'date_format:H:i',
        'after_or_equal:' . request('event')->available_from_time,
        'before_or_equal:' . request('event')->available_to_time,
        // check if timeslot is already booked
        function ($attribute, $value, $fail) {
          if (request('event')->bookings()->where('booked_at_date', request('booked_at_date'))->where('booked_at_time', $value)->exists()) {
            $fail('This timeslot is already booked try another one.');
          }
        },
      ],
    ];
  }
}
