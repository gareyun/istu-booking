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
                        focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                <option value="">-- Выберите аудиторию --</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}">{{ $classroom->room }}</option>
                @endforeach
            </select>
        </div>

        @if($selectedClassroom)
        <div class="flex items-center gap-3">
            <button wire:click="prevWeek"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                ← Пред. неделя
            </button>
            <span class="text-gray-800 font-semibold whitespace-nowrap">
                {{ \Carbon\Carbon::parse($weekDays[0]['date'])->format('d.m') }}
                –
                {{ \Carbon\Carbon::parse($weekDays[6]['date'])->format('d.m.Y') }}
            </span>
            <button wire:click="nextWeek"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
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
                        <div class="absolute z-20 rounded-md p-1 text-xs text-white bg-indigo-500 hover:bg-indigo-600
                                    transition overflow-hidden"
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
</div>