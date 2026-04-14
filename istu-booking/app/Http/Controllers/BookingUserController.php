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

    public function create()
    {

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
