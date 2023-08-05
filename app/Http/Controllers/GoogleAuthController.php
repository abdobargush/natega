<?php

namespace App\Http\Controllers;

use Exception;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
  /**
   * Google auth redirect
   *
   * @return void
   */
  public function googleAuthRedirect()
  {
    return Socialite::driver('google')
      ->scopes([
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/calendar.events'
      ])
      ->with(["access_type" => "offline", "prompt" => "consent select_account"])
      ->redirect();
  }


  /**
   * Google auth callback
   *
   * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
   */
  public function googleAuthCallback()
  {
    try {
      $socialUser = Socialite::driver('google')->user();
    } catch (Exception $ex) {
      if (config('app.debug')) {
        dd($ex);
      }

      return redirect()->route('google.auth')->with([
        'alert_type' => 'error',
        'alert_message' => 'Unexpected error, please try again!'
      ]);
    }

    /** @var \App\Models\User */
    $user = auth()->user();
    $user->setGoogleAuthMetadata(
      $socialUser->getId(),
      $socialUser->token,
      $socialUser->refreshToken,
      $socialUser->expiresIn,
    );

    return redirect()->route('events.index')->with([
      'alert_type' => 'success',
      'alert_message' => 'Google linked successfully!'
    ]);
  }
}
