<?php

namespace App\Services;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

class VkEmailVerificationService
{
    public const SENT = 'sent';
    public const COOLDOWN = 'cooldown';
    public const LIMIT = 'limit';
    public const FAILED = 'failed';

    public const LINK_TTL_HOURS = 24;
    private const COOLDOWN_SECONDS = 60;
    private const MAX_SENDS_PER_HOUR = 5;

    public function sendLink(User $user): string
    {
        $cooldownKey = "email-verify-cooldown:{$user->id}";
        $limitKey = "email-verify-send:{$user->id}";

        if (Cache::has($cooldownKey)) {
            return self::COOLDOWN;
        }

        if (RateLimiter::tooManyAttempts($limitKey, self::MAX_SENDS_PER_HOUR)) {
            return self::LIMIT;
        }

        try {
            Mail::to($user->email)->send(new VerifyEmailMail($user, $this->makeUrl($user)));
        } catch (\Throwable $e) {
            Log::error('Email verification send error: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return self::FAILED;
        }

        RateLimiter::hit($limitKey, 3600);
        Cache::put($cooldownKey, 1, self::COOLDOWN_SECONDS);

        return self::SENT;
    }

    public function makeUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'email.verify',
            now()->addHours(self::LINK_TTL_HOURS),
            [
                'user' => $user->id,
                'hash' => $this->hashFor($user),
            ]
        );
    }

    public function hashMatches(User $user, string $hash): bool
    {
        return hash_equals($this->hashFor($user), $hash);
    }

    private function hashFor(User $user): string
    {
        return hash_hmac('sha256', mb_strtolower($user->email), (string) config('app.key'));
    }
}
