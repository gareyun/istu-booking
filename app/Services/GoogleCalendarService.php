<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;

class GoogleCalendarService
{
    public function client()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path(config('services.google_service_account.path')));
        $client->addScope(Google_Service_Calendar::CALENDAR);

        return $client;
    }

    public function createEvent($booking)
    {
        $service = new Google_Service_Calendar($this->client());

        $date = Carbon::createFromFormat('d.m.Y', $booking->date)->format('Y-m-d');
        $calendarId = $booking->classroom->google_calendar_id;

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

        try {
            $event = $service->events->insert($calendarId, $event);
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }

        return $event->id;
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