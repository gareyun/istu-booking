<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class BookingAdminController extends Controller
{
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
            $booking->google_event_id = $eventId;

        } elseif ($action == 'rejected') {
            $booking->status = 'rejected';
        }

        $booking->admin_comment = $request->input('admin_comment');

        $booking->save();

        return back()->with('success', 'Статус заявки обновлён');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show(string $id)
    {

    }

    public function edit(string $id)
    {

    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
