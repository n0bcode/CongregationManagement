<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeriodicEventController extends Controller
{
    public function index()
    {
        $events = \App\Models\PeriodicEvent::with('community')
            ->orderBy('start_date', 'asc')
            ->paginate(10);
        return view('periodic-events.index', compact('events'));
    }

    public function create()
    {
        $communities = \App\Models\Community::orderBy('name')->get();
        return view('periodic-events.create', compact('communities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recurrence' => 'required|in:annual,monthly,one-time',
            'community_id' => 'nullable|exists:communities,id',
        ]);

        \App\Models\PeriodicEvent::create($validated);

        return redirect()->route('periodic-events.index')->with('success', 'Event created successfully.');
    }

    public function show(\App\Models\PeriodicEvent $periodicEvent)
    {
        return view('periodic-events.show', compact('periodicEvent'));
    }

    public function edit(\App\Models\PeriodicEvent $periodicEvent)
    {
        $communities = \App\Models\Community::orderBy('name')->get();
        return view('periodic-events.edit', compact('periodicEvent', 'communities'));
    }

    public function update(Request $request, \App\Models\PeriodicEvent $periodicEvent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recurrence' => 'required|in:annual,monthly,one-time',
            'community_id' => 'nullable|exists:communities,id',
        ]);

        $periodicEvent->update($validated);

        return redirect()->route('periodic-events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(\App\Models\PeriodicEvent $periodicEvent)
    {
        $periodicEvent->delete();
        return redirect()->route('periodic-events.index')->with('success', 'Event deleted successfully.');
    }
}
