<x-layout>
    <p>Список аудиторий:</p>

    @foreach ($classrooms as $classroom)
        <div class="classroom">
            <p class="classroom__title">{{$classroom->room}}</p>
            <p class="classroom__description">{{$classroom->description}}</p>
        </div>
    @endforeach

</x-layout>