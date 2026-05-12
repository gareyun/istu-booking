<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Classroom;

class TechSupportPanel extends Component
{
    public $selectedDate = '';
    public $selectedClassroom = '';

    public function getBookingsProperty()
    {
        return Booking::with(['classroom', 'user'])
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->where('is_tech_support', true)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('equipment')
                            ->where('equipment', '!=', '');
                    });
            })
            ->when($this->selectedDate, function ($query) {
                $query->where('date', $this->selectedDate);
            })
            ->when($this->selectedClassroom, function ($query) {
                $query->where('classroom_id', $this->selectedClassroom);
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    public function resetFilters()
    {
        $this->reset(['selectedDate', 'selectedClassroom']);
        $this->dispatch('resetFilterDate');
    }

    public function render()
    {
        return view('livewire.tech-support-panel', [
            'bookings' => $this->bookings,
            'classrooms' => Classroom::orderBy('room')->get(),
        ]);
    }
}