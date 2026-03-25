<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class BookingAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['classroom', 'user'])->orderBy('id', 'desc')->get();
        return view('admin', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $action = $request->input('action');

        if ($action == 'approved') {
            $booking->status = 'approved';

            $eventId = app(GoogleCalendarService::class)->createEvent($booking);

            #$booking->google_event_id = $eventId;

        } elseif ($action == 'rejected') {
            $booking->status = 'rejected';
        }

        $booking->save();

        return back()->with('success', 'Статус заявки обновлён');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
