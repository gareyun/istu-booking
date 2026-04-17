<x-layout>
    @vite('resources/css/admin.css')

    <div class="main-content">
        <h1 class="section-title">Заявки</h1>
        
        <div class="tabs">
            <a href="{{ route('admin.classrooms') }}" class="btn">Аудитории</a>
        </div>

        <div class="status-tabs">
            <a href="{{ route('admin') }}"
               class="status-tab {{ !$status ? 'active' : '' }}">
                Все
            </a>

            <a href="{{ route('admin', ['status' => 'pending']) }}"
               class="status-tab {{ $status === 'pending' ? 'active' : '' }}">
                В ожидании
            </a>

            <a href="{{ route('admin', ['status' => 'approved']) }}"
               class="status-tab {{ $status === 'approved' ? 'active' : '' }}">
                Одобренные
            </a>

            <a href="{{ route('admin', ['status' => 'rejected']) }}"
               class="status-tab {{ $status === 'rejected' ? 'active' : '' }}">
                Отклонённые
            </a>
        </div>

        {{-- <div class="bookings-wrapper flex flex-wrap justify-start"> --}}
        <div class="bookings-wrapper">
            @forelse ($bookings as $booking)
                <x-booking-card :booking="$booking"/>
            @empty
                <h1>Заявок нет</h1>
            @endforelse
        </div>

    </div>

</x-layout>