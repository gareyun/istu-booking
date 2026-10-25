<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Classroom;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use App\Services\BookingWithdrawalService;

class VkDialogService
{
    protected VkBotService $vk;
    protected VkRegistrationService $registration;

    public function __construct(VkBotService $vk, VkRegistrationService $registration)
    {
        $this->vk = $vk;
        $this->registration = $registration;
    }
    
    public function handleMessage(int $userId, string $text): void
    {
        $text = trim($text);
        $command = mb_strtolower($text);

        $stateKey = "vk_bot_state_{$userId}";

        if ($command === '/отмена') {
            $hadBooking = Cache::pull($stateKey) !== null;
            $hadRegistration = $this->registration->cancel($userId);

            if ($hadRegistration) {
                $this->vk->sendMessage(
                    $userId,
                    "🚫 Регистрация отменена. Чтобы начать заново, отправьте /бронь."
                );
            } elseif ($hadBooking) {
                $this->vk->sendMessage($userId, "🚫 Процесс бронирования отменён.");
            } else {
                $this->vk->sendMessage($userId, "ℹ️ У вас нет активного процесса.");
            }

            return;
        }

        $user = $this->findUser($userId);

        if (!$user) {
            if (
                in_array($command, VkRegistrationService::START_COMMANDS, true)
                || $this->registration->hasActiveRegistration($userId)
            ) {
                $this->registration->handle($userId, $text);
            }

            return;
        }

        if (!$user->isEmailVerified()) {
            $this->registration->handleUnverified($user, $text);
            return;
        }

        if ($command === '/бронь') {
            Cache::forget($stateKey);
            $this->sendStepClassroom($userId);
            return;
        }

        if ($command === '/отозвать') {
            Cache::forget($stateKey);
            $this->sendStepWithdraw($userId, $user);
            return;
        }

        if (in_array($command, VkRegistrationService::START_COMMANDS, true)) {
            $this->vk->sendMessage(
                $userId,
                "✅ Вы уже зарегистрированы. Чтобы подать заявку на бронирование, отправьте /бронь."
            );
            return;
        }

        $state = Cache::get($stateKey);

        if (!$state) {
            return;
        }

        switch ($state['step']) {
            case 'classroom':
                $this->processClassroom($userId, $text, $state);
                break;
            case 'date':
                $this->processDate($userId, $text, $state);
                break;
            case 'start_time':
                $this->processStartTime($userId, $text, $state);
                break;
            case 'end_time':
                $this->processEndTime($userId, $text, $state);
                break;
            case 'purpose':
                $this->processPurpose($userId, $text, $state);
                break;
            case 'equipment':
                $this->processEquipment($userId, $text, $state);
                break;
            case 'tech_support':
                $this->processTechSupport($userId, $text, $state);
                break;
            case 'comment':
                $this->processComment($userId, $text, $state, $user);
                break;
            case 'confirm':
                $this->processConfirm($userId, $text, $state, $user);
                break;
            case 'withdraw_select':
                $this->processWithdrawSelect($userId, $text, $state, $user);
                break;
            case 'withdraw_confirm':
                $this->processWithdrawConfirm($userId, $text, $state, $user);
                break;
            default:
                Cache::forget($stateKey);
                $this->vk->sendMessage(
                    $userId,
                    "⚠️ Состояние бронирования сброшено. Чтобы начать заново, отправьте /бронь."
                );
                break;
        }
    }

    protected function findUser(int $vkId): ?User
    {
        return User::where('vk_id', (string) $vkId)->first();
    }

    protected function sendStepClassroom(int $userId, bool $error = false): void
    {
        $classrooms = Classroom::bookable()->orderBy('room')->get();
        if ($classrooms->isEmpty()) {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage(
                $userId,
                "ℹ️ Сейчас нет пространств, доступных для бронирования. Попробуйте позже."
            );
            return;
        }

        $buttons = [];
        $row = [];
        foreach ($classrooms as $i => $room) {
            $row[] = ['text' => $room->room];
            if (count($row) == 2 || $i == count($classrooms) - 1) {
                $buttons[] = $row;
                $row = [];
            }
        }

        $buttons[] = [['text' => 'Отмена', 'color' => 'negative']];

        $message = ($error ? "❌ Не удалось распознать пространство.\n" : "📅 ")
            . "Выберите пространство из списка:";

        $this->vk->sendMessageWithKeyboard($userId, $message, $buttons, true);
        Cache::put("vk_bot_state_{$userId}", ['step' => 'classroom'], now()->addMinutes(30));
    }

    protected function processClassroom(int $userId, string $text, array $state): void
    {
        if (mb_strtolower($text) === 'отмена') {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage($userId, "🚫 Бронирование отменено.");
            return;
        }

        $classroom = Classroom::bookable()
            ->where(function ($query) use ($text) {
                $query->where('room', $text)
                    ->orWhere('room', 'like', "%{$text}%");
            })
            ->first();

        if (!$classroom) {
            $this->sendStepClassroom($userId, true);
            return;
        }

        $state['classroom_id'] = $classroom->id;
        $state['classroom_name'] = $classroom->room;
        $state['step'] = 'date';

        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $this->vk->sendMessage($userId, "✅ Пространство: {$classroom->room}\n"
            . "📅 Введите дату бронирования в формате ДД.ММ.ГГГГ (например, 15.05.2026):");
    }

    protected function processDate(int $userId, string $text, array $state): void
    {
        if (mb_strtolower($text) === 'отмена') {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage($userId, "🚫 Бронирование отменено.");
            return;
        }

        try {
            $date = Carbon::createFromFormat('d.m.Y', $text);
            if ($date->lt(Carbon::today())) {
                $this->vk->sendMessage($userId, "❌ Дата не может быть в прошлом. Введите ещё раз (ДД.ММ.ГГГГ):");
                return;
            }
        } catch (\Exception $e) {
            $this->vk->sendMessage($userId, "❌ Неверный формат. Введите дату в формате ДД.ММ.ГГГГ (например, 15.05.2026):");
            return;
        }

        $state['date'] = $text;
        $state['step'] = 'start_time';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $this->sendBusySlots(
            $userId,
            $state['classroom_id'],
            $text
        );

        $this->vk->sendMessage($userId, "⏰ Введите время начала (ЧЧ:ММ, например 14:00):");
    }

    protected function sendBusySlots(int $userId, int $classroomId, string $date): void
    {
        $busySlots = Booking::query()
            ->where('classroom_id', $classroomId)
            ->where('date', $date)
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        if ($busySlots->isEmpty()) {
            $this->vk->sendMessage(
                $userId,
                "🟢 На выбранную дату занятых временных слотов нет.\nМожно выбрать любое свободное время."
            );

            return;
        }

        $message = "📌 Занятые временные слоты:\n\n";

        foreach ($busySlots as $slot) {
            $message .= "• {$slot->start_time} – {$slot->end_time}\n";
        }

        $this->vk->sendMessage($userId, $message);
    }

    protected function processStartTime(int $userId, string $text, array $state): void
    {
        $text = trim($text);
        if (!preg_match('/^\d{2}:\d{2}$/', $text)) {
            $this->vk->sendMessage($userId, "❌ Формат должен быть ЧЧ:ММ (например, 14:00). Повторите:");
            return;
        }

        try {
            $bookingDateTime = Carbon::createFromFormat(
                'd.m.Y H:i',
                "{$state['date']} {$text}"
            );

            if ($bookingDateTime->lt(now()->addHours(24))) {
                $this->vk->sendMessage(
                    $userId,
                    "❌ Пространство можно бронировать минимум за 24 часа до начала.\n"
                    . "Выберите другую дату или время."
                );

                $state['step'] = 'date';

                Cache::put(
                    "vk_bot_state_{$userId}",
                    $state,
                    now()->addMinutes(30)
                );

                $this->vk->sendMessage(
                    $userId,
                    "📅 Введите новую дату (ДД.ММ.ГГГГ):"
                );

                return;
            }
        } catch (\Exception $e) {
            $this->vk->sendMessage(
                $userId,
                "❌ Не удалось обработать дату и время. Попробуйте снова."
            );

            return;
        }

        $state['start_time'] = $text;
        $state['step'] = 'end_time';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $this->vk->sendMessage($userId, "⏰ Введите время окончания (ЧЧ:ММ):");
    }

    protected function processEndTime(int $userId, string $text, array $state): void
    {
        $text = trim($text);
        if (!preg_match('/^\d{2}:\d{2}$/', $text)) {
            $this->vk->sendMessage($userId, "❌ Формат времени – ЧЧ:ММ. Повторите:");
            return;
        }

        if ($text <= $state['start_time']) {
            $this->vk->sendMessage($userId, "❌ Время окончания должно быть позже начала. Введите ещё раз:");
            return;
        }

        $busy = Booking::where('classroom_id', $state['classroom_id'])
            ->where('date', $state['date'])
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($state, $text) {
                $query->where('start_time', '<', $text)
                      ->where('end_time', '>', $state['start_time']);
            })
            ->exists();

        if ($busy) {
            $this->vk->sendMessage($userId, "❌ Это время уже занято. Введите другую дату или время.\n"
                . "📅 Введите новую дату (ДД.ММ.ГГГГ):");
            $state['step'] = 'date';
            Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));
            return;
        }

        $state['end_time'] = $text;
        $state['step'] = 'purpose';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $this->vk->sendMessage($userId, "🎯 Введите цель бронирования (например, собрание, репетиция):");
    }

    protected function processPurpose(int $userId, string $text, array $state): void
    {
        $text = trim($text);
        if (mb_strlen($text) < 3) {
            $this->vk->sendMessage($userId, "❌ Слишком короткая цель. Опишите подробнее:");
            return;
        }

        $state['purpose'] = $text;
        $state['step'] = 'equipment';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $buttons = [
            [['text' => 'Нет', 'color' => 'negative']],
        ];
        $this->vk->sendMessageWithKeyboard($userId, "🔧 Нужно ли оборудование? Введите список или нажмите «Нет»:", $buttons, true);
    }

    protected function processEquipment(int $userId, string $text, array $state): void
    {
        $text = trim($text);
        if (mb_strtolower($text) === 'отмена') {
            $state['equipment'] = null;
        } elseif (mb_strtolower($text) === 'нет') {
            $state['equipment'] = null;
        } else {
            $state['equipment'] = $text;
        }

        $state['step'] = 'tech_support';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $buttons = [
            [['text' => 'Да', 'color' => 'positive'], ['text' => 'Нет', 'color' => 'negative']],
        ];
        $this->vk->sendMessageWithKeyboard($userId, "👨‍💻 Нужен ли технический специалист для помощи в подключении оборудования?",
                                            $buttons, true);
    }

    protected function processTechSupport(int $userId, string $text, array $state): void
    {
        $text = mb_strtolower(trim($text));
        if ($text === 'да') {
            $state['is_tech_support'] = 1;
        } elseif ($text === 'нет') {
            $state['is_tech_support'] = 0;
        } else {
            $this->vk->sendMessage($userId, "❌ Ответьте «Да» или «Нет». Нужен ли тех. специалист?");
            return;
        }

        $state['step'] = 'comment';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $buttons = [
            [['text' => 'Нет', 'color' => 'negative']],
        ];
        $this->vk->sendMessageWithKeyboard($userId, "💬 Оставьте комментарий для администратора или нажмите «Нет»:", $buttons, true);
    }

    protected function processComment(int $userId, string $text, array $state, User $user): void
    {
        if (mb_strtolower(trim($text)) === 'нет') {
            $state['user_comment'] = null;
        } else {
            $state['user_comment'] = trim($text);
        }

        $state['step'] = 'confirm';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $summary = "📋 Проверьте данные заявки:\n\n"
            . "👤 ФИО: {$user->name}\n"
            . "🏛 Факультет: {$user->faculty}\n"
            . "👥 Группа: {$user->group}\n"
            . "📱 Телефон: {$user->phone}\n"
            . "🏫 Пространство: {$state['classroom_name']}\n"
            . "📅 Дата: {$state['date']}\n"
            . "⏰ Время: {$state['start_time']} – {$state['end_time']}\n"
            . "🎯 Цель: {$state['purpose']}\n"
            . "🔧 Оборудование: " . ($state['equipment'] ?: 'нет') . "\n"
            . "👨‍💻 Техподдержка: " . ($state['is_tech_support'] ? 'да' : 'нет') . "\n"
            . "💬 Комментарий: " . ($state['user_comment'] ?: 'нет') . "\n\n"
            . "Отправляем?";

        $buttons = [
            [['text' => '✅ Отправить', 'color' => 'positive'], ['text' => '❌ Отменить', 'color' => 'negative']],
        ];
        $this->vk->sendMessageWithKeyboard($userId, $summary, $buttons, true);
    }

    protected function processConfirm(int $userId, string $text, array $state, User $user): void
    {
        $text = mb_strtolower(trim($text));
        if ($text === 'отменить' || $text === '❌ отменить') {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage($userId, "🚫 Заявка отменена.");
            return;
        }

        if ($text !== 'отправить' && $text !== '✅ отправить') {
            $this->vk->sendMessage($userId, "Пожалуйста, выберите «Отправить» или «Отменить».");
            return;
        }

        if (!Classroom::bookable()->whereKey($state['classroom_id'])->exists()) {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage(
                $userId,
                "❌ Это пространство временно недоступно для бронирования. Чтобы начать заново, отправьте /бронь."
            );
            return;
        }

        $booking = Booking::create([
            'user_id'         => $user->id,
            'classroom_id'    => $state['classroom_id'],
            'date'            => $state['date'],
            'start_time'      => $state['start_time'],
            'end_time'        => $state['end_time'],
            'purpose'         => $state['purpose'],
            'name'            => $user->name,
            'faculty'         => $user->faculty,
            'group'           => $user->group,
            'phone'           => $user->phone,
            'equipment'       => $state['equipment'],
            'is_tech_support' => $state['is_tech_support'],
            'user_comment'    => $state['user_comment'],
            'vk_link'         => "https://vk.com/id{$userId}",
            'vk_user_id'      => (string) $userId,
            'status'          => 'pending',
        ]);

        $booking->load('classroom');

        try {
            app(VkNotificationService::class)->notifyBookingCreated($booking, true);
        } catch (\Exception $e) {
            Log::error(
                'VK notification create booking error: ' . $e->getMessage()
            );
        }

        Cache::forget("vk_bot_state_{$userId}");

        $this->vk->sendMessage($userId, "✅ Заявка №{$booking->id} успешно создана! Ожидайте подтверждения администратора. "
            . "О статусе заявки пришлём уведомление.\n\nЧтобы отозвать заявку, отправьте /отозвать.");
    }

    protected function sendStepWithdraw(int $userId, User $user): void
    {
        $bookings = $user->bookings()
            ->with('classroom')
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->take(20)
            ->get()
            ->filter(fn ($b) => $b->isWithdrawable())
            ->take(8)
            ->values();

        if ($bookings->isEmpty()) {
            $this->vk->sendMessage($userId, "ℹ️ У вас нет заявок, которые можно отозвать.");
            return;
        }

        $message = "↩️ Выберите заявку, которую нужно отозвать:\n\n";
        $buttons = [];

        foreach ($bookings as $b) {
            $statusText = $b->status === 'approved' ? 'одобрена' : 'на рассмотрении';
            $message .= "№{$b->id}: 🏫 {$b->classroom->room}, 📅 {$b->date}, ⏰ {$b->start_time}–{$b->end_time} ({$statusText})\n";
            $buttons[] = [['text' => "Отозвать №{$b->id}"]];
        }

        $buttons[] = [['text' => 'Отмена', 'color' => 'negative']];

        $this->vk->sendMessageWithKeyboard($userId, $message, $buttons, true);

        Cache::put("vk_bot_state_{$userId}", [
            'step' => 'withdraw_select',
            'ids'  => $bookings->pluck('id')->all(),
        ], now()->addMinutes(30));
    }

    protected function processWithdrawSelect(int $userId, string $text, array $state, User $user): void
    {
        if (mb_strtolower(trim($text)) === 'отмена') {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage($userId, "🚫 Отзыв заявки отменён.");
            return;
        }

        if (!preg_match('/(\d+)/u', $text, $m) || !in_array((int) $m[1], $state['ids'], true)) {
            $this->vk->sendMessage($userId, "❌ Выберите заявку кнопкой из списка или нажмите «Отмена».");
            return;
        }

        $booking = $user->bookings()->with('classroom')->find((int) $m[1]);

        if (!$booking) {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage($userId, "❌ Заявка не найдена.");
            return;
        }

        $state['booking_id'] = $booking->id;
        $state['step'] = 'withdraw_confirm';
        Cache::put("vk_bot_state_{$userId}", $state, now()->addMinutes(30));

        $buttons = [
            [['text' => 'Да, отозвать', 'color' => 'positive'], ['text' => 'Нет', 'color' => 'negative']],
        ];

        $this->vk->sendMessageWithKeyboard(
            $userId,
            "Отозвать заявку №{$booking->id}?\n\n"
            . "🏫 {$booking->classroom->room}\n"
            . "📅 {$booking->date}, {$booking->start_time} – {$booking->end_time}\n\n"
            . "Администратор получит уведомление.",
            $buttons,
            true
        );
    }

    protected function processWithdrawConfirm(int $userId, string $text, array $state, User $user): void
    {
        $answer = mb_strtolower(trim($text));

        if (in_array($answer, ['нет', 'отмена'], true)) {
            Cache::forget("vk_bot_state_{$userId}");
            $this->vk->sendMessage($userId, "🚫 Заявка не отозвана.");
            return;
        }

        if (!in_array($answer, ['да, отозвать', 'да'], true)) {
            $this->vk->sendMessage($userId, "Пожалуйста, выберите «Да, отозвать» или «Нет».");
            return;
        }

        Cache::forget("vk_bot_state_{$userId}");

        $booking = $user->bookings()->with(['classroom', 'user'])->find($state['booking_id']);

        if (!$booking) {
            $this->vk->sendMessage($userId, "❌ Заявка не найдена.");
            return;
        }

        try {
            app(BookingWithdrawalService::class)->withdraw($booking, $user, false);
            $this->vk->sendMessage($userId, "✅ Заявка №{$booking->id} отозвана. Администратор уведомлён.");
        } catch (\DomainException $e) {
            $this->vk->sendMessage($userId, "❌ " . $e->getMessage());
        }
    }
}
