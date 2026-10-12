<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\VkEmailVerificationService;
use App\Services\VkNotificationService;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    public function verify(
        User $user,
        string $hash,
        VkEmailVerificationService $verification,
        VkNotificationService $vk
    ) {
        if (!$verification->hashMatches($user, $hash)) {
            abort(403);
        }

        $already = $user->isEmailVerified();

        if (!$already) {
            $user->markEmailAsVerified();

            if ($user->vk_id) {
                try {
                    $vk->sendMessage(
                        (string) $user->vk_id,
                        "✅ Почта подтверждена, регистрация завершена!\n"
                        . "Напишите /бронь, чтобы подать заявку на бронирование.",
                        true
                    );
                } catch (\Throwable $e) {
                    Log::error('VK notify after email verification failed: ' . $e->getMessage());
                }
            }
        }

        return view('auth.email-verified', ['already' => $already]);
    }
}
