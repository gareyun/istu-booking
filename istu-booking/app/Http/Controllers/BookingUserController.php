<?php

namespace App\Http\Controllers;

use \App\Models\Classroom;
use \App\Models\Booking;
use \App\Models\User;
use Illuminate\Http\Request;

class BookingUserController extends Controller
{

    public function index()
    {
        $classrooms = Classroom::get();
        $bookings = User::find(1)
            ->bookings()
            ->with('classroom')
            ->latest()
            ->get();

        return view('booking', [
            'classrooms' => $classrooms,
            'bookings' => $bookings
        ]);
    }

    public function getBusySlots(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date' => 'required',
        ]);

        $bookings = Booking::where('classroom_id', $request->classroom_id)
            ->where('date', $request->date)
            ->whereIn('status', ['pending', 'approved'])
            ->get(['start_time', 'end_time', 'status']);

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'classroom_id' => 'required|exists:classrooms,id',
                'date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'purpose' => 'required|string|max:255',
                'equipment' => 'nullable|string',
                'is_tech_support' => 'required|boolean',
                'user_comment' => 'nullable|string',
            ]);

            $exists = Booking::where('classroom_id', $validated['classroom_id'])
                ->where('date', $validated['date'])
                ->where(function ($query) use ($validated) {
                    $query->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                })
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Это время уже занято другой заявкой'
                ], 409);
            }

            $booking = Booking::create([
                'user_id' => 1,
                ...$validated
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Заявка успешно создана'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера'
            ], 500);
        }
    }
}
