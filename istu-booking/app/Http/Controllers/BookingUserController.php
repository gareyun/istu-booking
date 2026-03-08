<?php

namespace App\Http\Controllers;

use \App\Models\Classroom;
use \App\Models\Booking;
use Illuminate\Http\Request;

class BookingUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::get();
        return view('booking', ['classrooms' => $classrooms]);
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
        //echo $request;

        Booking::create([
            'user_id' => 1,
            'classroom_id' => 1,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'purpose' => $request->purpose,
            'equipment' => $request->equipment,
            'is_tech_support' => $request->is_tech_support,
            'comment' => $request->comment
        ]);

       return redirect('/booking')->with('success', 'Booking created!');
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
