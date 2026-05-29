<div class="min-h-screen bg-[#f5f7fa] text-[#333] font-['Segoe_UI']">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    <div class="flex-1 p-[30px] overflow-y-auto">
        <h1 class="text-[1.5rem] mb-5 pb-[10px] border-b-2 border-[#1a2a6c] text-[#1a2a6c] font-bold">Заявки</h1>

        @if($loadError)
            <div class="mb-5 bg-red-100 border border-red-300 text-red-700 px-5 py-4 rounded-[10px]">
                ❌ Не удалось загрузить данные. Попробуйте позже.
            </div>
        @endif

        @if(session()->has('success'))
            <div class="mb-5 bg-green-500 text-white px-5 py-4 rounded-[10px] shadow">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-5 bg-red-500 text-white px-5 py-4 rounded-[10px] shadow">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-5">
            <x-button onclick="window.location.href='{{ route('admin.classrooms') }}'">Аудитории</x-button>
        </div>

        <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.08)] p-5 mb-5">
            <div class="flex flex-wrap gap-4 items-end mb-[20px]">

                <div class="flex flex-col min-w-[220px]">
                    <label class="mb-2 font-semibold text-[#1a2a6c]">Дата</label>
                    <div wire:ignore>
                        <x-input wire:model="description" id="admin_filter_date" placeholder="ДД.ММ.ГГГГ" class="font-medium "/>
                    </div>
                </div>

                <div class="flex flex-col min-w-[220px]">
                    <label class="mb-2 font-semibold text-[#1a2a6c]">Аудитория</label>
                    <x-select wire:model.live="selectedClassroom">
                        <option value="">Все аудитории</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">
                                {{ $classroom->room }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div class="flex flex-col min-w-[220px]">
                    <label class="mb-2 font-semibold text-[#1a2a6c]">Статус заявки</label>
                    <x-select wire:model.live="status">
                        <option value="">Все статусы</option>
                        <option value="pending">⏳ В ожидании</option>
                        <option value="approved">✅ Одобренные</option>
                        <option value="rejected">❌ Отклонённые</option>
                        <option value="cancelled">🚫 Отменённые</option>
                    </x-select>
                </div>

                <x-button wire:click="resetFilters" color="red">Сбросить</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-[25px] p-5">
            @forelse($bookings as $booking)
                <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.1)] p-5 transition-all duration-300
                            hover:-translate-y-[5px] hover:shadow-[0_8px_25px_rgba(0,0,0,0.15)]
                            flex flex-col justify-between">

                    <div>
                        <div class="flex justify-between items-center mb-[15px]">
                            <div class="text-[0.9rem] text-[#7f8c8d]">ID: {{ $booking->id }}</div>
                            @if(!$booking->user_id && $booking->vk_user_id)
                                <div class="text-xs text-gray-400 mt-2">через VK бота</div>
                            @endif
                        </div>

                        <div class="text-[1.3rem] font-bold text-[#1a2a6c] mb-[15px]">
                            Аудитория: {{ $booking->classroom->room }}
                        </div>

                        <div class="space-y-[7px]">
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
                                <div class="font-bold text-[#7f8c8d]">Факультет: </div>
                                <div>
                                    @if($booking->user)
                                        {{ $booking->user->faculty ?? '—' }}
                                    @else
                                        {{ $booking->faculty ?? '—' }}
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
                            <x-input wire:model.live="adminComments.{{ $booking->id }}" type="textarea"
                                    class="resize-none" placeholder="Оставить комментарий..."/>

                            <div class="flex gap-[10px] mt-[15px]">
                                <button
                                    wire:click="updateStatus({{ $booking->id }}, 'approved')"
                                    class="flex-1 py-2 bg-gradient-to-br from-[#1f9d55] to-[#27ae60]
                                        transition-all duration-300 shadow-[0_4px_15px_rgba(39,174,96,0.3)] text-white
                                        rounded-lg cursor-pointer font-semibold hover:shadow-[0_6px_20px_rgba(39,174,96,0.45)]">
                                    Принять
                                </button>

                                <button
                                    wire:click="updateStatus({{ $booking->id }}, 'rejected')"
                                    class="flex-1 py-2 bg-gradient-to-br from-[#c2433a] to-[#EB4C42]
                                        transition-all duration-300 shadow-[0_4px_15px_rgba(231,76,60,0.3)] text-white
                                        rounded-lg cursor-pointer font-semibold hover:shadow-[0_6px_20px_rgba(231,76,60,0.45)]">
                                    Отклонить
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="mt-[30px] text-center text-[18px] font-bold">
                            @if($booking->status === 'approved')
                                <div class="py-3 flex justify-center items-center gap-3">
                                    <div class="text-green-600 font-bold text-[18px]">✅ Одобрена</div>
                                    |
                                    <button wire:click="openCancelModal({{ $booking->id }})"
                                            class="group inline-flex items-center gap-2
                                            text-[#d64545] text-[14px] font-semibold
                                            transition-all duration-200 hover:text-[#bb2d2d]
                                            active:scale-[0.98] cursor-pointer">
                                        Отменить бронь
                                    </button>
                                </div>
                            @elseif($booking->status === 'rejected')
                                <div class="py-3">❌ Отклонена</div>
                            @elseif($booking->status === 'cancelled')
                                <div class="py-3">🚫 Отменена</div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-2xl font-bold">Заявок нет</div>
            @endforelse

            @if($hasMoreBookings)
                <div class="col-span-full flex justify-center mt-2">
                    <x-button wire:click="loadMore">Показать ещё</x-button>
                </div>
            @endif
        </div>

        @if($showCancelModal)
            <x-modal title="Подтверждение отмены">
                <p class="text-gray-600 mb-6">Вы уверены, что хотите отменить эту бронь? Действие нельзя будет отменить.</p>
                <x-slot name="footer">
                    <x-button wire:click="cancelBooking" color="red">Да, отменить</x-button>
                    <x-button wire:click="closeCancelModal" color="gray">Нет, оставить</x-button>
                </x-slot>
            </x-modal>
        @endif

        <div wire:loading.flex wire:target="updateStatus, cancelBooking"
            class="fixed inset-0 bg-[rgba(255,255,255,0.7)] z-[9999] flex-col justify-center items-center">
            <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-3 text-primary font-semibold">Загрузка...</p>
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