<div class="min-h-screen bg-[#f8f9fc] font-sans pt-8 pb-10 px-4">
    <div class="max-w-[800px] mx-auto bg-white rounded-[15px] shadow-[0_0_30px_rgba(0,0,0,0.1)] p-6 md:p-10">

        @if($submitted)
            <div class="text-center py-12">
                <div class="text-7xl mb-6">✅</div>
                <h2 class="text-2xl font-bold text-green-600 mb-4">Заявка успешно создана</h2>
                <p class="text-gray-600 mb-8">Скоро мы её рассмотрим. Ожидайте уведомления.</p>
                <x-button wire:click="resetForm">Подать новую заявку</x-button>
            </div>
        @else

        <div class="text-center mb-4 border-b-[3px] border-primary pb-5">
            <h1 class="text-primary font-bold text-3xl md:text-4xl mb-2">Бронирование молодёжных пространств СЦ "Интеграл"</h1>
            <p class="text-secondary text-[1.1rem]">Заполните форму, отправьте заявку и мы её рассмотрим</p>
        </div>

        <div class="mb-4">
            <button wire:click="openSettingsModal" type="button"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100
                    transition-colors font-semibold cursor-pointer">
                ⚙️ Настройки
            </button>

            <a href="{{ route('schedule') }}"
                class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100
                        transition-colors font-semibold">
                📅 Расписание
            </a>
        </div>

        <div class="bg-warning text-[#684F06] rounded-[10px] p-5 mb-4">
            <h5 class="font-bold text-lg mb-3">📋 Правила бронирования</h5>
            <ul class="space-y-1 list-disc pl-5">
                <li>Бронирование возможно минимум за 24 часа до мероприятия</li>
                <li>При использовании танцевального зала обязательна сменная обувь</li>
                <li>Необходимо поддерживать чистоту после мероприятия</li>
                <li>Мебель должна быть возвращена на свои места</li>
            </ul>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-100 text-green-700 rounded-[10px] p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="submit">

            <div class="mb-4">
                <label class="block font-semibold text-[#495057] mb-2">
                    Пространство
                    <span class="text-danger">*</span>
                </label>
                <x-select wire:model.live="classroom_id" required>
                    <option value="">Выберите пространство</option>
                    @foreach ($classrooms->where('is_hidden', false) as $classroom)
                        <option value="{{ $classroom->id }}">
                            {{ $classroom->room }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            
            @if($selectedClassroom)
                <section class="mb-5 overflow-hidden rounded-2xl border border-[#e3e6f0] bg-white shadow-sm transition-all duration-300 hover:shadow-md">
                    <div class="relative h-52 sm:h-64 overflow-hidden bg-gradient-to-br from-[#eef2ff] to-[#f8f9fc]">
                        @if($selectedClassroom['photo'])
                            <img
                                src="{{ $selectedClassroom['photo'] }}"
                                alt="Фотография пространства {{ $selectedClassroom['room'] }}"
                                loading="lazy"
                                decoding="async"
                                class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center gap-3 text-[#3456DB]">
                                <span class="text-6xl">🏫</span>
                            </div>
                        @endif

                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 via-black/35 to-transparent px-5 pb-5 pt-12">
                            <div class="flex flex-wrap items-end justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="mb-1 text-xs font-medium uppercase tracking-wider text-white/75">
                                        Выбранное пространство
                                    </p>
                                    <h3 class="text-2xl font-bold text-white drop-shadow-sm sm:text-3xl">
                                        {{ $selectedClassroom['room'] }}
                                    </h3>
                                </div>

                                @if($selectedClassroom['category'])
                                    <div>
                                        <span class="mr-2 rounded-lg border border-white/30 bg-white/15 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                                            {{ $selectedClassroom['category'] }}
                                        </span>
                                        <span class="rounded-lg border border-white/30 bg-white/15 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                                            {{ $selectedClassroom['capacity']
                                                ? 'До ' . $selectedClassroom['capacity'] . ' человек'
                                                : 'Вместимость не указана' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="px-7 py-4">
                        <div class="mb-3 flex flex-col">
                            <h4 class="font-bold text-primary">Описание</h4>
                            <p class="break-words text-sm leading-6 text-gray-600">
                                {{ $selectedClassroom['description'] ?: 'Описание отсутствует' }}
                            </p>
                        </div>
                        <div class="flex flex-col">
                            <h4 class="font-bold text-primary">Оборудование</h4>
                            <p class="break-words text-sm leading-6 text-gray-600">
                                {{ $selectedClassroom['equipment'] ?: 'Информация об оборудовании не указана' }}
                            </p>
                        </div>
                    </div>
                </section>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block font-semibold text-[#495057] mb-2">
                        Дата бронирования
                        <span class="text-danger">*</span>
                    </label>

                    <div wire:ignore>
                        <x-input id="event_date" placeholder="ДД.ММ.ГГГГ" autocomplete="off" required/>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-[#495057] mb-2">
                        Время бронирования
                        <span class="text-danger">*</span>
                    </label>

                    <div class="flex flex-col md:flex-row gap-5 items-center">
                        <x-input wire:model="start_time" id="start_time" placeholder="__:__"
                                    inputmode="numeric" autocomplete="off" maxlength="5" required/>
                        <span class="text-xl font-bold text-secondary">—</span>
                        <x-input wire:model="end_time" id="end_time" placeholder="__:__"
                                    inputmode="numeric" autocomplete="off" maxlength="5" required/>
                    </div>
                </div>
            </div>

            @if(count($busySlots))
                <div class="bg-blue-100 text-blue-800 rounded-[10px] p-4 mb-4">
                    <b>Занятые временные слоты:</b>
                    <div class="mt-2 space-y-1">
                        @foreach($busySlots as $slot)
                            <div>
                                {{ $slot['start_time'] }} - {{ $slot['end_time'] }}
                                {{ $slot['status'] === 'pending' ? '(на рассмотрении)' : '(занято)' }}
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
                <x-input wire:model="purpose" type="textarea" placeholder="Собрание студсовета, репетиция, совещание..." rows="3" required/>
            </div>

            <div class="bg-[#f8f9fa] p-5 rounded-[10px] mb-4">
                <h5 class="font-[600] text-lg mb-4">🔧 Техническое сопровождение</h5>
                <div class="mb-4">
                    <label class="block font-semibold text-[#495057] mb-2">Необходимое оборудование</label>
                    <x-input wire:model="equipment" type="textarea" placeholder="Проектор, микрофоны, стулья..." rows="2"/>
                    <small class="text-secondary text-sm">Оставьте пустым, если оборудование не требуется</small>
                </div>

                <div class="bg-[#f8f9fa] rounded-[10px]">
                    <label class="block font-semibold text-[#495057] mb-3">Нужен ли технический специалист?</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                wire:model="is_tech_support" type="radio" value="1"
                                class="w-4 h-4 text-primary accent-primary border-gray-300 focus:ring-primary">
                            <span>Да</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                wire:model="is_tech_support" type="radio" value="0"
                                class="w-4 h-4 text-primary accent-primary border-gray-300 focus:ring-primary">
                            <span>Нет</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-semibold text-[#495057] mb-2">Комментарий для администратора</label>
                <x-input wire:model="user_comment" type="textarea" placeholder="Дополнительная информация, пожелания, особенности мероприятия..." rows="3"/>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 rounded-[10px] p-4 mb-4">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-3 md:justify-end">
                <x-button type="submit" wire:loading.attr="disabled">Отправить заявку</x-button>
            </div>
        </form>
        @endif
        
        <div wire:loading.flex class="fixed inset-0 bg-[rgba(255,255,255,0.7)] z-[9999] flex-col justify-center items-center">
            <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-3 text-primary font-semibold">Загрузка...</p>
        </div>

        <div class="text-center mt-[30px] pt-5 mb-8 border-t border-[#e3e6f0] text-secondary text-[0.9rem]">
            <p>По вопросам обращайтесь: 8 (3412) 77-60-55, доб. 1371</p>
            <p class="mt-2">
                Также вы можете подать заявку через
                <a href="https://vk.com" target="_blank" class="text-primary hover:underline">VK бота</a>
            </p>
        </div>

        @if($loadError)
            <div class="bg-red-100 border border-red-300 text-red-700 rounded-[10px] p-4 mb-6">
                ❌ Не удалось загрузить данные. Попробуйте позже.
            </div>
        @endif

        <div class="mt-10">
            <h2 class="text-2xl font-bold mb-6 text-center">📋 Мои заявки</h2>

            @if($bookingsSuccess)
                <div class="bg-green-100 text-green-700 rounded-[10px] p-4 mb-4">{{ $bookingsSuccess }}</div>
            @endif
            @if($bookingsError)
                <div class="bg-red-100 text-red-700 rounded-[10px] p-4 mb-4">{{ $bookingsError }}</div>
            @endif

            <x-filter :classrooms="$classrooms"/>

            @forelse($bookings as $booking)
                <div class="bg-white rounded-xl shadow p-4 mb-4 border border-[#e3e6f0]">
                    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                ID: {{ $booking->id }}
                            </span>
                        </div>

                        <span
                            class="text-sm font-semibold

                            @if($booking->status === 'pending')
                                text-yellow-600
                            @elseif($booking->status === 'approved')
                                text-green-600
                            @elseif($booking->status === 'rejected' || $booking->status === 'cancelled')
                                text-red-600
                            @endif">

                            {{ match($booking->status) {
                                'pending' => '⏳ Ожидает',
                                'approved' => '✅ Одобрена',
                                'rejected' => '❌ Отклонена',
                                'cancelled' => $booking->cancelled_by === 'student' ? '↩️ Отозвана вами' : '❌ Отменена',
                            } }}
                        </span>
                    </div>

                    <div class="font-semibold">Пространство {{ $booking->classroom->room }}</div>

                    <div class="text-sm text-gray-600 mt-2">
                        📅 {{ $booking->date }}
                        |
                        ⏰ {{ $booking->start_time }}
                        -
                        {{ $booking->end_time }}
                    </div>

                    <div class="mt-3"><b>Цель:</b> {{ $booking->purpose }}</div>

                    @if($booking->user_comment)
                        <div class="text-gray-500 text-sm mt-2">{{ $booking->user_comment }}</div>
                    @endif

                    @if($booking->admin_comment)
                        <div class="text-gray-500 text-sm mt-2">
                            <b>Комментарий администратора:</b>
                            {{ $booking->admin_comment }}
                        </div>
                    @endif

                    @if($booking->status === 'cancelled' && $booking->cancel_reason)
                        <div class="text-red-600 text-sm mt-2">
                            <b>Причина отмены:</b>
                            {{ $booking->cancel_reason }}
                        </div>
                    @endif

                    @if($booking->isWithdrawable())
                        <div class="mt-3">
                            <button wire:click="openWithdrawModal({{ $booking->id }})" type="button"
                                    class="text-[#d64545] text-sm font-semibold hover:text-[#bb2d2d] transition-colors cursor-pointer">
                                Отозвать заявку
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-500 text-center">Заявок не найдено</div>
            @endforelse

            @if($hasMoreBookings)
                <div class="flex justify-center mt-6">
                    <x-button wire:click="loadMore">Показать ещё</x-button>
                </div>
            @endif
        </div>

        @if($showWithdrawModal)
            <x-modal title="Отзыв заявки">
                <p class="text-gray-600">
                    Вы уверены, что хотите отозвать заявку? Администратор получит уведомление.
                    Если бронь уже была одобрена, она будет снята, а время освободится для других.
                </p>

                <x-slot name="footer">
                    <x-button wire:click="withdrawBooking" color="red">Отозвать заявку</x-button>
                    <x-button wire:click="closeWithdrawModal" color="gray">Оставить</x-button>
                </x-slot>
            </x-modal>
        @endif

        @if($showSettingsModal)
            <x-modal title="Настройки">
                <label class="block font-semibold text-[#495057] mb-2">Привязанная страница ВКонтакте</label>
                @if($userVkLink)
                    <a href="{{ $userVkLink }}" target="_blank" rel="noopener noreferrer"
                    class="inline-block break-all text-primary hover:underline">
                        {{ $userVkLink }}
                    </a>
                @else
                    <span class="text-gray-500 text-sm">Страница ВКонтакте не привязана</span>
                @endif

                <label class="block font-semibold text-[#495057] mb-2 mt-4">
                    Номер телефона <span class="text-red-500">*</span>
                </label>
                <x-input wire:model="settingsPhone" placeholder="+7 (999) 123-45-67"/>
                @error('settingsPhone')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                <x-slot name="footer">
                    <x-button wire:click="saveSettings">Сохранить</x-button>
                    <x-button wire:click="closeSettingsModal" color="gray">Отмена</x-button>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            let datePicker = null;

            function initDatePicker() {
                const dateInput = document.getElementById('event_date');

                if (!dateInput) {
                    return;
                }

                if (datePicker) {
                    datePicker.destroy();
                    datePicker = null;
                }

                datePicker = flatpickr(dateInput, {
                    dateFormat: 'd.m.Y',
                    locale: 'ru',
                    minDate: 'today',
                    maxDate: @json($maxDate),
                    disableMobile: true,
                    allowInput: true,
                    onChange: function(selectedDates, dateStr) {
                        const component = Livewire.find(
                            dateInput.closest('[wire\\:id]').getAttribute('wire:id')
                        );

                        if (component) {
                            component.set('date', dateStr);
                        }
                    }
                });
            }

            function setupTimeMask(element) {
                if (!element) {
                    return;
                }

                if (element.dataset.timeMaskInitialized === 'true') {
                    return;
                }

                element.dataset.timeMaskInitialized = 'true';

                function formatTime(digits, isDeleting, hadColon) {
                    if (isDeleting && !hadColon && digits.length === 2) {
                        digits = digits.substring(0, 1);
                    }

                    let hours = digits.substring(0, 2);
                    let minutes = digits.substring(2, 4);

                    if (hours.length === 2 && parseInt(hours, 10) > 23) {
                        hours = '23';
                    }

                    if (minutes.length === 2 && parseInt(minutes, 10) > 59) {
                        minutes = '59';
                    }

                    if (minutes.length > 0) {
                        return hours + ':' + minutes;
                    }

                    if (hours.length === 2) {
                        return (!isDeleting || hadColon) ? hours + ':' : hours;
                    }

                    return hours;
                }

                function caretFromDigits(formatted, digitsBefore) {
                    if (digitsBefore <= 0) {
                        return 0;
                    }

                    let count = 0;

                    for (let i = 0; i < formatted.length; i++) {
                        if (/\d/.test(formatted[i])) {
                            count++;

                            if (count === digitsBefore) {
                                return formatted[i + 1] === ':' ? i + 2 : i + 1;
                            }
                        }
                    }

                    return formatted.length;
                }

                element.addEventListener('input', function (e) {
                    const input = e.target;
                    const isDeleting = typeof e.inputType === 'string' && e.inputType.startsWith('delete');
                    const hadColon = input.value.includes(':');

                    const caret = input.selectionStart ?? input.value.length;
                    let digitsBefore = input.value.slice(0, caret).replace(/\D/g, '').length;

                    let digits = input.value.replace(/\D/g, '');

                    if (!isDeleting && digits.length > 0 && digits[0] > '2') {
                        digits = '0' + digits;
                        digitsBefore += 1;
                    }

                    digits = digits.substring(0, 4);

                    const formatted = formatTime(digits, isDeleting, hadColon);

                    if (input.value === formatted) {
                        return;
                    }

                    input.value = formatted;

                    let newCaret = caretFromDigits(formatted, digitsBefore);

                    if (isDeleting && formatted[newCaret - 1] === ':') {
                        newCaret--;
                    }

                    input.setSelectionRange(newCaret, newCaret);

                    input.dispatchEvent(new Event('input', { bubbles: true }));
                });

                element.addEventListener('keydown', function (e) {
                    const allowedKeys = ['Backspace', 'Delete',
                                         'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                                         'Tab', 'Home', 'End', 'Enter'
                    ];

                    if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) {
                        return;
                    }

                    if (!/^\d$/.test(e.key)) {
                        e.preventDefault();
                    }
                });
            }

            function initTimeMasks() {
                setupTimeMask(document.getElementById('start_time'));
                setupTimeMask(document.getElementById('end_time'));
            }

            function initBookingForm() {
                requestAnimationFrame(() => {
                    initDatePicker();
                    initTimeMasks();
                });
            }

            initBookingForm();

            Livewire.on('booking-form-reset', () => {
                initBookingForm();
            });
        });
    </script>
</div>