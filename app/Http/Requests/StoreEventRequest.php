<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      'title' => 'required|string',
      'description' => 'nullable|string',
      'duration' => 'required|in:15,30,45,60',
      'slug' => 'required|alpha_dash|unique:events',
      'color' => 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i',
      'available_from_date' => 'required|date|after_or_equal:today',
      'available_to_date' => 'required|date|after_or_equal:today|after_or_equal:available_from_date',
      'available_from_time' => 'required|date_format:H:i',
      'available_to_time' => 'required|date_format:H:i|after_or_equal:available_from_time',
    ];
  }
}
