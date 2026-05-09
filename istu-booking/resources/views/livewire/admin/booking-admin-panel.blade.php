<div class="min-h-screen bg-[#f5f7fa] text-[#333] font-['Segoe_UI']">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    <div class="flex-1 p-[30px] overflow-y-auto">

        <h1 class="text-[1.5rem] mb-5 pb-[10px] border-b-2 border-[#1a2a6c] text-[#1a2a6c] font-bold">Заявки</h1>

        @if(session()->has('success'))
            <div class="mb-5 bg-green-500 text-white px-5 py-4 rounded-[10px] shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-5">
            <a href="{{ route('admin.classrooms') }}"
               class="inline-block mr-[10px] px-6 py-3 text-white rounded-[10px] text-[16px] font-semibold
                      transition-all duration-300 shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br
                      from-[#1A2A6C] to-[#3456DB]
                      hover:-translate-y-[2px] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]">
                Аудитории
            </a>
        </div>

        <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.08)] p-5 mb-5">
            <div class="flex flex-wrap gap-4 items-end mb-[20px]">

                <div class="flex flex-col">
                    <label class="mb-2 font-semibold text-[#1a2a6c]">
                        Дата
                    </label>

                    <div wire:ignore>
                        <input
                            type="text"
                            id="admin_filter_date"
                            placeholder="ДД.ММ.ГГГГ"
                            class="px-4 py-3 border-2 border-[#e0e0e0] rounded-[10px]
                                focus:outline-none focus:border-[#3456db]
                                transition-all duration-300 bg-white min-w-[220px]">
                    </div>
                </div>

                <div class="flex flex-col min-w-[220px]">
                    <label class="mb-2 font-semibold text-[#1a2a6c]">
                        Аудитория
                    </label>

                    <select
                        wire:model.live="selectedClassroom"
                        class="px-4 py-3 border-2 border-[#e0e0e0] rounded-[10px]
                            focus:outline-none focus:border-[#3456db]
                            transition-all duration-300">

                        <option value="">Все аудитории</option>

                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">
                                {{ $classroom->room }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    wire:click="resetFilters"
                    class="px-6 py-3 text-white rounded-[10px] text-[16px]
                        font-semibold transition-all duration-300
                        shadow-[0_4px_15px_rgba(231,76,60,0.3)]
                        bg-gradient-to-br from-[#c2433a] to-[#EB4C42]
                        hover:-translate-y-[2px] cursor-pointer
                        hover:shadow-[0_6px_20px_rgba(231,76,60,0.45)]">
                    Сбросить
                </button>
            </div>

            <div class="flex flex-wrap gap-[10px] mb-5">
                <button
                    wire:click="setStatus('')"
                    class="px-5 py-[10px] rounded-[5px] font-bold transition-all duration-300 cursor-pointer
                        {{ !$status
                            ? 'bg-[#1a2a6c] text-white'
                            : 'bg-[#e0e0e0] text-black'
                        }}">
                    Все
                </button>

                <button
                    wire:click="setStatus('pending')"
                    class="px-5 py-[10px] rounded-[5px] font-bold transition-all duration-300 cursor-pointer
                        {{ $status === 'pending'
                            ? 'bg-[#1a2a6c] text-white'
                            : 'bg-[#e0e0e0] text-black'
                        }}">
                    В ожидании
                </button>

                <button
                    wire:click="setStatus('approved')"
                    class="px-5 py-[10px] rounded-[5px] font-bold transition-all duration-300 cursor-pointer
                        {{ $status === 'approved'
                            ? 'bg-[#1a2a6c] text-white'
                            : 'bg-[#e0e0e0] text-black'
                        }}">
                    Одобренные
                </button>

                <button
                    wire:click="setStatus('rejected')"
                    class="px-5 py-[10px] rounded-[5px] font-bold transition-all duration-300 cursor-pointer
                        {{ $status === 'rejected'
                            ? 'bg-[#1a2a6c] text-white'
                            : 'bg-[#e0e0e0] text-black'
                        }}">
                    Отклонённые
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-[25px] p-5">
            @forelse($bookings as $booking)
                <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.1)] p-5 transition-all duration-300
                            hover:-translate-y-[5px] hover:shadow-[0_8px_25px_rgba(0,0,0,0.15)]
                            flex flex-col justify-between">

                    <div>
                        <div class="flex justify-between items-center mb-[15px]">
                            <div class="text-[0.9rem] text-[#7f8c8d]">
                                ID: {{ $booking->id }}
                            </div>
                        </div>

                        <div class="text-[1.3rem] font-bold text-[#1a2a6c] mb-[15px]">
                            Аудитория: {{ $booking->classroom->room }}
                        </div>

                        <div class="space-y-[8px]">
                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">ФИО: </div>
                                <div>{{ $booking->user->name }}</div>
                            </div>

                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Группа: </div>
                                <div>{{ $booking->user->group }}</div>
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
                                <div class="text-right">{{ $booking->equipment ?: '-' }}</div>
                            </div>

                            <div class="flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Тех. специалист: </div>
                                <div>{{ $booking->is_tech_support ? 'Да' : 'Нет' }}</div>
                            </div>

                            <div class="flex justify-between gap-5">
                                <div class="font-bold text-[#7f8c8d]">Комментарий студента: </div>
                                <div class="text-right break-words">{{ $booking->user_comment ?: '-' }}</div>
                            </div>

                            <div class="flex justify-between gap-5">
                                <div class="font-bold text-[#7f8c8d]">Комментарий администратора: </div>
                                <div class="text-right break-words">{{ $booking->admin_comment ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($booking->status === 'pending')
                        <div class="mt-5">
                            <textarea
                                wire:model.live="adminComments.{{ $booking->id }}"
                                placeholder="Оставить комментарий..."
                                class="w-full
                                       min-h-[60px] max-h-[300px] p-3 border-2 border-[#ddd] rounded-[8px] text-[14px]
                                       leading-[1.5] resize-none transition-all duration-300 focus:outline-none
                                       focus:border-primary focus:shadow-[0_0_0_3px_rgba(0,123,255,0.1)]">
                            </textarea>

                            <div class="flex gap-[10px] mt-[15px]">
                                <button
                                    wire:click="updateStatus({{ $booking->id }}, 'approved')"
                                    class="flex-1 px-[15px] py-[8px] rounded-[5px] font-bold
                                           transition-all duration-200 bg-[#27ae60] text-white hover:opacity-90">
                                    Принять
                                </button>

                                <button
                                    wire:click="updateStatus({{ $booking->id }}, 'rejected')"
                                    class="flex-1 px-[15px] py-[8px] rounded-[5px] font-bold
                                           transition-all duration-200 bg-[#e74c3c] text-white hover:opacity-90">
                                    Отклонить
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="mt-[30px] text-center text-[18px] font-bold">
                            @if($booking->status === 'approved')
                                <div class="py-3">
                                    ✅ Одобрена
                                </div>
                            @elseif($booking->status === 'rejected')
                                <div class="py-3">
                                    ❌ Отклонена
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-2xl font-bold">Заявок нет</div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <script>
        document.addEventListener('livewire:init', () => {

            flatpickr("#admin_filter_date", {
                dateFormat: "d.m.Y",
                locale: "ru",

                onChange: function(selectedDates, dateStr) {
                    @this.set('selectedDate', dateStr);
                }
            });

            Livewire.on('resetFilterDate', () => {
                const fp =
                    document.querySelector("#admin_filter_date")._flatpickr;

                if (fp) {
                    fp.clear();
                }
            });

        });
    </script>
</div>