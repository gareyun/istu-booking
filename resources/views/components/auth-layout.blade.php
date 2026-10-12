@props(['title' => null])

@php($siteName = 'Бронирование молодёжных пространств СЦ "Интеграл"')

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' — ' . $siteName : $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f8f9fc] font-sans flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-[500px]">
        <div class="bg-white rounded-[15px] shadow-[0_0_30px_rgba(0,0,0,0.1)] p-6 md:p-10">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
