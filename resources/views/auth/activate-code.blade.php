<x-auth-layout title="Ввод кода">
    <div class="text-center mb-8">
        <h1 class="text-[1.6rem] font-bold text-primary">Код и пароль</h1>
        <p class="mt-1 text-sm text-secondary">
            Введите код из сообщения бота и задайте пароль для входа на сайт.
        </p>
    </div>

    @if(session('status'))
        <div class="mb-5 bg-blue-50 border border-blue-200
                    text-blue-700 px-4 py-3 rounded-lg text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200
                    text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('activate.confirm') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block font-semibold text-[#495057] mb-2">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $email) }}"
                required
                autocomplete="email"
                class="w-full px-4 py-3 border border-gray-300
                       rounded-lg text-gray-700
                       outline-none transition-colors
                       focus:border-primary
                       focus:ring-2 focus:ring-primary/10">
        </div>

        <div>
            <label for="code" class="block font-semibold text-[#495057] mb-2">Код из VK</label>
            <input
                id="code"
                type="text"
                name="code"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                required
                autofocus
                autocomplete="one-time-code"
                placeholder="6 цифр"
                class="w-full px-4 py-3 border border-gray-300
                       rounded-lg text-gray-700 tracking-widest
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
                minlength="8"
                autocomplete="new-password"
                class="w-full px-4 py-3 border border-gray-300
                       rounded-lg text-gray-700
                       outline-none transition-colors
                       focus:border-primary
                       focus:ring-2 focus:ring-primary/10">
            <p class="mt-1.5 text-xs text-gray-500">Не менее 8 символов.</p>
        </div>

        <div>
            <label for="password_confirmation" class="block font-semibold text-[#495057] mb-2">
                Подтверждение пароля
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                minlength="8"
                autocomplete="new-password"
                class="w-full px-4 py-3 border border-gray-300
                       rounded-lg text-gray-700
                       outline-none transition-colors
                       focus:border-primary
                       focus:ring-2 focus:ring-primary/10">
        </div>

        <x-button type="submit" class="w-full">Активировать</x-button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('activate') }}" class="font-semibold text-blue-600 hover:underline">
            Запросить код заново
        </a>
    </div>
</x-auth-layout>
