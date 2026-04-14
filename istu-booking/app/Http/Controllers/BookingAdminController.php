<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class BookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Booking::with(['classroom', 'user'])->orderBy('id', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        return view('admin', compact('bookings', 'status'));
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
        $validated = $request->validate([
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
        ]);

        Classroom::create($validated);

        return back()->with('success', 'Аудитория успешно добавлена');
    }

    public function classrooms()
    {
        $classrooms = Classroom::orderBy('id', 'desc')->get();
        return view('admin-classrooms', compact('classrooms'));
    }

    public function updateClassroom(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
        ]);

        $classroom->update($validated);

        return back()->with('success', 'Аудитория обновлена');
    }

    public function destroyClassroom(Classroom $classroom)
    {
        if ($classroom->bookings()->exists()) {
            return back()->with('error', 'Нельзя удалить аудиторию, есть связанные заявки');
        }

        $classroom->delete();

        return back()->with('success', 'Аудитория удалена');
    }

    public function show(string $id)
    {

    }

    public function edit(string $id)
    {

    }
}
