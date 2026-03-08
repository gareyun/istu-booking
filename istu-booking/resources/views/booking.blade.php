<x-layout>
    <p>Подать заявку:</p>

    <input type="text">

    @foreach ($bookings as $booking)
        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-bold mb-2">{{$booking->classroom->room}}</h2>
            <p class="text-gray-600">Вместимость: {{$booking->classroom->capacity}} человек</p>
            <button class="mt-4 bg-green-500 text-white px-3 py-1 rounded">Забронировать</button>
        </div>
    @endforeach


</x-layout>