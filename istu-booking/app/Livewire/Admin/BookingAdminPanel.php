<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Classroom;
use App\Services\GoogleCalendarService;
use App\Services\VkNotificationService;
use Carbon\Carbon;

class BookingAdminPanel extends Component
{
    public $status = '';
    public $selectedDate = '';
    public $selectedClassroom = '';
    public $adminComments = [];

    public $showCancelModal = false;
    public $bookingToCancel = null;

    public function setStatus($status = '')
    {
        $this->status = $status;
    }

    public function updateStatus($bookingId, $action)
    {
        $booking = Booking::with('classroom')->findOrFail($bookingId);

        if ($action === 'approved') {
            $booking->status = 'approved';

            try {
                $eventId = app(GoogleCalendarService::class)->createEvent($booking);
                $booking->google_event_id = $eventId;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Google Calendar error: ' . $e->getMessage());
            }
        } elseif ($action === 'rejected') {
            $booking->status = 'rejected';
        }

        $booking->admin_comment = $this->adminComments[$bookingId] ?? null;
        $booking->save();

        try {
            app(VkNotificationService::class)->notifyStatusChange($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VK notification error: ' . $e->getMessage());
        }

        session()->flash('success', 'Статус заявки обновлён');
    }

    public function openCancelModal($bookingId)
    {
        $this->bookingToCancel = $bookingId;
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->bookingToCancel = null;
    }

    public function cancelBooking()
    {
        if (!$this->bookingToCancel) {
            return;
        }

        $booking = Booking::with('classroom')->findOrFail($this->bookingToCancel);

        if ($booking->status !== 'approved') {
            session()->flash('error', 'Можно отменить только одобренную бронь.');
            $this->closeCancelModal();
            return;
        }

        try {
            $startDateTime = Carbon::createFromFormat(
                'd.m.Y H:i',
                $booking->date . ' ' . $booking->start_time
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Некорректный формат даты/времени.');
            $this->closeCancelModal();
            return;
        }

        if ($startDateTime->lessThan(now()->addHours(24))) {
            session()->flash('error', 'Отмена возможна не позднее чем за 24 часа до начала.');
            $this->closeCancelModal();
            return;
        }

        $booking->status = 'cancelled';
        $booking->save();

        try {
            app(VkNotificationService::class)->notifyStatusChange($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VK notification error: ' . $e->getMessage());
        }

        session()->flash('success', 'Бронь отменена.');
        $this->closeCancelModal();
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