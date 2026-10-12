<x-auth-layout title="Авторизация">
    <div class="text-center mb-8">
        <h1 class="text-[1.6rem] font-bold text-primary">Бронирование молодёжных пространств СЦ "Интеграл"</h1>
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

    <div class="mt-6 text-center text-sm text-gray-600 space-y-2">
        <div>
            Нет аккаунта?
            <a href="https://vk.ru"
            class="font-semibold text-blue-600 hover:underline">
                Зарегистрироваться
            </a>
        </div>
        <div>
            Регистрировались через VK-бота?
            <a href="{{ route('activate') }}"
            class="font-semibold text-blue-600 hover:underline">
                Активировать веб-профиль
            </a>
        </div>
    </div>
</x-auth-layout>
