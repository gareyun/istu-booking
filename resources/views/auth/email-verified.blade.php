<x-auth-layout title="Подтверждение почты">
    <div class="text-center">
        <h1 class="text-[1.6rem] font-bold text-primary">
            {{ $already ? 'Почта уже подтверждена' : 'Почта подтверждена' }}
        </h1>
        <p class="mt-3 text-sm text-secondary">
            Вернитесь в диалог с ботом ВКонтакте и напишите /бронь, чтобы подать заявку.
        </p>
        <p class="mt-6 text-sm text-gray-600">
            Хотите пользоваться сайтом?
            <a href="{{ route('activate') }}" class="font-semibold text-blue-600 hover:underline">
                Активировать веб-доступ
            </a>
        </p>
    </div>
</x-auth-layout>
