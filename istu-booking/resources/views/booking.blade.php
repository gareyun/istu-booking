<x-layout>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1A2A6C;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .booking-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
        }

        .header h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-control, .form-select {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .time-inputs {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .time-inputs .form-control {
            flex: 1;
        }

        .time-separator {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .equipment-section, .tech-support-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #3a56c4;
            border-color: #3a56c4;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading-spinner {
            width: 3rem;
            height: 3rem;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e3e6f0;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        /* Кастомизация flatpickr */
        .flatpickr-input {
            background-color: white !important;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .booking-container {
                margin: 20px;
                padding: 25px;
            }

            .time-inputs {
                flex-direction: column;
                gap: 10px;
            }

            .time-separator {
                transform: rotate(90deg);
            }
        }
    </style>

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
                {{-- <input name="classroom" type="text" class="form-control" id="room"
                       list="classrooms" placeholder="Выберите или введите аудиторию" required>
                <datalist id="classrooms">
                    @foreach ($classrooms as $classroom)
                        <option value="{{$classroom->id}}">{{$classroom->room}}</option>
                    @endforeach
                </datalist> --}}
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
                <textarea name="comment" class="form-control" id="student_message" rows="3"
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

        <!-- Индикатор загрузки -->
        <div id="loading" class="loading">
            <div class="spinner-border text-primary loading-spinner" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
            <p class="mt-3">Отправка заявки...</p>
        </div>

        <div class="footer">
            <p>По вопросам обращайтесь: 8 (3412) 77-60-55, доб. 1371</p>
            <p>Также вы можете подать заявку через <a href="https://t.me/your_bot" target="_blank">Telegram бота</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <script>
    
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