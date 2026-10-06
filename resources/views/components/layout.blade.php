@php
    $fullWidthHeader = request()->routeIs('admin.*', 'schedule', 'tech-support');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование пространств - СЦ "Интеграл"</title>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#f8f9fc] font-sans">
    @auth
        <header class="{{ $fullWidthHeader ? 'pt-0' : 'px-4 pt-5' }}">
            <div class="{{ $fullWidthHeader
                ? 'w-full rounded-none'
                : 'max-w-[800px] mx-auto rounded-[15px]'
            }} bg-white shadow-[0_0_20px_rgba(0,0,0,0.07)] px-5 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary">👤</div>
                        <div>
                            <div class="font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</div>
                            @if(auth()->user()->role === 'admin')
                                <div class="text-xs text-gray-500 mt-0.5">Администратор</div>
                            @elseif(auth()->user()->role === 'tech')
                                <div class="text-xs text-gray-500 mt-0.5">Технический специалист</div>
                            @else
                                <div class="text-xs text-gray-500 mt-0.5">Пользователь</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'tech')
                            <a href="{{ route('booking') }}"
                                class="px-3 py-2 border border-gray-300 text-gray-700
                                       rounded-lg hover:bg-gray-100 transition-colors
                                       text-sm font-semibold">
                                Бронирование
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.index') }}"
                                class="px-3 py-2 border border-gray-300 text-gray-700
                                       rounded-lg hover:bg-gray-100 transition-colors
                                       text-sm font-semibold">
                                ⚙️ Админ-панель
                            </a>
                        @elseif(auth()->user()->role === 'tech')
                            <a href="{{ route('tech-support') }}"
                                class="px-3 py-2 border border-gray-300 text-gray-700
                                       rounded-lg hover:bg-gray-100 transition-colors
                                       text-sm font-semibold">
                                ⚙️ Тех-панель
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg
                                       hover:bg-red-50 hover:text-red-600 hover:border-red-200
                                       transition-colors text-sm font-semibold cursor-pointer">
                                Выйти
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
    @endauth

    <main>
        {{ $slot }}
    </main>
    
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
</body>
</html>