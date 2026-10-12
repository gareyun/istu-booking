<!DOCTYPE html>
<html lang="ru">
<body style="font-family: Arial, sans-serif; color: #212529; line-height: 1.5;">
    <p>Здравствуйте, {{ $user->name }}!</p>

    <p>
        Вы зарегистрировались в системе бронирования молодёжных пространств СЦ "Интеграл" через VK-бота.
        Чтобы завершить регистрацию, подтвердите адрес электронной почты:
    </p>

    <p style="margin: 24px 0;">
        <a href="{{ $url }}"
           style="background: #2563eb; color: #fff; padding: 12px 20px;
                  border-radius: 8px; text-decoration: none; font-weight: bold;">
            Подтвердить почту
        </a>
    </p>

    <p style="font-size: 13px; color: #6c757d;">
        Ссылка действует 24 часа. Если кнопка не работает, скопируйте адрес в браузер:<br>
        <span style="word-break: break-all;">{{ $url }}</span>
    </p>

    <p style="font-size: 13px; color: #6c757d;">
        Если вы не регистрировались, просто проигнорируйте это письмо.
    </p>
</body>
</html>
