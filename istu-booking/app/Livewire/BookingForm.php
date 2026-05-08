<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Classroom;
use App\Models\User;

class BookingForm extends Component
{
    public $classrooms = [];
    public $bookings = [];

    public $classroom_id;
    public $date;
    public $start_time;
    public $end_time;
    public $purpose;
    public $equipment;
    public $is_tech_support = 0;
    public $user_comment;

    public $busySlots = [];

    public $filterStatus = '';
    public $filterDate = '';
    public $filterClassroom = '';

    public function mount()
    {
        $this->classrooms = Classroom::all();

        $this->loadBookings();
    }

    public function loadBookings()
    {
        $query = User::find(1)
            ->bookings()
            ->with('classroom');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDate) {
            $query->where('date', $this->filterDate);
        }

        if ($this->filterClassroom) {
            $query->where('classroom_id', $this->filterClassroom);
        }

        $this->bookings = $query
            ->latest()
            ->get();
    }

    public function updatedFilterStatus()
    {
        $this->loadBookings();
    }

    public function updatedFilterDate()
    {
        $this->loadBookings();
    }

    public function updatedFilterClassroom()
    {
        $this->loadBookings();
    }

    public function resetFilters()
    {
        $this->reset([
            'filterStatus',
            'filterDate',
            'filterClassroom'
        ]);

        $this->loadBookings();

        $this->dispatch('resetFilterDate');
    }

    public function loadBusySlots()
    {
        if (!$this->classroom_id || !$this->date) {
            $this->busySlots = [];
            return;
        }

        $this->busySlots = Booking::where('classroom_id', $this->classroom_id)
            ->where('date', $this->date)
            ->whereIn('status', ['pending', 'approved'])
            ->get(['start_time', 'end_time', 'status'])
            ->toArray();
    }

    public function submit()
    {
        $validated = $this->validate([
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
            $this->addError('busy', 'Выбранное время уже занято');
            return;
        }

        Booking::create([
            'user_id' => 1,
            ...$validated
        ]);

        session()->flash('success', 'Заявка успешно создана');

        $this->reset([
            'classroom_id',
            'date',
            'start_time',
            'end_time',
            'purpose',
            'equipment',
            'user_comment',
        ]);

        $this->is_tech_support = 0;

        $this->loadBookings();
        $this->loadBusySlots();
    }

    public function render()
    {
        return view('livewire.booking-form');
    }
}