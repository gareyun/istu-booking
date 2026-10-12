<?php

namespace App\Services;

use App\Models\User;
use App\Models\WebActivationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;


class WebActivationService
{
    public const CODE_TTL_MINUTES = 10;
    public const MAX_ATTEMPTS = 5;

    private const MAX_ISSUES = 3;
    private const ISSUE_WINDOW_SECONDS = 900;

    public const ERROR_INVALID = 'Неверный или просроченный код. Запросите новый код.';
    public const ERROR_LOCKED = 'Превышено число попыток. Запросите новый код.';

    public function __construct(protected VkNotificationService $vk)
    {
    }

    public function requestCode(string $email): void
    {
        $user = $this->findUser($email);

        if (!$user || $user->hasWebAccess()) {
            return;
        }

        if (!$user->isEmailVerified()) {
            $this->vk->sendMessage(
                (string) $user->vk_id,
                "📧 Вы запросили активацию сайта, но почта ещё не подтверждена. "
                . "Сначала перейдите по ссылке из письма, затем повторите запрос."
            );
            return;
        }

        $limitKey = "web-activation-issue:{$user->id}";

        if (RateLimiter::tooManyAttempts($limitKey, self::MAX_ISSUES)) {
            return;
        }

        RateLimiter::hit($limitKey, self::ISSUE_WINDOW_SECONDS);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($user, $code) {
            WebActivationCode::where('user_id', $user->id)->delete();

            WebActivationCode::create([
                'user_id' => $user->id,
                'code_hash' => $this->hash($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            ]);
        });

        $sent = $this->vk->sendMessage(
            (string) $user->vk_id,
            "🔐 Код активации сайта: {$code}\n"
            . "Он действует " . self::CODE_TTL_MINUTES . " минут. Никому его не сообщайте.\n"
            . "Если вы не запрашивали код, просто проигнорируйте это сообщение."
        );

        if (!$sent) {
            Log::warning('Web activation code was not delivered to VK', ['user_id' => $user->id]);
            WebActivationCode::where('user_id', $user->id)->delete();
        }
    }

    /**
     * @return array{ok: bool, user: ?User, error: ?string}
     */
    public function activate(string $email, string $code, string $password): array
    {
        $user = $this->findUser($email);

        if (!$user || $user->hasWebAccess() || !$user->isEmailVerified()) {
            return $this->fail(self::ERROR_INVALID);
        }

        return DB::transaction(function () use ($user, $code, $password) {
            $record = WebActivationCode::where('user_id', $user->id)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$record || $record->expires_at->isPast()) {
                return $this->fail(self::ERROR_INVALID);
            }

            if ($record->attempts >= self::MAX_ATTEMPTS) {
                return $this->fail(self::ERROR_LOCKED);
            }

            $record->increment('attempts');

            if (!hash_equals($record->code_hash, $this->hash($code))) {
                return $this->fail(self::ERROR_INVALID);
            }

            $user->password = $password;
            $user->save();

            WebActivationCode::where('user_id', $user->id)->delete();

            return ['ok' => true, 'user' => $user, 'error' => null];
        });
    }

    protected function findUser(string $email): ?User
    {
        return User::where('email', mb_strtolower(trim($email)))
            ->whereNotNull('vk_id')
            ->first();
    }

    protected function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    protected function fail(string $error): array
    {
        return ['ok' => false, 'user' => null, 'error' => $error];
    }
}
