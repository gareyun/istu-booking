<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BookingWithdrawalService
{
    /**
     * @throws \DomainException
     */
    public function withdraw(Booking $booking, User $user, bool $notifyStudent = true): void
    {
        if ((int) $booking->user_id !== (int) $user->id) {
            throw new \DomainException('Заявка не найдена.');
        }

        if (!in_array($booking->status, ['pending', 'approved'], true)) {
            throw new \DomainException('Эту заявку уже нельзя отозвать: она отклонена или отменена.');
        }

        if (!$booking->isWithdrawable()) {
            throw new \DomainException('Мероприятие уже началось или прошло, бронь отозвать нельзя.');
        }

        $previousStatus = $booking->status;

        if ($previousStatus === 'approved') {
            try {
                app(GoogleCalendarService::class)->deleteEvent($booking);
            } catch (\Throwable $e) {
                Log::error('Withdraw: Google Calendar delete error: ' . $e->getMessage(), [
                    'booking_id' => $booking->id,
                ]);

                throw new \DomainException(
                    'Не удалось удалить событие из Google Календаря. Обратитесь к администратору.'
                );
            }
        }

        $updated = Booking::whereKey($booking->id)
            ->where('status', $previousStatus)
            ->update([
                'status'          => 'cancelled',
                'cancelled_by'    => 'student',
                'google_event_id' => null,
                'updated_at'      => now(),
            ]);

        if ($updated === 0) {
            throw new \DomainException('Статус заявки изменился. Обновите страницу и попробуйте снова.');
        }

        $booking->refresh()->loadMissing(['classroom', 'user']);

        try {
            app(VkNotificationService::class)->notifyAdminsBookingWithdrawn($booking, $previousStatus);
        } catch (\Throwable $e) {
            Log::error('VK admin withdraw notification error: ' . $e->getMessage());
        }

        if ($notifyStudent) {
            try {
                app(VkNotificationService::class)->notifyStudentBookingWithdrawn($booking);
            } catch (\Throwable $e) {
                Log::error('VK student withdraw notification error: ' . $e->getMessage());
            }
        }
    }
}