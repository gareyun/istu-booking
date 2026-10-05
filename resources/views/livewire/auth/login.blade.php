
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация — Бронирование аудиторий</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f8f9fc] font-sans flex items-center justify-center px-4">
    <div class="w-full max-w-[500px]">
        <div class="bg-white rounded-[15px] shadow-[0_0_30px_rgba(0,0,0,0.1)] p-6 md:p-10">

            <div class="text-center mb-8">
                <h1 class="text-[1.6rem] font-bold text-primary">Бронирование аудиторий</h1>
                <p class="mt-1 text-sm text-secondary">Войдите в систему для продолжения</p>
            </div>

            @if(session('error'))
                <div class="mb-5 bg-red-50 border border-red-200
                            text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200
                            text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block font-semibold text-[#495057] mb-2">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="Введите email"
                        class="w-full px-4 py-3 border border-gray-300
                               rounded-lg text-gray-700
                               outline-none transition-colors
                               focus:border-primary
                               focus:ring-2 focus:ring-primary/10">
                </div>

                <div>
                    <label for="password" class="block font-semibold text-[#495057] mb-2">Пароль</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Введите пароль"
                        class="w-full px-4 py-3 border border-gray-300
                               rounded-lg text-gray-700
                               outline-none transition-colors
                               focus:border-primary
                               focus:ring-2 focus:ring-primary/10">
                </div>

                <x-button type="submit" class="w-full">Войти</x-button>
            </form>

        </div>
    </div>
</body>
</html>

