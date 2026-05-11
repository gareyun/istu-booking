<div class="p-4 md:p-8 bg-gray-50 min-h-screen font-sans">

    <h1 class="text-2xl md:text-3xl font-bold text-indigo-800 mb-6">
        📅 Расписание аудиторий
    </h1>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <label for="classroom" class="text-gray-700 font-semibold whitespace-nowrap">
                Аудитория:
            </label>
            <select
                wire:model.live="selectedClassroom"
                class="w-full md:w-64 border-2 border-gray-300 rounded-lg px-4 py-2
                        focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 cursor-pointer">
                <option value="">-- Выберите аудиторию --</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}">{{ $classroom->room }}</option>
                @endforeach
            </select>
        </div>

        @if($selectedClassroom)
        <div class="flex items-center gap-3">
            <button wire:click="prevWeek"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                ← Пред. неделя
            </button>
            <span class="text-gray-800 font-semibold whitespace-nowrap">
                {{ \Carbon\Carbon::parse($weekDays[0]['date'])->format('d.m') }}
                –
                {{ \Carbon\Carbon::parse($weekDays[6]['date'])->format('d.m.Y') }}
            </span>
            <button wire:click="nextWeek"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                След. неделя →
            </button>
        </div>
        @endif
    </div>

    @if($selectedClassroom)
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="grid grid-cols-[60px_repeat(7,1fr)] border-b-2 border-gray-200">
                <div class="p-2 bg-gray-100 border-r-2 border-gray-200"></div>
                @foreach($weekDays as $day)
                    <div class="p-2 text-center border-r-2 border-gray-200 last:border-r-0
                        {{ $day['date'] == \Carbon\Carbon::today()->format('Y-m-d') ? 'bg-indigo-50' : 'bg-gray-50' }}">
                        <div class="text-xs font-semibold text-gray-500 uppercase">{{ $day['dayName'] }}</div>
                        <div class="text-lg font-bold text-gray-800">{{ $day['dayNumber'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Сам календарь с сеткой и событиями --}}
            <div class="relative" style="height: {{ count($timeSlots) * 60 }}px; background: #fff;">
                
                {{-- вертикальные линии --}}
                <div class="absolute inset-0 grid grid-cols-[60px_repeat(7,1fr)] z-0">
                    @foreach($timeSlots as $time)
                        <div class="border-b border-gray-100
                                    {{ Str::endsWith($time, ':00') ? 'border-t-2 border-t-gray-300' : '' }}"></div>
                        @for($i=0; $i<7; $i++)
                            <div class="border-b border-gray-100
                                        {{ Str::endsWith($time, ':00') ? 'border-t-2 border-t-gray-300' : '' }}"></div>
                        @endfor
                    @endforeach
                </div>

                {{-- временные метки слева --}}
                <div class="absolute left-0 top-0 w-[60px] h-full z-10 bg-white">
                    @foreach($timeSlots as $time)
                        <div class="h-[60px] border-b border-gray-100 flex items-start justify-end pr-1
                                    {{ Str::endsWith($time, ':00') ? 'border-t-2 border-t-gray-300' : '' }}">
                            @if(Str::endsWith($time, ':00'))
                                <span class="text-[10px] text-gray-400">{{ $time }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- События --}}
                @foreach($weekDays as $index => $day)
                    @php $dayBookings = $bookings->where('date', $day['fullDate']); @endphp
                    @foreach($dayBookings as $booking)
                        @php
                            $calendarStart = \Carbon\Carbon::createFromFormat('H:i', '08:00');

                            $start = \Carbon\Carbon::createFromFormat('H:i', $booking->start_time);
                            $end = \Carbon\Carbon::createFromFormat('H:i', $booking->end_time);

                            $startMin = $calendarStart->diffInMinutes($start, false);
                            $endMin = $calendarStart->diffInMinutes($end, false);

                            // 30 минут = 60px
                            $pixelsPerMinute = 2;

                            $topPx = $startMin * $pixelsPerMinute;
                            $heightPx = max(
                                40,
                                ($endMin - $startMin) * $pixelsPerMinute
                            );
                        @endphp
                        <div wire:click="openBookingModal({{ $booking->id }})"
                                class="absolute z-20 rounded-md p-1 text-xs text-white bg-indigo-500 hover:bg-indigo-600
                                transition overflow-hidden cursor-pointer shadow-md hover:shadow-lg"
                                style="top: {{ $topPx }}px;
                                height: {{ $heightPx }}px;
                                left: calc(60px + (100% - 60px) / 7 * {{ $index }} + 2px);
                                width: calc((100% - 60px) / 7 - 4px);">
                            <div class="font-semibold truncate">{{ $booking->purpose }}</div>
                            <div>{{ $booking->start_time }}–{{ $booking->end_time }}</div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif

    @if($showBookingModal && $selectedBooking)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-[#1a2a6c]">Информация о бронировании</h2>
                        <p class="text-sm text-gray-500 mt-1">ID заявки: {{ $selectedBooking->id }}</p>
                    </div>

                    <button
                        wire:click="closeBookingModal"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold transition cursor-pointer">
                        &times;
                    </button>
                </div>

                <div class="p-6 space-y-4 text-[15px]">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-gray-500 font-semibold">Аудитория</div>
                            <div class="text-gray-900">{{ $selectedBooking->classroom->room }}</div>
                        </div>

                        <div>
                            <div class="text-gray-500 font-semibold">Дата</div>
                            <div class="text-gray-900">{{ $selectedBooking->date }}</div>
                        </div>

                        <div>
                            <div class="text-gray-500 font-semibold">Время</div>
                            <div class="text-gray-900">
                                {{ $selectedBooking->start_time }}
                                –
                                {{ $selectedBooking->end_time }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500 font-semibold">Статус</div>
                            <div class="font-semibold">
                                @if($selectedBooking->status === 'approved')
                                    <span class="text-green-600">✅ Одобрена</span>
                                @elseif($selectedBooking->status === 'rejected')
                                    <span class="text-red-600">❌ Отклонена</span>
                                @elseif($selectedBooking->status === 'cancelled')
                                    <span class="text-gray-600">🚫 Отменена</span>
                                @else
                                    <span class="text-yellow-600">⏳ В ожидании</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="text-gray-500 font-semibold mb-1">Студент</div>
                        <div>{{ $selectedBooking->user->name ?? 'Не указан' }}</div>
                        <div class="text-sm text-gray-500">{{ $selectedBooking->user->group ?? '' }}</div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="text-gray-500 font-semibold mb-1">Цель бронирования</div>
                        <div class="text-gray-900 whitespace-pre-line">{{ $selectedBooking->purpose }}</div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="text-gray-500 font-semibold mb-1">Оборудование</div>
                        <div class="text-gray-900">{{ $selectedBooking->equipment ?: 'Не требуется' }}</div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="text-gray-500 font-semibold mb-1">Технический специалист</div>
                        <div class="text-gray-900">{{ $selectedBooking->is_tech_support ? 'Да' : 'Нет' }}</div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="text-gray-500 font-semibold mb-1">Комментарий пользователя</div>
                        <div class="text-gray-900 whitespace-pre-line">{{ $selectedBooking->user_comment ?: '–' }}</div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="text-gray-500 font-semibold mb-1">Комментарий администратора</div>
                        <div class="text-gray-900 whitespace-pre-line">{{ $selectedBooking->admin_comment ?: '–' }}</div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button
                        wire:click="closeBookingModal"
                        class="px-5 py-2.5 bg-gradient-to-br
                            from-[#1A2A6C] to-[#3456DB]
                            text-white rounded-lg font-semibold
                            shadow-[0_4px_15px_rgba(52,86,219,0.3)]
                            hover:shadow-[0_6px_20px_rgba(52,86,219,0.45)]
                            transition-all duration-300 cursor-pointer">
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>