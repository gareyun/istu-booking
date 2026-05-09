<div class="min-h-screen bg-[#f8f9fc] font-sans py-10 px-4">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <div class="max-w-[800px] mx-auto bg-white rounded-[15px] shadow-[0_0_30px_rgba(0,0,0,0.1)] p-6 md:p-10">
        
        <div class="text-center mb-10 border-b-[3px] border-primary pb-5">
            <h1 class="text-primary font-bold text-3xl md:text-4xl mb-2">Бронирование аудитории</h1>
            <p class="text-secondary text-[1.1rem]">Заполните форму, отправьте заявку и мы её рассмотрим</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 rounded-[10px] p-4 mb-4">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session()->has('success'))
            <div class="bg-green-100 text-green-700 rounded-[10px] p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="submit">

            <div class="mb-4">
                <label class="block font-semibold text-[#495057] mb-2">
                    Аудитория
                    <span class="text-danger">*</span>
                </label>

                <div class="select-block relative">
                    <select
                        wire:model.live="classroom_id"
                        class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                            focus:border-primary focus:ring-4 appearance-none focus:ring-[rgba(78,115,223,0.25)]
                            outline-none bg-white cursor-pointer"
                        required>

                        <option value="">Выберите аудиторию</option>

                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">
                                {{ $classroom->room }}
                            </option>
                        @endforeach
                    </select>

                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                
                <div>
                    <label class="block font-semibold text-[#495057] mb-2">
                        Дата бронирования
                        <span class="text-danger">*</span>
                    </label>

                    <div wire:ignore>
                        <input
                            type="text"
                            id="event_date"
                            class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                                   focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                                   outline-none bg-white"
                            placeholder="ДД.ММ.ГГГГ"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-[#495057] mb-2">
                        Время бронирования
                        <span class="text-danger">*</span>
                    </label>

                    <div class="flex flex-col md:flex-row gap-5 items-center">
                        <input
                            type="text"
                            id="start_time"
                            wire:model="start_time"
                            class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                                   focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                                   outline-none bg-white"
                            placeholder="14:30"
                            required>

                        <span class="text-xl font-bold text-secondary">—</span>

                        <input
                            type="text"
                            id="end_time"
                            wire:model="end_time"
                            class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                                   focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                                   outline-none bg-white"
                            placeholder="16:00"
                            required>
                    </div>
                </div>
            </div>

            @if(count($busySlots))
                <div class="bg-blue-100 text-blue-800 rounded-[10px] p-4 mb-4">
                    <b>⛔ Занятые слоты:</b>
                    <div class="mt-2 space-y-1">
                        @foreach($busySlots as $slot)
                            <div>
                                {{ $slot['start_time'] }}
                                -
                                {{ $slot['end_time'] }}

                                @if($slot['status'] === 'pending')
                                    (на рассмотрении)
                                @else
                                    (занято)
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-4">
                <label class="block font-semibold text-[#495057] mb-2">
                    Цель бронирования
                    <span class="text-danger">*</span>
                </label>

                <textarea
                    wire:model="purpose"
                    rows="3"
                    class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                        focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                        outline-none bg-white"
                    placeholder="Собрание студсовета, репетиция, занятие..."
                    required></textarea>
            </div>

            <div class="bg-[#f8f9fa] p-5 rounded-[10px] mb-4">
                <h5 class="font-[600] text-lg mb-4">🔧 Оборудование</h5>
                
                <div class="mb-4">
                    <label class="block font-semibold text-[#495057] mb-2">Необходимое оборудование</label>
                    <textarea
                        wire:model="equipment"
                        rows="2"
                        class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                               focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                               outline-none bg-white"
                        placeholder="Проектор, микрофоны, стулья..."
                    ></textarea>

                    <small class="text-secondary text-sm">Оставьте пустым, если оборудование не требуется</small>
                </div>

                <div class="bg-[#f8f9fa] rounded-[10px]">
                    <label class="block font-semibold text-[#495057] mb-3">Нужен ли технический специалист?</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                wire:model="is_tech_support"
                                type="radio"
                                value="1"
                                class="w-4 h-4 text-primary accent-primary border-gray-300 focus:ring-primary">

                            <span>Да</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                wire:model="is_tech_support"
                                type="radio"
                                value="0"
                                class="w-4 h-4 text-primary accent-primary border-gray-300 focus:ring-primary">

                            <span>Нет</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-semibold text-[#495057] mb-2">Комментарий для администратора</label>

                <textarea
                    wire:model="user_comment"
                    rows="3"
                    class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                        focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                           outline-none bg-white"
                    placeholder="Дополнительная информация, пожелания, особенности мероприятия..."
                ></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold text-[#495057] mb-2">
                    Ссылка на VK (для уведомлений)
                </label>
                
                <input
                    type="text"
                    wire:model="vk_link"
                    class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] 
                        transition-all duration-300 focus:border-primary focus:ring-4 
                        focus:ring-[rgba(78,115,223,0.25)] outline-none bg-white"
                    placeholder="https://vk.com/id123456789 или https://vk.com/username">
                
                <small class="text-secondary text-sm mt-1 block">
                    Укажите ссылку на вашу страницу ВКонтакте, чтобы получать уведомления о статусе заявки
                </small>
            </div>

            <div class="bg-warning text-[#684F06] rounded-[10px] p-5 mb-4">
                <h5 class="font-bold text-lg mb-3">📋 Правила бронирования</h5>

                <ul class="space-y-1 list-disc pl-5">
                    <li>Бронирование возможно только минимум за 24 часа до мероприятия</li>
                    <li>При использовании танцевального зала обязательна сменная обувь</li>
                    <li>Необходимо поддерживать чистоту после мероприятия</li>
                    <li>Мебель должна быть возвращена на свои места</li>
                    <li>Заявка будет рассмотрена администратором в течение 24 часов</li>
                </ul>
            </div>

            <div class="flex flex-col md:flex-row gap-3 md:justify-end">
                <button
                    type="button"
                    onclick="window.location.href='/'"
                    class="border border-gray-300 hover:bg-gray-100 text-gray-700 px-6 py-3
                           rounded-[8px] transition-all duration-300 cursor-pointer">
                    На главную
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-primary hover:bg-[#3a56c4] text-white font-semibold px-8 py-3
                           rounded-[8px] transition-all duration-300 hover:-translate-y-[2px] cursor-pointer">
                    Отправить заявку
                </button>
            </div>

        </form>

        <div wire:loading.flex class="fixed inset-0 bg-[rgba(255,255,255,0.7)] z-[9999] flex-col justify-center items-center">
            <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-3 text-primary font-semibold">Загрузка...</p>
        </div>

        <div class="text-center mt-[30px] pt-5 border-t border-[#e3e6f0] text-secondary text-[0.9rem]">
            <p>По вопросам обращайтесь: 8 (3412) 77-60-55, доб. 1371</p>
            <p class="mt-2">
                Также вы можете подать заявку через
                <a href="https://vk.com" target="_blank" class="text-primary hover:underline">
                    VK бота
                </a>
            </p>
        </div>

        <div class="bg-white border border-[#e3e6f0] rounded-[10px] p-5 mt-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div>
                    <label class="block font-semibold text-[#495057] mb-2">Статус</label>

                    <div class="select-block relative">
                        <select
                            wire:model.live="filterStatus"
                            class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                                focus:border-primary focus:ring-4 appearance-none focus:ring-[rgba(78,115,223,0.25)]
                                outline-none bg-white cursor-pointer">

                            <option value="">Все статусы</option>
                            <option value="pending">⏳ Ожидает</option>
                            <option value="approved">✅ Одобрена</option>
                            <option value="rejected">❌ Отклонена</option>
                        </select>

                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-[#495057] mb-2">Дата</label>
                    <input
                        type="text"
                        id="filter_date"
                        placeholder="ДД.ММ.ГГГГ"
                        class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                               focus:border-primary focus:ring-4 focus:ring-[rgba(78,115,223,0.25)]
                               outline-none bg-white">
                </div>

                <div>
                    <label class="block font-semibold text-[#495057] mb-2">Аудитория</label>

                    <div class="select-block relative">
                        <select
                            wire:model.live="filterClassroom"
                            class="w-full border-2 border-[#e3e6f0] rounded-[8px] px-[15px] py-[12px] transition-all duration-300
                                focus:border-primary focus:ring-4 appearance-none focus:ring-[rgba(78,115,223,0.25)]
                                outline-none bg-white cursor-pointer">

                            <option value="">Все аудитории</option>

                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">
                                    {{ $classroom->room }}
                                </option>
                            @endforeach
                        </select>

                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button
                    wire:click="resetFilters"
                    type="button"
                    class="px-6 py-3 text-white rounded-[10px] text-[16px]
                        font-semibold transition-all duration-300
                        shadow-[0_4px_15px_rgba(231,76,60,0.3)]
                        bg-gradient-to-br from-[#c2433a] to-[#EB4C42]
                        hover:-translate-y-[2px] cursor-pointer
                        hover:shadow-[0_6px_20px_rgba(231,76,60,0.45)]">
                    Сбросить
                </button>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-2xl font-bold mb-6 text-center">📋 Мои заявки</h2>

            @forelse($bookings as $booking)
                <div class="bg-white rounded-xl shadow p-4 mb-4 border border-[#e3e6f0]">
                    
                    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-2">
                        <span class="font-semibold">
                            Аудитория
                            {{ $booking->classroom->room }}
                        </span>

                        <span
                            class="text-sm font-semibold

                            @if($booking->status === 'pending')
                                text-yellow-600
                            @elseif($booking->status === 'approved')
                                text-green-600
                            @elseif($booking->status === 'rejected')
                                text-red-600
                            @endif">

                            {{ match($booking->status) {
                                'pending' => '⏳ Ожидает',
                                'approved' => '✅ Одобрена',
                                'rejected' => '❌ Отклонена',
                            } }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600">
                        📅 {{ $booking->date }}
                        |
                        ⏰ {{ $booking->start_time }}
                        -
                        {{ $booking->end_time }}
                    </div>

                    <div class="mt-3">
                        <b>Цель:</b> {{ $booking->purpose }}
                    </div>

                    @if($booking->user_comment)
                        <div class="text-gray-500 text-sm mt-2">
                            {{ $booking->user_comment }}
                        </div>
                    @endif

                    @if($booking->admin_comment)
                        <div class="text-gray-500 text-sm mt-2">
                            <b>Комментарий администратора:</b>
                            {{ $booking->admin_comment }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-500 text-center">Заявок не найдено</div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            flatpickr("#event_date", {
                dateFormat: "d.m.Y",
                locale: "ru",
                minDate: "today",

                onChange: function(selectedDates, dateStr) {
                    @this.set('date', dateStr);
                }
            });

            flatpickr("#filter_date", {
                dateFormat: "d.m.Y",
                locale: "ru",

                onChange: function(selectedDates, dateStr) {
                    @this.set('filterDate', dateStr);
                }
            });

            Livewire.on('resetFilterDate', () => {
                const fp = document.querySelector("#filter_date")._flatpickr;

                if (fp) {
                    fp.clear();
                }
            });

            function setupTimeMask(element) {
                element.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');

                    if (value.length >= 3) {
                        value =
                            value.substring(0, 2)
                            + ':'
                            + value.substring(2, 4);
                    } else if (value.length >= 1) {
                        if (parseInt(value) > 23) {
                            value = '23';
                        }
                    }

                    e.target.value = value.substring(0, 5);
                });
            }

            setupTimeMask(
                document.getElementById('start_time')
            );

            setupTimeMask(
                document.getElementById('end_time')
            );
        });
    </script>
</div>