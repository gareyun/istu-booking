<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Classroom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\BookingWithdrawalService;

use Livewire\Attributes\Layout;
#[Layout('components.layout')]

class BookingForm extends Component
{
    private const STUDENT_MAX_DAYS_AHEAD = 30;
    public $maxDate = null;

    public $classrooms = [];
    public $bookings = [];
    public $selectedClassroom = null;

    public $classroom_id;
    public $date;
    public $start_time;
    public $end_time;
    public $purpose;
    public $equipment;
    public $description;
    public $is_tech_support = 0;
    public $user_comment;

    public $busySlots = [];

    public $filterStatus = '';
    public $filterDate = '';
    public $filterClassroom = '';

    public $showSettingsModal = false;
    public $settingsPhone = '';
    public $userVkLink = '';

    public $submitted = false;

    public $perPage = 5;
    public $hasMoreBookings = false;

    public $loadError = false;
    public $bookingsSuccess = null;
    public $bookingsError = null;

    public $showWithdrawModal = false;
    public $withdrawBookingId = null;

    public function mount()
    {
        $user = Auth::user();
        $this->classrooms = Classroom::with('category')->orderBy('room')->get();

        $this->userVkLink = $this->normalizeVkLink($user->vk_link);
        $this->settingsPhone = $user->phone ?? '';

        $this->maxDate = $this->hasUnlimitedBooking($user)
            ? null
            : now()->addDays(self::STUDENT_MAX_DAYS_AHEAD)->format('d.m.Y');

        $this->loadBookings();
    }

    public function loadBookings()
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $query = $user
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
                ->take($this->perPage)
                ->get();

            $this->hasMoreBookings = $query->count() > $this->perPage;

            $this->loadError = false;
        } catch (\Exception $e) {

            \Illuminate\Support\Facades\Log::error(
                'Booking load error: ' . $e->getMessage()
            );

            $this->bookings = [];
            $this->hasMoreBookings = false;
            $this->loadError = true;
        }
    }

    public function loadMore()
    {
        $this->perPage += 5;
        $this->loadBookings();
    }

    public function updatedClassroomId()
    {
        $this->loadSelectedClassroom();
        $this->loadBusySlots();
    }

    public function loadSelectedClassroom(): void
    {
        $classroom = $this->classroom_id
            ? Classroom::bookable()->with('category')->find($this->classroom_id)
            : null;

        if (!$classroom) {
            if ($this->classroom_id) {
                $this->classroom_id = null;
                $this->addError('classroom_id', 'Это пространство временно недоступно для бронирования.');
            }

            $this->selectedClassroom = null;
            return;
        }

        $this->selectedClassroom = [
            'id'            => $classroom->id,
            'room'          => $classroom->room,
            'category'      => $classroom->category?->category,
            'capacity'      => $classroom->capacity,
            'equipment'     => $classroom->equipment,
            'description'   => $classroom->description,
            'photo'         => $classroom->photo
                ? (str_starts_with($classroom->photo, 'http')
                    ? $classroom->photo
                    : asset('storage/' . $classroom->photo))
                : null,
        ];
    }

    public function updatedDate()
    {
        $this->loadBusySlots();
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
            ->orderBy('start_time', 'asc')
            ->get(['start_time', 'end_time', 'status'])
            ->toArray();
    }

    public function openSettingsModal()
    {
        $this->showSettingsModal = true;
    }

    public function closeSettingsModal()
    {
        $this->showSettingsModal = false;
    }

    public function openWithdrawModal($bookingId)
    {
        $this->bookingsSuccess = null;
        $this->bookingsError = null;
        $this->withdrawBookingId = $bookingId;
        $this->showWithdrawModal = true;
    }

    public function closeWithdrawModal()
    {
        $this->showWithdrawModal = false;
        $this->withdrawBookingId = null;
    }

    public function withdrawBooking()
    {
        $this->bookingsSuccess = null;
        $this->bookingsError = null;

        $booking = Booking::with(['classroom', 'user'])
            ->where('user_id', Auth::id())
            ->find($this->withdrawBookingId);

        if (!$booking) {
            $this->bookingsError = 'Заявка не найдена.';
        } else {
            try {
                app(BookingWithdrawalService::class)->withdraw($booking, Auth::user());
                $this->bookingsSuccess = 'Заявка отозвана.';
            } catch (\DomainException $e) {
                $this->bookingsError = $e->getMessage();
            }
        }

        $this->closeWithdrawModal();
        $this->loadBookings();
    }

    protected function hasUnlimitedBooking(User $user): bool
    {
        return in_array($user->role, ['admin', 'tech_support'], true);
    }

    protected function normalizeVkLink(?string $link): string
    {
        $link = trim((string) $link);

        if ($link === '') {
            return '';
        }

        if (str_starts_with($link, '@')) {
            return 'https://vk.com/' . ltrim($link, '@');
        }

        if (!preg_match('~^https?://~i', $link)) {
            return 'https://' . ltrim($link, '/');
        }

        return $link;
    }

    public function saveSettings()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'settingsPhone'  => 'required|string|max:20'
        ]);

        $user->update(['phone' => $this->settingsPhone]);

        session()->flash('success', 'Настройки сохранены.');

        $this->closeSettingsModal();
    }

    public function submit()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$user->phone) {
            $this->addError('phone', 'Для отправки заявки необходимо указать номер телефона в настройках.');
            $this->openSettingsModal();
            return;
        }

        if ($this->classroom_id && !Classroom::bookable()->whereKey($this->classroom_id)->exists()) {
            $this->reset(['classroom_id', 'selectedClassroom']);
            $this->busySlots = [];
            $this->addError('classroom_id', 'Это пространство временно недоступно для бронирования. Выберите другое.');
            return;
        }

        $validated = $this->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:255',
            'equipment' => 'nullable|string',
            'description' => 'nullable|string',
            'is_tech_support' => 'required|boolean',
            'user_comment' => 'nullable|string'
        ]);

        $vkLink = $user->vk_link;

        try {
            $bookingStart = Carbon::createFromFormat(
                'd.m.Y H:i',
                trim($validated['date']) . ' ' . trim($validated['start_time'])
            );
        } catch (\Exception $e) {
            $this->addError('date', 'Некорректный формат даты или времени.');
            return;
        }

        if ($bookingStart->lessThanOrEqualTo(now()->addHours(24))) {
            $this->addError('date', 'Забронировать пространство можно не позднее чем за 24 часа до начала мероприятия.');
            return;
        }

        if (
            !$this->hasUnlimitedBooking($user)
            && $bookingStart->startOfDay()->greaterThan(now()->addDays(self::STUDENT_MAX_DAYS_AHEAD)->startOfDay())
        ) {
            $this->addError(
                'date',
                'Забронировать аудиторию можно не более чем на ' . self::STUDENT_MAX_DAYS_AHEAD . ' дней вперёд от даты подачи заявки.'
            );
            return;
        }

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

        $booking = Booking::create([
            'user_id' => $user->id,
            ...$validated,
            'vk_link' => $vkLink
        ]);

        $booking->load('classroom');

        try {
            app(\App\Services\VkNotificationService::class)->notifyBookingCreated($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error(
                'VK notification create booking error: ' . $e->getMessage()
            );
        }

        session()->flash('success', 'Заявка успешно создана');
        $this->submitted = true; 

        $this->reset([
            'classroom_id',
            'date',
            'start_time',
            'end_time',
            'purpose',
            'equipment',
            'description',
            'user_comment'
        ]);
        $this->is_tech_support = 0;
        $this->selectedClassroom = null;

        $this->loadBookings();
        $this->loadBusySlots();
    }

    public function resetForm()
    {
        $this->submitted = false;

        $this->reset([
            'classroom_id',
            'date',
            'start_time',
            'end_time',
            'purpose',
            'equipment',
            'description',
            'user_comment',
        ]);

        $this->is_tech_support = 0;
        $this->busySlots = [];
        $this->selectedClassroom = null;

        $this->dispatch('booking-form-reset');
    }
}