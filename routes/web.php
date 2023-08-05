<?php

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Middleware\LinkedWithGoogleMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/events')->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  Route::resource('/events', EventController::class)->middleware(LinkedWithGoogleMiddleware::class)->except(['show']);
  Route::resource('/bookings', BookingController::class)->middleware(LinkedWithGoogleMiddleware::class)->only(['index', 'destroy']);

  Route::inertia('/auth/google', 'Auth/GoogleAuth')->name('google.auth');
  Route::get('/auth/google/redirect', [GoogleAuthController::class, 'googleAuthRedirect'])->name('google.redirect');
  Route::get('/auth/google/callback', [GoogleAuthController::class, 'googleAuthCallback'])->name('google.callback');
});

Route::get('/e/{event:slug}', [EventController::class, 'showPublic'])->name('events.show.public');
Route::post('/e/{event:slug}/book', [BookingController::class, 'store'])->name('bookings.store');

require __DIR__ . '/auth.php';
