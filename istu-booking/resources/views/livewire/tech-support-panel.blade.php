<div class="min-h-screen bg-[#f5f7fa] text-[#333] font-['Segoe_UI']">
    <div class="flex-1 p-[30px] overflow-y-auto">

        <h1 class="text-[1.5rem] mb-5 pb-[10px] border-b-2 border-[#1a2a6c] text-[#1a2a6c] font-bold">
            🔧 Панель технического специалиста
        </h1>

        <x-filter :classrooms="$classrooms" :showStatus="false"/>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-[25px] p-5">
            @forelse($bookings as $booking)
                <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.1)] p-5 transition-all duration-300
                            hover:-translate-y-[5px] hover:shadow-[0_8px_25px_rgba(0,0,0,0.15)]
                            flex flex-col justify-between">

                    <div>
                        <div class="flex justify-between items-center mb-[15px]">
                            <div class="text-[0.9rem] text-[#7f8c8d]">ID: {{ $booking->id }}</div>
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                Одобрена
                            </span>
                        </div>

                        <div class="text-[1.3rem] font-bold text-[#1a2a6c] mb-[15px]">
                            Аудитория: {{ $booking->classroom->room }}
                        </div>

                        <div class="space-y-[8px]">
                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">ФИО: </div>
                                <div>
                                    @if($booking->user)
                                        {{ $booking->user->name }}
                                    @else
                                        {{ $booking->name ?? '—' }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Группа: </div>
                                <div>
                                    @if($booking->user)
                                        {{ $booking->user->group ?? '—' }}
                                    @else
                                        {{ $booking->group ?? '—' }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Номер телефона: </div>
                                <div>
                                    @if($booking->user)
                                        {{ $booking->user->phone ?? '—' }}
                                    @else
                                        {{ $booking->phone ?? '—' }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Дата: </div>
                                <div>{{ $booking->date }}</div>
                            </div>
                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Время: </div>
                                <div>{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                            </div>
                            <div class="flex justify-between gap-5">
                                <div class="font-bold text-[#7f8c8d]">Цель: </div>
                                <div class="text-right">{{ $booking->purpose }}</div>
                            </div>
                            <div class="flex justify-between gap-5">
                                <div class="font-bold text-[#7f8c8d]">Оборудование: </div>
                                <div class="text-right font-semibold text-blue-700">
                                {{ $booking->equipment ? $booking->equipment : 'Нет' }}
                                </div>
                            </div>
                            <div class="flex justify-between gap-5">
                                <div class="font-bold text-[#7f8c8d]">Технический специалист: </div>
                                <div class="text-right
                                {{ $booking->is_tech_support ? 'text-blue-700 font-semibold' : '' }}">
                                {{ $booking->is_tech_support ? 'Да' : 'Нет' }}
                                </div>
                            </div>
                            @if($booking->user_comment)
                                <div class="flex justify-between gap-5">
                                    <div class="font-bold text-[#7f8c8d]">Комментарий студента: </div>
                                    <div class="text-right break-words">{{ $booking->user_comment }}</div>
                                </div>
                            @endif
                            @if($booking->admin_comment)
                                <div class="flex justify-between gap-5">
                                    <div class="font-bold text-[#7f8c8d]">Комментарий администратора: </div>
                                    <div class="text-right break-words">{{ $booking->admin_comment }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-2xl font-bold text-gray-400 py-10">
                    Нет заявок, требующих оборудования или помощи в подключении
                </div>
            @endforelse
        </div>
    </div>
</div>