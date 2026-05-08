<div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <div class="booking-container">
        <div class="header">
            <h1>Бронирование аудитории</h1>
            <p class="subtitle">
                Заполните форму, отправьте заявку и мы её рассмотрим
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="submit">

            <div class="mb-4">
                <label class="form-label">Аудитория</label>
                <select wire:model.live="classroom_id" class="form-control" required>
                    <option value="">Выберите аудиторию</option>

                    @foreach ($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">
                            {{ $classroom->room }}
                        </option>
                    @endforeach
                </select>

                <small class="text-muted">Выберите из списка</small>
            </div>

            <div class="row mb-4">
                
                <div class="col-md-6">
                    <label class="form-label required">Дата бронирования</label>
                    <div wire:ignore>
                        <input
                            type="text"
                            id="event_date"
                            class="form-control flatpickr"
                            placeholder="ДД.ММ.ГГГГ"
                            required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Время бронирования</label>
                    <div class="time-inputs">
                        <input
                            type="text"
                            id="start_time"
                            class="form-control"
                            placeholder="14:30"
                            required>

                        <span class="time-separator">—</span>

                        <input
                            type="text"
                            id="end_time"
                            class="form-control"
                            placeholder="16:00"
                            required>
                    </div>
                </div>
            </div>

            @if(count($busySlots))
                <div class="alert alert-info">
                    <b>⛔ Занятые слоты:</b>
                    <br>

                    @foreach($busySlots as $slot)
                        <div>
                            {{ $slot['start_time'] }} - {{ $slot['end_time'] }}

                            @if($slot['status'] === 'pending')
                                (на рассмотрении)
                            @else
                                (занято)
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mb-4">
                <label class="form-label required">Цель бронирования</label>
                <textarea
                    wire:model="purpose"
                    class="form-control"
                    rows="3"
                    placeholder="Собрание студсовета, репетиция, занятие..."
                    required>
                </textarea>
            </div>

            <div class="equipment-section mb-4">
               
                <h5 class="mb-3">🔧 Оборудование</h5>
                <div class="mb-3">
                    <label class="form-label">Необходимое оборудование</label>
                    <textarea
                        wire:model="equipment"
                        class="form-control"
                        rows="2"
                        placeholder="Проектор, микрофоны, стулья...">
                    </textarea>
                    <small class="text-muted">Оставьте пустым, если оборудование не требуется</small>
                </div>

                <div class="tech-support-section">
                    <label class="form-label d-block mb-2">Нужен ли технический специалист?</label>
                    <div class="form-check form-check-inline">
                        <input
                            wire:model="is_tech_support"
                            class="form-check-input"
                            type="radio"
                            id="tech_support_yes"
                            value="1">
                        <label class="form-check-label" for="tech_support_yes">Да</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input
                            wire:model="is_tech_support"
                            class="form-check-input"
                            type="radio"
                            id="tech_support_no"
                            value="0">

                        <label class="form-check-label" for="tech_support_no">Нет</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Комментарий для администратора</label>
                <textarea
                    wire:model="user_comment"
                    class="form-control"
                    rows="3"
                    placeholder="Дополнительная информация, пожелания, особенности мероприятия...">
                </textarea>
            </div>

            <div class="alert alert-warning mb-4">
                <h5>📋 Правила бронирования</h5>
                <ul class="mb-0">
                    <li>Бронирование возможно только минимум за 24 часа до мероприятия</li>
                    <li>При использовании танцевального зала обязательна сменная обувь</li>
                    <li>Необходимо поддерживать чистоту после мероприятия</li>
                    <li>Мебель должна быть возвращена на свои места</li>
                    <li>Заявка будет рассмотрена администратором в течение 24 часов</li>
                </ul>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button
                    type="button"
                    class="btn btn-outline-secondary me-md-2"
                    onclick="window.location.href='/'">
                    На главную
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                    wire:loading.attr="disabled">
                    Отправить заявку
                </button>
            </div>
        </form>

        <div wire:loading class="loading">
            <div class="spinner-border text-primary loading-spinner" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
            <p class="mt-3">Отправка заявки...</p>
        </div>

        <div class="footer">
            <p>По вопросам обращайтесь: 8 (3412) 77-60-55, доб. 1371 </p>
            <p>Также вы можете подать заявку через <a href="https://vk.com" target="_blank">VK бота</a></p>
        </div>

        <div class="card p-4 mb-4">
            <h5 class="mb-3">🔍 Фильтрация заявок</h5>
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Статус</label>
                    <select wire:model.live="filterStatus" class="form-control">
                        <option value="">Все статусы</option>
                        <option value="pending">⏳ Ожидает</option>
                        <option value="approved">✅ Одобрена</option>
                        <option value="rejected">❌ Отклонена</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Дата</label>
                    <input type="text" id="filter_date" class="form-control" placeholder="ДД.ММ.ГГГГ">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Аудитория</label>

                    <select wire:model.live="filterClassroom" class="form-control">
                        <option value="">Все аудитории</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->room }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-2">
                <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm" type="button">
                    Сбросить фильтры
                </button>
            </div>
        </div>

        <div class='my-bookings mt-15'>
            <h2 class="text-xl font-bold mb-4 text-center">📋 Мои заявки</h2>

            @forelse($bookings as $booking)
                <div class="bg-white rounded-xl shadow p-4 mb-4 border">
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Аудитория {{ $booking->classroom->room }}</span>

                        <span class="text-sm
                            @if($booking->status === 'pending')
                                text-yellow-600
                            @elseif($booking->status === 'approved')
                                text-green-600
                            @elseif($booking->status === 'rejected')
                                text-red-600
                            @endif
                            ">

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

                    <div class="mt-2">
                        <b>Цель:</b>
                        {{ $booking->purpose }}
                    </div>

                    @if($booking->user_comment)
                        <div class="text-gray-500 text-sm mt-1">
                            {{ $booking->user_comment }}
                        </div>
                    @endif

                    @if($booking->admin_comment)
                        <div class="text-gray-500 text-sm mt-1">
                            <b>Комментарий администратора:</b>
                            {{ $booking->admin_comment }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-500">У вас пока нет заявок</div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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

            function setupTimeMask(element, field) {
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
                    @this.set(field, e.target.value);
                });

            }

            setupTimeMask(
                document.getElementById('start_time'),
                'start_time'
            );

            setupTimeMask(
                document.getElementById('end_time'),
                'end_time'
            );

        });
    </script>
</div>