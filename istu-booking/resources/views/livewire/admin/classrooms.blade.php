<div class="flex-1 p-[30px] overflow-y-auto">
    <h1 class="text-2xl mb-5 pb-2.5 border-b-2 border-[#1a2a6c] text-[#1a2a6c]">Аудитории</h1>

    <div class="mb-5">
        <a href="{{ route('admin') }}"
            class="inline-block mr-2.5 px-6 py-3
            text-white rounded-[10px] text-base font-semibold
            cursor-pointer border-none transition-all duration-300 ease-in-out
            shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
            hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5
                hover:from-[#1A2A6C] hover:to-[#3456DB]">
            Заявки
        </a>

        <button wire:click="openCreateModal"
                class="inline-block mr-2.5 px-6 py-3 text-white rounded-[10px] text-base font-semibold
                cursor-pointer border-none transition-all duration-300 ease-in-out
                shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5 hover:bg-gradient-to-r
                hover:from-[#1A2A6C] hover:to-[#3456DB]">
            + Добавить аудиторию
        </button>

        <button wire:click="openBuildingModal"
                class="inline-block mr-2.5 px-6 py-3 text-white rounded-[10px] text-base font-semibold
                cursor-pointer border-none transition-all duration-300 ease-in-out
                shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5 hover:bg-gradient-to-r
                hover:from-[#1A2A6C] hover:to-[#3456DB]">
            + Добавить корпус
        </button>
    </div>

    <div class="applications-list">
        @foreach ($classrooms as $classroom)
            <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.1)]
                            p-5 mb-5 transition-transform duration-300 ease-in-out">
                <div class="text-xl font-bold text-[#1a2a6c] mb-[5px]">{{ $classroom->room }}</div>

                <div class="my-2 flex justify-between">
                    <div class="font-bold text-[#7f8c8d]">Тип аудитории:</div>
                    <div>{{ $classroom->category->category }}</div>
                </div>

                <div class="my-2 flex justify-between">
                    <div class="font-bold text-[#7f8c8d]">Корпус:</div>
                    <div>{{ $classroom->building->name }}</div>
                </div>

                <div class="my-2 flex justify-between">
                    <div class="font-bold text-[#7f8c8d]">Описание:</div>
                    <div>{{ $classroom->description }}</div>
                </div>

                <div class="my-2 flex justify-between">
                    <div class="font-bold text-[#7f8c8d]">Оборудование:</div>
                    <div>{{ $classroom->equipment }}</div>
                </div>

                <div class="my-2 flex justify-between">
                    <div class="font-bold text-[#7f8c8d]">Вместимость:</div>
                    <div>{{ $classroom->capacity }}</div>
                </div>

                <div class="w-full flex mt-5">
                    <button wire:click="openEditModal({{ $classroom->id }})"
                            class="inline-block mr-2.5 px-6 py-3 text-white rounded-[10px] text-base
                            font-semibold cursor-pointer border-none transition-all duration-300 ease-in-out
                            shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                            hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5 hover:bg-gradient-to-r
                            hover:from-[#1A2A6C] hover:to-[#3456DB]">
                        ✏️ Редактировать
                    </button>

                    <button wire:click="delete({{ $classroom->id }})"
                            class="inline-block mr-2.5 px-6 py-3 text-white rounded-[10px] text-base
                            font-semibold cursor-pointer border-none transition-all duration-300 ease-in-out
                            shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#c2433a] to-[#EB4C42]
                            hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5 hover:bg-gradient-to-r">
                        🗑 Удалить
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- CREATE MODAL --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-[1000] bg-[rgba(26,42,108,0.35)] backdrop-blur-[4px]">
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[500px] max-w-full p-[25px]
                    rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] flex flex-col text-2xl animate-modalFade">
                <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">Добавить аудиторию</h2>

                <p class="mb-[5px] text-lg font-semibold">Аудитория</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                        wire:model="room" placeholder="Номер аудитории">

                <div class="flex justify-between items-center mb-4">
                    <select class="flex-1 p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)]"
                            wire:model="classroom_category_id">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                        @endforeach
                    </select>
                    <button class="inline-block px-5 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-[#1A2A6C] hover:to-[#3456DB] ml-2
                            flex-shrink-0"
                            wire:click="toggleCategoryModal">
                        @if($showCategoryModal)
                            &times;
                        @else
                            +
                        @endif
                    </button>
                </div>

                @if($showCategoryModal)
                    <div>
                        <input wire:model="newCategory" placeholder="Категория"
                               class="w-full mb-2.5 p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all
                               duration-300 focus:outline-none focus:border-[#3456db] focus:ring-4
                               focus:ring-[rgba(52,86,219,0.15)]">
                        <button wire:click="createCategory"
                                class="inline-block w-full mb-5 px-6 py-3 text-white rounded-[10px] text-base font-semibold
                                cursor-pointer border-none transition-all duration-300 ease-in-out
                                shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                                hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5 hover:bg-gradient-to-r
                                hover:from-[#1A2A6C] hover:to-[#3456DB]">
                            Сохранить
                        </button>
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <select class="flex-1 p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)]"
                            wire:model="building_id">
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->name }}</option>
                        @endforeach
                    </select>
                    <button class="inline-block px-5 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-[#1A2A6C] hover:to-[#3456DB] ml-2
                            flex-shrink-0"
                            wire:click="openBuildingModal">+</button>
                </div>

                <p class="mb-[5px] text-lg font-semibold">Описание</p>
                <textarea class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)]
                            min-h-[80px] resize-y mb-4"
                          wire:model="description" placeholder="Описание"></textarea>

                <p class="mb-[5px] text-lg font-semibold">Оборудование</p>
                <textarea class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)]
                            min-h-[80px] resize-y mb-4"
                          wire:model="equipment" placeholder="Оборудование, имеющееся в аудитории"></textarea>

                <p class="mb-[5px] text-lg font-semibold">Вместимость</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="capacity" type="number" placeholder="Количество человек">

                <p class="mb-[5px] text-lg font-semibold">Идентификатор Google Calendar</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="google_calendar_id" placeholder="Ссылка на календарь">

                <div class="flex justify-end gap-2.5 mt-2.5">
                    <button wire:click="save"
                            class="inline-block px-6 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-[#1A2A6C] hover:to-[#3456DB]">
                        Сохранить
                    </button>
                    <button wire:click="closeModal"
                            class="inline-block px-6 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#c2433a] to-[#EB4C42] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- BUILDING MODAL --}}
    @if($showBuildingModal)
        <div class="fixed inset-0 z-[1000] bg-[rgba(26,42,108,0.35)] backdrop-blur-[4px]">
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[500px] max-w-full p-[25px]
                    rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] flex flex-col text-2xl animate-modalFade">
                <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">Добавить корпус</h2>

                <p class="mb-[5px] text-lg font-semibold">Корпус</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="newBuildingName" placeholder="Название">

                <div class="flex justify-between items-center mb-4">
                    <select class="flex-1 p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)]"
                            wire:model="newBuildingTypeId">
                        @foreach($buildingTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->type }}</option>
                        @endforeach
                    </select>
                    <button class="inline-block px-5 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-[#1A2A6C] hover:to-[#3456DB] ml-2
                            flex-shrink-0"
                            wire:click="toggleTypeModal">
                        @if($showTypeModal)
                            <span>&times;</span>
                        @else
                            +
                        @endif
                    </button>
                </div>

                @if($showTypeModal)
                    <div>
                        <input wire:model="newType" placeholder="Тип корпуса"
                               class="w-full mb-2.5 p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all
                               duration-300 focus:outline-none focus:border-[#3456db] focus:ring-4
                               focus:ring-[rgba(52,86,219,0.15)]">
                        <button wire:click="createBuildingType"
                                class="inline-block w-full mb-5 px-6 py-3 text-white rounded-[10px] text-base font-semibold
                                cursor-pointer border-none transition-all duration-300 ease-in-out
                                shadow-[0_4px_15px_rgba(37,117,252,0.4)] bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                                hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5 hover:bg-gradient-to-r
                                hover:from-[#1A2A6C] hover:to-[#3456DB]">
                            Сохранить
                        </button>
                    </div>
                @endif

                <p class="mb-[5px] text-lg font-semibold">Адрес</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="newBuildingAddress" placeholder="ул. Студенческая">

                <p class="mb-[5px] text-lg font-semibold">Описание</p>
                <textarea class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] min-h-[80px]
                            resize-y mb-4"
                          wire:model="newBuildingDescription" placeholder="Корпус IT технологий"></textarea>

                <div class="flex justify-end gap-2.5 mt-2.5">
                    <button wire:click="createBuilding"
                            class="inline-block px-6 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-[#1A2A6C] hover:to-[#3456DB]">
                        Сохранить
                    </button>
                    <button wire:click="closeBuildingModal"
                            class="inline-block px-6 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#c2433a] to-[#EB4C42] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- EDIT MODAL --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-[1000] bg-[rgba(26,42,108,0.35)] backdrop-blur-[4px]">
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[500px] max-w-full p-[25px]
                    rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] flex flex-col text-2xl animate-modalFade">
                <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">Редактировать аудиторию</h2>

                <p class="mb-[5px] text-lg font-semibold">Аудитория</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="room">

                <p class="mb-[5px] text-lg font-semibold">Описание</p>
                <textarea class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] min-h-[80px]
                        resize-y mb-4"
                          wire:model="description"></textarea>

                <p class="mb-[5px] text-lg font-semibold">Оборудование</p>
                <textarea class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                            focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)]
                            min-h-[80px] resize-y mb-4"
                          wire:model="equipment"></textarea>

                <p class="mb-[5px] text-lg font-semibold">Вместимость</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="capacity" type="number">

                <p class="mb-[5px] text-lg font-semibold">Идентификатор Google Calendar</p>
                <input class="w-full p-3 border-2 border-gray-300 rounded-[10px] text-base transition-all duration-300
                        focus:outline-none focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4"
                       wire:model="google_calendar_id">

                <div class="flex justify-end gap-2.5 mt-2.5">
                    <button wire:click="update"
                            class="inline-block px-6 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r hover:from-[#1A2A6C] hover:to-[#3456DB]">
                        Сохранить
                    </button>
                    <button wire:click="closeModal"
                            class="inline-block px-6 py-3 text-white rounded-[10px] text-base font-semibold cursor-pointer
                            border-none transition-all duration-300 ease-in-out shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                            bg-gradient-to-br from-[#c2433a] to-[#EB4C42] hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                            hover:-translate-y-0.5 hover:bg-gradient-to-r">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>