<x-layout>
    @vite('resources/css/admin.css')

    <div class="main-content">
        <h1 class="section-title">Аудитории</h1>

        <div class="tabs">
            <a href="{{ route('admin') }}" class="btn">Заявки</a>

            <button onclick="openModal()" class="btn">
                + Добавить аудиторию
            </button>
        </div>

        <div class="applications-list">
            @foreach ($classrooms as $classroom)
                <div class="application-classroom">
                    <div class="application-title">
                        {{$classroom->room}}
                    </div>

                    <div class="application-detail">
                        <div class="label">Описание:</div>
                        <div class="value">{{$classroom->description}}</div>
                    </div>

                    <div class="application-detail">
                        <div class="label">Оборудование:</div>
                        <div class="value">{{$classroom->equipment}}</div>
                    </div>

                    <div class="application-detail">
                        <div class="label">Вместимость:</div>
                        <div class="value">{{$classroom->capacity}}</div>
                    </div>

                    <div class="application-detail">
                        <div class="label">Google Calendar:</div>
                        <div class="value">{{$classroom->google_calendar_id}}</div>
                    </div>

                    <div class="classroom-btn-area w-full flex">
                        <button onclick='openEditModal(@json($classroom))' class="btn-edit">✏️ Редактировать</button>
                        <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST"
                            onsubmit="return confirm('Удалить аудиторию?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-reject">🗑 Удалить</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>

            <h2 class="modal-title">Редактировать аудиторию</h2>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <input type="text" name="room" id="edit-room" required>
                <textarea name="description" id="edit-description" required></textarea>
                <textarea name="equipment" id="edit-equipment"></textarea>
                <input type="number" name="capacity" id="edit-capacity" required>
                <input type="text" name="google_calendar_id" id="edit-calendar" class="input-calendar" required>

                <div class="modal-actions">
                    <button type="submit" class="btn">Сохранить</button>
                    <button type="button" onclick="closeEditModal()" class="btn-reject">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="modal-title">Добавить аудиторию</h2>

            <form method="POST" action="{{ route('classrooms.store') }}">
                @csrf
                <input type="text" name="room" placeholder="Номер аудитории" required>
                <textarea name="description" placeholder="Описание" required></textarea>
                <textarea name="equipment" placeholder="Оборудование"></textarea>
                <input type="number" name="capacity" placeholder="Вместимость" required>
                <input type="text" name="google_calendar_id" placeholder="Ссылка на Google Calendar" required>
                <div class="modal-actions">
                    <button type="submit" class="btn">Сохранить</button>
                    <button type="button" onclick="closeModal()" class="btn-reject">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(classroom) {
            document.getElementById('editModal').style.display = 'block';

            document.getElementById('edit-room').value = classroom.room;
            document.getElementById('edit-description').value = classroom.description;
            document.getElementById('edit-equipment').value = classroom.equipment;
            document.getElementById('edit-capacity').value = classroom.capacity;
            document.getElementById('edit-calendar').value = classroom.google_calendar_id;

            document.getElementById('editForm').action = `/admin/classrooms/${classroom.id}`;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openModal() {
            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>

</x-layout>