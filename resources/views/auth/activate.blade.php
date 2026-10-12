<x-auth-layout title="Активация сайта">
    <div class="text-center mb-8">
        <h1 class="text-[1.6rem] font-bold text-primary">Активация сайта</h1>
        <p class="mt-1 text-sm text-secondary">
            Для тех, кто зарегистрировался через VK-бота. Укажите почту, и бот пришлёт код в личные сообщения.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200
                    text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('activate.request') }}" class="space-y-5">
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
                placeholder="Почта, указанная при регистрации"
                class="w-full px-4 py-3 border border-gray-300
                       rounded-lg text-gray-700
                       outline-none transition-colors
                       focus:border-primary
                       focus:ring-2 focus:ring-primary/10">
        </div>

        <x-button type="submit" class="w-full">Получить код в VK</x-button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('activate.code') }}" class="font-semibold text-blue-600 hover:underline">
            У меня уже есть код
        </a>
        <span class="mx-2">·</span>
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:underline">
            Войти
        </a>
    </div>
</x-auth-layout>
