<x-layout>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <div class="booking-container">
        <div class="header">
            <h1>Бронирование аудитории</h1>
            <p class="subtitle">Заполните форму для бронирования аудитории</p>
        </div>

        <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
        <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

        <form id="bookingForm" method="POST" action="/bookings">
            @csrf

            <div class="mb-4">
                <label for="room" class="form-label">Аудитория</label>
                <select name="classroom_id" class="form-control" required>
                    @foreach ($classrooms as $classroom)
                        <option value="{{$classroom->id}}">{{$classroom->room}}</option>
                    @endforeach
                </select>
                <small class="text-muted">Вы можете выбрать из списка или ввести свою аудиторию</small>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="event_date" class="form-label required">Дата бронирования</label>
                    <input name="date" type="text" class="form-control flatpickr" id="event_date"
                           placeholder="ДД.ММ.ГГГГ" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Время бронирования</label>
                    <div class="time-inputs">
                        <input name="start_time" type="text" class="form-control" id="start_time"
                               placeholder="14:30" required>
                        <span class="time-separator">—</span>
                        <input name="end_time" type="text" class="form-control" id="end_time"
                               placeholder="16:00" required>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="purpose" class="form-label required">Цель бронирования</label>
                <textarea name="purpose" class="form-control" id="purpose" rows="3"
                          placeholder="Опишите цель мероприятия (собрание студсовета, репетиция, занятие и т.д.)"
                          required></textarea>
            </div>

            <div class="equipment-section mb-4">
                <h5 class="mb-3">🔧 Оборудование</h5>
                <div class="mb-3">
                    <label for="equipment" class="form-label">Необходимое оборудование</label>
                    <textarea name="equipment" class="form-control" id="equipment" rows="2"
                              placeholder="Опишите необходимое оборудование (проектор, микрофоны, стулья и т.д.)"></textarea>
                    <small class="text-muted">Оставьте пустым, если оборудование не требуется</small>
                </div>

                <div class="tech-support-section">
                    <label class="form-label d-block mb-2">Нужен ли технический специалист?</label>
                    <div class="form-check form-check-inline">
                        <input name="is_tech_support" class="form-check-input" type="radio" name="tech_support"
                               id="tech_support_yes" value="1">
                        <label class="form-check-label" for="tech_support_yes">Да</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input name="is_tech_support" class="form-check-input" type="radio" name="tech_support"
                               id="tech_support_no" value="0" checked>
                        <label class="form-check-label" for="tech_support_no">Нет</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="student_message" class="form-label">Комментарий для администратора</label>
                <textarea name="user_comment" class="form-control" id="student_message" rows="3"
                          placeholder="Дополнительная информация, пожелания, особенности мероприятия..."></textarea>
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
                <button type="button" class="btn btn-outline-secondary me-md-2"
                        onclick="window.location.href='/'">На главную</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Отправить заявку
                </button>
            </div>
        </form>

        <div id="loading" class="loading">
            <div class="spinner-border text-primary loading-spinner" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
            <p class="mt-3">Отправка заявки...</p>
        </div>

        <div class="footer">
            <p>По вопросам обращайтесь: 8 (3412) 77-60-55, доб. 1371</p>
            <p>Также вы можете подать заявку через <a href="https://vk.com" target="_blank">VK бота</a></p>
        </div>

        <div class='my-bookings mt-15'>
            <h2 class="text-xl font-bold mb-4 text-center">📋 Мои заявки</h2>

            @forelse($bookings as $booking)
                <div class="bg-white rounded-xl shadow p-4 mb-4 border">

                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">
                            Аудитория {{ $booking->classroom->room }}
                        </span>

                        <span class="text-sm
                            @if($booking->status === 'pending') text-yellow-600
                            @elseif($booking->status === 'approved') text-green-600
                            @elseif($booking->status === 'rejected') text-red-600
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
                        📅 {{ $booking->date }} |
                        ⏰ {{ $booking->start_time }} - {{ $booking->end_time }}
                    </div>

                    <div class="mt-2">
                        <b>Цель:</b> {{ $booking->purpose }}
                    </div>

                    @if($booking->user_comment)
                        <div class="text-gray-500 text-sm mt-1">
                            {{ $booking->user_comment }}
                        </div>
                    @endif

                    @if($booking->admin_comment)
                        <div class="text-gray-500 text-sm mt-1">
                            <b>Комментарий администратора:</b> {{ $booking->admin_comment }}
                        </div>
                    @endif

                </div>
            @empty
                <div class="text-gray-500">У вас пока нет заявок</div>
            @endforelse
        </div>

    </div>

    @vite('resources/css/booking.css')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <script>

        const form = document.getElementById('bookingForm');
        const loading = document.getElementById('loading');
        const submitBtn = document.getElementById('submitBtn');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            loading.classList.add('active');
            submitBtn.disabled = true;

            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');

            const formData = new FormData(form);

            try {
                const response = await fetch('/bookings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('[name=_token]').value
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    successAlert.textContent = data.message;
                    successAlert.classList.remove('d-none');

                    form.reset();

                } else if (response.status === 422) {
                    let errors = Object.values(data.errors).flat().join('\n');
                    errorAlert.textContent = errors;
                    errorAlert.classList.remove('d-none');

                } else {
                    throw new Error(data.message);
                }

            } catch (error) {
                errorAlert.textContent = 'Произошла ошибка. Попробуйте позже.';
                errorAlert.classList.remove('d-none');
            }

            loading.classList.remove('active');
            submitBtn.disabled = false;
        });
    
        flatpickr(".flatpickr", {
            dateFormat: "d.m.Y",
            locale: "ru",
            minDate: "today",
            disable: [
                function(date) {
                    return date.getDate() < (new Date()).getDate();
                }
            ]
        });

        function setupTimeMask(element) {
            element.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 3) {
                    value = value.substring(0, 2) + ':' + value.substring(2, 4);
                } else if (value.length >= 1) {
                    if (parseInt(value) > 23) {
                        value = '23';
                    }
                }
                e.target.value = value.substring(0, 5);
            });
        }

        setupTimeMask(document.getElementById('start_time'));
        setupTimeMask(document.getElementById('end_time'));
    
    </script>

</x-layout>