<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;

class EventController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /** @var mixed */
    $user = auth()->user();
    $events = $user->events()->latest()->get();

    return Inertia::render('Events/Index', compact('events'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return Inertia::render('Events/Create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreEventRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreEventRequest $request)
  {
    /** @var mixed */
    $user = auth()->user();
    $user->events()->create($request->validated());

    return redirect()->route('events.index')->with([
      'alert_type' => 'success',
      'alert_message' => "Event created successfully!"
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  // public function show(Event $event)
  // {
  //   $event->load('user');

  //   return Inertia::render('Events/Show', compact('event'));
  // }

  /**
   * Display the specified resource for piublic.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function showPublic(Event $event)
  {
    $event->load('user')->append('timeslots');

    return Inertia::render('Events/ShowPublic', compact('event'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function edit(Event $event)
  {
    $this->authorize('update', $event);

    return Inertia::render('Events/Edit', compact('event'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateEventRequest  $request
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateEventRequest $request, Event $event)
  {
    $this->authorize('update', $event);

    $event->update($request->validated());

    return back()->with([
      'alert_type' => 'success',
      'alert_message' => "Event updated successfully!"
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function destroy(Event $event)
  {
    $this->authorize('delete', $event);

    if ($event->delete()) {
      return back()->with([
        'alert_type' => 'success',
        'alert_message' => "Event deleted!"
      ]);
    }
  }
}
