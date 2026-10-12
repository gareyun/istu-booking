<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VkRegistrationService
{
    public const START_COMMANDS = ['/start', '/бронь'];

    private const BTN_RESEND = '📨 Отправить письмо повторно';
    private const BTN_CHANGE = '✏️ Изменить email';

    private const STATE_TTL_MINUTES = 30;

    public function __construct(
        protected VkBotService $vk,
        protected VkEmailVerificationService $emailVerification,
    ) {
    }

    public function handle(int $vkId, string $text): void
    {
        $text = trim($text);
        $lower = mb_strtolower($text);

        if (in_array($lower, self::START_COMMANDS, true)) {
            $this->start($vkId);
            return;
        }

        $state = Cache::get($this->stateKey($vkId));

        if (!$state) {
            return;
        }

        if (in_array($lower, ['/отмена', 'отмена'], true)) {
            Cache::forget($this->stateKey($vkId));
            $this->vk->sendMessage($vkId, "🚫 Регистрация отменена. Чтобы начать заново, отправьте /бронь.");
            return;
        }

        switch ($state['step'] ?? '') {
            case 'reg_name':
                $this->processName($vkId, $text, $state);
                break;
            case 'reg_faculty':
                $this->processFaculty($vkId, $text, $state);
                break;
            case 'reg_group':
                $this->processGroup($vkId, $text, $state);
                break;
            case 'reg_phone':
                $this->processPhone($vkId, $text, $state);
                break;
            case 'reg_email':
                $this->processEmail($vkId, $text, $state);
                break;
            default:
                $this->start($vkId);
        }
    }

    protected function start(int $vkId): void
    {
        $this->putState($vkId, ['step' => 'reg_name']);

        $this->vk->sendMessageWithKeyboard(
            $vkId,
            "👋 Здравствуйте! Чтобы бронировать пространства, нужно один раз зарегистрироваться. "
            . "Это займёт пару минут, повторно данные вводить не придётся. "
            . "Отменить регистрацию можно командой /отмена.\n\n"
            . "👤 Введите ваше ФИО полностью (например, Иванов Иван Иванович):",
            [[['text' => 'Отмена', 'color' => 'negative']]],
            true
        );
    }

    protected function processName(int $vkId, string $text, array $state): void
    {
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (
            mb_strlen($text) > 255
            || count($words) < 3
            || !preg_match('/^[\p{L}\-\'.\s]+$/u', $text)
        ) {
            $this->vk->sendMessage($vkId, "❌ Введите фамилию, имя и отчество, только буквами:");
            return;
        }

        $state['name'] = implode(' ', $words);
        $state['step'] = 'reg_faculty';
        $this->putState($vkId, $state);

        $this->vk->sendMessage($vkId, "🏛 Введите ваш факультет/институт:");
    }

    protected function processFaculty(int $vkId, string $text, array $state): void
    {
        if (mb_strlen($text) < 2 || mb_strlen($text) > 255) {
            $this->vk->sendMessage($vkId, "❌ Укажите факультет (от 2 до 255 символов):");
            return;
        }

        $state['faculty'] = $text;
        $state['step'] = 'reg_group';
        $this->putState($vkId, $state);

        $this->vk->sendMessage($vkId, "👥 Введите вашу учебную группу (например, Б22-191-2):");
    }

    protected function processGroup(int $vkId, string $text, array $state): void
    {
        if (mb_strlen($text) < 2 || mb_strlen($text) > 100) {
            $this->vk->sendMessage($vkId, "❌ Укажите группу (от 2 до 100 символов):");
            return;
        }

        $state['group'] = $text;
        $state['step'] = 'reg_phone';
        $this->putState($vkId, $state);

        $this->vk->sendMessage($vkId, "📱 Введите ваш номер телефона:");
    }

    protected function processPhone(int $vkId, string $text, array $state): void
    {
        $digits = preg_replace('/\D/', '', $text);

        if (
            !preg_match('/^\+?[0-9\s()\-]{10,20}$/', $text)
            || strlen($digits) < 10
            || strlen($digits) > 15
        ) {
            $this->vk->sendMessage($vkId, "❌ Неверный формат номера. Введите корректный номер телефона:");
            return;
        }

        $state['phone'] = $text;
        $state['step'] = 'reg_email';
        $this->putState($vkId, $state);

        $this->vk->sendMessage(
            $vkId,
            "📧 Введите адрес электронной почты. Мы отправим на него ссылку для подтверждения:"
        );
    }

    protected function processEmail(int $vkId, string $text, array $state): void
    {
        $email = mb_strtolower($text);

        if (mb_strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->vk->sendMessage($vkId, "❌ Некорректный адрес. Введите email ещё раз (например, student@example.ru):");
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->vk->sendMessage(
                $vkId,
                "❌ Эта почта уже используется в системе. Укажите другой адрес.\n"
                . "Если вы уже регистрировались на сайте, обратитесь к администратору."
            );
            return;
        }

        $user = $this->createUser($vkId, $state, $email);

        Cache::forget($this->stateKey($vkId));

        if (!$user) {
            $this->vk->sendMessage($vkId, "⚠️ Не удалось завершить регистрацию. Напишите /бронь и попробуйте ещё раз.");
            return;
        }

        $this->sendVerification($user);
    }

    protected function createUser(int $vkId, array $state, string $email): ?User
    {
        try {
            $user = User::create([
                'name' => $state['name'],
                'faculty' => $state['faculty'],
                'group' => $state['group'],
                'phone' => $state['phone'],
                'email' => $email,
                'password' => null,
                'role' => 'student',
                'vk_id' => (string) $vkId,
                'vk_link' => "https://vk.com/id{$vkId}",
            ]);
        } catch (UniqueConstraintViolationException $e) {
            Log::warning('VK registration unique violation: ' . $e->getMessage(), ['vk_id' => $vkId]);

            return User::where('vk_id', (string) $vkId)->first();
        }

        Booking::whereNull('user_id')
            ->where('vk_user_id', (string) $vkId)
            ->update(['user_id' => $user->id]);

        return $user;
    }

    public function handleUnverified(User $user, string $text): void
    {
        $vkId = (int) $user->vk_id;
        $lower = mb_strtolower(trim($text));
        $state = Cache::get($this->stateKey($vkId));

        if (($state['step'] ?? null) === 'reg_change_email') {
            if (in_array($lower, ['/отмена', 'отмена'], true)) {
                Cache::forget($this->stateKey($vkId));
                $this->remind($user);
                return;
            }

            $this->processChangeEmail($user, trim($text));
            return;
        }

        $button = $this->normalizeButton($text);

        if ($button === $this->normalizeButton(self::BTN_RESEND)) {
            $this->sendVerification($user);
            return;
        }

        if ($button === $this->normalizeButton(self::BTN_CHANGE)) {
            $this->putState($vkId, ['step' => 'reg_change_email']);
            $this->vk->sendMessageWithKeyboard(
                $vkId,
                "✏️ Введите новый адрес электронной почты (или /отмена):",
                [[['text' => 'Отмена', 'color' => 'negative']]],
                true
            );
            return;
        }

        if (in_array($lower, self::START_COMMANDS, true)) {
            $this->remind($user);
        }
    }

    protected function processChangeEmail(User $user, string $text): void
    {
        $vkId = (int) $user->vk_id;
        $email = mb_strtolower($text);

        if (mb_strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->vk->sendMessage($vkId, "❌ Некорректный адрес. Введите email ещё раз:");
            return;
        }

        if (User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
            $this->vk->sendMessage($vkId, "❌ Эта почта уже используется. Укажите другой адрес:");
            return;
        }

        $user->forceFill(['email' => $email, 'email_verified_at' => null])->save();
        Cache::forget($this->stateKey($vkId));

        $this->sendVerification($user);
    }

    protected function remind(User $user): void
    {
        $this->vk->sendMessageWithKeyboard(
            (int) $user->vk_id,
            "📧 Регистрация почти завершена: подтвердите почту {$this->maskEmail($user->email)}, "
            . "перейдя по ссылке из письма. До этого подавать заявки нельзя.",
            $this->unverifiedKeyboard(),
            false
        );
    }

    protected function sendVerification(User $user): void
    {
        $status = $this->emailVerification->sendLink($user);
        $masked = $this->maskEmail($user->email);

        $text = match ($status) {
            VkEmailVerificationService::SENT =>
                "📨 Письмо со ссылкой подтверждения отправлено на {$masked}.\n"
                . "Ссылка действует " . VkEmailVerificationService::LINK_TTL_HOURS . " часа. "
                . "Проверьте также папку «Спам».\n\n"
                . "После подтверждения я напишу вам, и можно будет подавать заявки.",
            VkEmailVerificationService::COOLDOWN =>
                "⏳ Письмо уже отправлено. Повторно запросить его можно через минуту.",
            VkEmailVerificationService::LIMIT =>
                "⛔ Слишком много отправок. Попробуйте через час или укажите другой адрес.",
            default =>
                "⚠️ Не удалось отправить письмо. Попробуйте позже или измените адрес.",
        };

        $this->vk->sendMessageWithKeyboard((int) $user->vk_id, $text, $this->unverifiedKeyboard(), false);
    }

    protected function unverifiedKeyboard(): array
    {
        return [
            [['text' => self::BTN_RESEND]],
            [['text' => self::BTN_CHANGE]],
        ];
    }

    protected function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($local, 0, 1) . str_repeat('*', max(mb_strlen($local) - 1, 2)) . '@' . $domain;
    }

    public function hasActiveRegistration(int $vkId): bool
    {
        return Cache::has($this->stateKey($vkId));
    }

    public function cancel(int $vkId): bool
    {
        return Cache::pull($this->stateKey($vkId)) !== null;
    }

    protected function normalizeButton(string $text): string
    {
        $text = preg_replace('/[^\p{L}\p{N}\s()@.]/u', '', $text);

        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $text)));
    }

    protected function stateKey(int|string $vkId): string
    {
        return "vk_reg_state_{$vkId}";
    }

    protected function putState(int $vkId, array $state): void
    {
        Cache::put($this->stateKey($vkId), $state, now()->addMinutes(self::STATE_TTL_MINUTES));
    }
}
