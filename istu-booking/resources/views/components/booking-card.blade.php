@props(['booking'])

<div class="bg-white shadow-md rounded p-6 m-5 w-100">
    <h3>ID: {{$booking->id}}</h3>
    <h2 class="text-xl font-bold mb-2"><b>Аудитория:</b> {{$booking->classroom->room}}</h2>
    <p class="text-black-600"><b>ФИО:</b> {{$booking->user->name}}</p>
    <p class="text-black-600"><b>Группа:</b> {{$booking->user->group}}</p>
    <p class="text-black-600"><b>Дата:</b> {{$booking->date}}</p>
    <p class="text-black-600"><b>Время:</b> {{$booking->start_time}} - {{$booking->end_time}}</p>
    <p class="text-black-600"><b>Цель:</b> {{$booking->purpose}}</p>
    <p class="text-black-600"><b>Комментарий:</b> {{$booking->comment}}</p>
    <button class="mt-4 bg-green-500 text-white px-3 py-1 rounded">Принять</button>
    <button class="mt-4 bg-red-500 text-white px-3 py-1 rounded">Отклонить</button>
</div>