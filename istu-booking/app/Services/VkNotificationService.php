<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VkNotificationService
{
    private $accessToken;
    private $apiVersion = '5.131';

    public function __construct()
    {
        $this->accessToken = config('services.vk.access_token');
    }

    public function extractUserId(string $vkLink): ?string
    {
        $vkLink = trim($vkLink);
        
        // Форматы ссылок:
        // https://vk.com/id123456789
        // https://vk.com/username
        // vk.com/username
        // @username
        // username
        
        $vkLink = ltrim($vkLink, '@');
        
        // извлекаем идентификатор из URL
        if (preg_match('/vk\.com\/(.+)/', $vkLink, $matches)) {
            $identifier = trim($matches[1], '/');
        } else {
            $identifier = $vkLink;
        }
        
        // если это числовой ID (id123456789)
        if (preg_match('/^id(\d+)$/', $identifier, $matches)) {
            return $matches[1];
        }
        
        // если просто число
        if (is_numeric($identifier)) {
            return $identifier;
        }
        
        // если это username - получаем ID через API
        return $this->resolveScreenName($identifier);
    }

    private function resolveScreenName(string $screenName): ?string
    {
        try {
            $response = Http::get('https://api.vk.com/method/utils.resolveScreenName', [
                'screen_name' => $screenName,
                'access_token' => $this->accessToken,
                'v' => $this->apiVersion,
            ]);

            $data = $response->json();
            
            if (isset($data['response']['object_id'])) {
                return (string) $data['response']['object_id'];
            }
            
            Log::warning('VK: Не удалось разрешить screen_name', [
                'screen_name' => $screenName,
                'response' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('VK resolveScreenName error: ' . $e->getMessage());
        }
        
        return null;
    }

    public function sendMessage(string $userId, string $message): bool
    {
        try {
            $randomId = random_int(100000, 999999);
            
            $response = Http::get('https://api.vk.com/method/messages.send', [
                'user_id' => (int) $userId,
                'message' => $message,
                'random_id' => $randomId,
                'access_token' => $this->accessToken,
                'v' => $this->apiVersion,
            ]);

            $data = $response->json();
            
            if (isset($data['error'])) {
                Log::error('VK send message error', [
                    'user_id' => $userId,
                    'error' => $data['error']
                ]);
                return false;
            }
            
            if (isset($data['response'])) {
                Log::info('VK message sent successfully', [
                    'user_id' => $userId,
                    'message_id' => $data['response']
                ]);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('VK sendMessage exception: ' . $e->getMessage());
            return false;
        }
    }

    public function notifyStatusChange($booking): bool
    {
        // указана ли ссылка VK
        if (empty($booking->vk_link)) {
            Log::info('VK notification skipped: no vk_link');
            return false;
        }

        $userId = $this->extractUserId($booking->vk_link);
        
        if (!$userId) {
            Log::warning('VK: Не удалось извлечь ID пользователя', [
                'vk_link' => $booking->vk_link
            ]);
            return false;
        }

        // Формируем сообщение
        $statusEmoji = match($booking->status) {
            'approved' => '✅',
            'rejected' => '❌',
            'cancelled' => '❌',
            default => '📋'
        };

        $statusText = match($booking->status) {
            'approved' => 'ОДОБРЕНА',
            'rejected' => 'ОТКЛОНЕНА',
            'cancelled' => 'ОТМЕНЕНА',
            default => 'ОБНОВЛЁН'
        };

        $message = "{$statusEmoji} Статус вашей заявки изменён!\n\n";
        $message .= "📋 Статус: {$statusText}\n";
        $message .= "🏫 Аудитория: {$booking->classroom->room}\n";
        $message .= "📅 Дата: {$booking->date}\n";
        $message .= "⏰ Время: {$booking->start_time} - {$booking->end_time}\n";
        $message .= "🎯 Цель: {$booking->purpose}\n";

        if (!empty($booking->admin_comment)) {
            $message .= "\n💬 Комментарий администратора:\n{$booking->admin_comment}";
        }

        if ($booking->status === 'approved') {
            $message .= "\n\nПожалуйста, ознакомьтесь с правилами использования аудитории.";
        } elseif ($booking->status === 'rejected') {
            $message .= "\n\nВы можете подать новую заявку на другую дату или аудиторию.";
        } elseif ($booking->status === 'cancelled') {
            $message .= "\n\nБронь была отменена администратором. Вы можете подать новую заявку.";
        }

        return $this->sendMessage($userId, $message);
    }
}