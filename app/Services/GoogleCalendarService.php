<?php

namespace App\Services;

use App\Exceptions\GoogleCalendarException;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Exception;
use Illuminate\Support\Facades\Log;


class GoogleCalendarService
{
    public function client()
    {
        $path = storage_path(config('services.google_service_account.path'));

        if (!is_file($path)) {
            throw new GoogleCalendarException(
                'Не найден файл ключа сервисного аккаунта Google. Проверьте настройку services.google_service_account.path.'
            );
        }

        $client = new Google_Client();
        $client->setAuthConfig($path);
        $client->addScope(Google_Service_Calendar::CALENDAR);

        return $client;
    }

    public function createEvent($booking)
    {
        $calendarId = trim((string) $booking->classroom?->google_calendar_id);

        if ($calendarId === '') {
            throw new GoogleCalendarException(
                'Для пространства «' . $booking->classroom?->room . '» не указан идентификатор Google Calendar. '
                . 'Укажите его в разделе «Пространства» и повторите одобрение заявки.'
            );
        }

        try {
            $service = new Google_Service_Calendar($this->client());

            $date = Carbon::createFromFormat('d.m.Y', $booking->date)->format('Y-m-d');

            $event = new Google_Service_Calendar_Event([
                'summary' => $booking->purpose ?? 'Бронирование',
                'description' => $booking->comment ?? '',
                'start' => [
                    'dateTime' => $date . 'T' . $booking->start_time . ':00',
                    'timeZone' => 'Europe/Samara',
                ],
                'end' => [
                    'dateTime' => $date . 'T' . $booking->end_time . ':00',
                    'timeZone' => 'Europe/Samara',
                ],
            ]);

            return $service->events->insert($calendarId, $event)->id;
        } catch (GoogleCalendarException $e) {
            throw $e;
        } catch (Google_Service_Exception $e) {
            Log::error('Google Calendar insert failed', [
                'booking_id' => $booking->id ?? null,
                'calendar_id' => $calendarId,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            throw new GoogleCalendarException(match ($e->getCode()) {
                404 => 'Календарь с указанным идентификатором не найден. Проверьте ID календаря пространства.',
                403 => 'Нет доступа к календарю. Предоставьте сервисному аккаунту права на изменение событий этого календаря.',
                400 => 'Google отклонил запрос: проверьте идентификатор календаря, дату и время заявки.',
                default => 'Не удалось создать событие в Google Calendar (код ' . $e->getCode() . '). Подробности в логе.',
            }, 0, $e);
        } catch (\Throwable $e) {
            Log::error('Google Calendar unexpected error', ['exception' => $e]);

            throw new GoogleCalendarException(
                'Ошибка при работе с Google Calendar. Подробности в логе.', 0, $e
            );
        }
    }

    public function deleteEvent($booking): void
    {
        if (empty($booking->google_event_id) || empty($booking->classroom?->google_calendar_id)) {
            return;
        }

        $service = new Google_Service_Calendar($this->client());

        try {
            $service->events->delete(
                $booking->classroom->google_calendar_id,
                $booking->google_event_id
            );
        } catch (\Google_Service_Exception $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
        }
    }
}