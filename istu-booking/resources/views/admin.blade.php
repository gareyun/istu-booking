<x-layout>
    @vite('resources/css/admin.css')

    <div class="bookings-wrapper flex flex-wrap justify-start">
        @forelse ($bookings as $booking)
            <x-booking-card :booking="$booking"/>
        @empty
            <h1>Входящих заявок нет</h1>
        @endforelse
    </div>

</x-layout>