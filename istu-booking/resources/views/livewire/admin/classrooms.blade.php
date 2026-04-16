<div class="main-content">
    <h1 class="section-title">Аудитории</h1>

    <div class="tabs">
        <a href="{{ route('admin') }}" class="btn">Заявки</a>

        <button wire:click="openCreateModal" class="btn">
            + Добавить аудиторию
        </button>
    </div>

    <div class="applications-list">
        @foreach ($classrooms as $classroom)
            <div class="application-classroom">
                <div class="application-title">{{ $classroom->room }}</div>

                <div class="application-detail">
                    <div class="label">Тип аудитории:</div>
                    <div class="value">{{ $classroom->category->category }}</div>
                </div>

                <div class="application-detail">
                    <div class="label">Корпус:</div>
                    <div class="value">{{ $classroom->building->name }}</div>
                </div>

                <div class="application-detail">
                    <div class="label">Описание:</div>
                    <div class="value">{{ $classroom->description }}</div>
                </div>

                <div class="application-detail">
                    <div class="label">Оборудование:</div>
                    <div class="value">{{ $classroom->equipment }}</div>
                </div>

                <div class="application-detail">
                    <div class="label">Вместимость:</div>
                    <div class="value">{{ $classroom->capacity }}</div>
                </div>

                <div class="classroom-btn-area w-full flex">
                    <button wire:click="openEditModal({{ $classroom->id }})" class="btn-edit">
                        ✏️ Редактировать
                    </button>

                    <button wire:click="delete({{ $classroom->id }})" class="btn-reject">
                        🗑 Удалить
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- CREATE MODAL --}}
    @if($showCreateModal)
        <div class="modal" style="display:block">
            <div class="modal-content">
                <h2 class="modal-title">Добавить аудиторию</h2>

                <p class="modal-input-title">Аудитория</p>
                <input class="modal-input" wire:model="room" placeholder="Номер аудитории">

                <select class="modal-input" wire:model="classroom_category_id">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </select>

                <select class="modal-input" wire:model="building_id">
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}">
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>

                <p class="modal-input-title">Описание</p>
                <textarea class="modal-input" wire:model="description" placeholder="Описание"></textarea>
                <p class="modal-input-title">Оборудование</p>
                <textarea class="modal-input" wire:model="equipment" placeholder="Оборудование, имеющееся в аудитории"></textarea>
                <p class="modal-input-title">Вместимость</p>
                <input class="modal-input" wire:model="capacity" type="number" placeholder="Количество человек">
                <p class="modal-input-title">Идентификатор Google Calendar</p>
                <input class="modal-input" wire:model="google_calendar_id" placeholder="Ссылка">

                <div class="modal-actions">
                    <button wire:click="save" class="btn">Сохранить</button>
                    <button wire:click="closeModal" class="btn-reject">Отмена</button>
                </div>
            </div>
        </div>
    @endif

    {{-- EDIT MODAL --}}
    @if($showEditModal)
        <div class="modal" style="display:block">
            <div class="modal-content">
                <h2 class="modal-title">Редактировать аудиторию</h2>

                <p class="modal-input-title">Аудитория</p>
                <input class="modal-input" wire:model="room">
                <p class="modal-input-title">Описание</p>
                <textarea class="modal-input" wire:model="description"></textarea>
                <p class="modal-input-title">Оборудование</p>
                <textarea class="modal-input" wire:model="equipment"></textarea>
                <p class="modal-input-title">Вместимость</p>
                <input class="modal-input" wire:model="capacity" type="number">
                <p class="modal-input-title">Идентификатор Google Calendar</p>
                <input class="modal-input" wire:model="google_calendar_id">

                <div class="modal-actions">
                    <button wire:click="update" class="btn">Сохранить</button>
                    <button wire:click="closeModal" class="btn-reject">Отмена</button>
                </div>
            </div>
        </div>
    @endif
</div>