<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Classroom;
use App\Services\GoogleCalendarService;

class BookingAdminPanel extends Component
{
    public $status = '';
    public $selectedDate = '';
    public $selectedClassroom = '';

    public $adminComments = [];

    public function setStatus($status = '')
    {
        $this->status = $status;
    }

    public function updateStatus($bookingId, $action)
    {
        $booking = Booking::with('classroom')->findOrFail($bookingId);

        if ($action === 'approved') {
            $booking->status = 'approved';

            $eventId = app(GoogleCalendarService::class)->createEvent($booking);

            $booking->google_event_id = $eventId;

        } elseif ($action === 'rejected') {
            $booking->status = 'rejected';
        }

        $booking->admin_comment = $this->adminComments[$bookingId] ?? null;

        $booking->save();

        session()->flash(
            'success',
            'Статус заявки обновлён'
        );
    }

    public function getBookingsProperty()
    {
        return Booking::with(['classroom', 'user'])

            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })

            ->when($this->selectedDate, function ($query) {
                $query->where('date', $this->selectedDate);
            })

            ->when($this->selectedClassroom, function ($query) {
                $query->where('classroom_id', $this->selectedClassroom);
            })

            ->orderByDesc('id')
            ->get();
    }

    public function resetFilters()
    {
        $this->status = '';
        $this->selectedDate = '';
        $this->selectedClassroom = '';

        $this->dispatch('resetFilterDate');
    }

    public function render()
    {
        return view('livewire.admin.booking-admin-panel', [
            'bookings' => $this->bookings,
            'classrooms' => Classroom::orderBy('room')->get(),
        ]);
    }
}