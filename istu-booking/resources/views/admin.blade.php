<x-layout>

    <h2 class='text-2xl'>Входящие заявки</h2>

    <div class="bookings-wrapper flex">
        @forelse ($bookings as $booking)
            <x-booking-card :booking="$booking"/>
        @empty
            <h1>Входящих заявок нет</h1>
        @endforelse
    </div>

</x-layout>