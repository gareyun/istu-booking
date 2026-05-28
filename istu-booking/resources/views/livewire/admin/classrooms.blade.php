<div class="flex-1 p-[30px] overflow-y-auto">
    <h1 class="text-2xl mb-5 pb-2.5 border-b-2 border-[#1a2a6c] text-[#1a2a6c]">Аудитории</h1>

    @if ($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $successMessage }}
        </div>
    @endif
    @if ($errorMessage)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errorMessage }}
        </div>
    @endif

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

        <button wire:click="openBuildingListModal" class="inline-block mr-2.5 px-6 py-3 text-white rounded-[10px]
                text-base font-semibold cursor-pointer shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                transition-all duration-300 ease-in-out
                bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)] hover:-translate-y-0.5">
            Корпуса
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
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                bg-white w-[500px] max-w-full max-h-[90vh] rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)]
                p-[25px] pr-1 flex flex-col text-2xl animate-modalFade overflow-hidden">
                <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">Добавить аудиторию</h2>

                <div class="flex-1 overflow-y-auto pr-[20px]">

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
                        <div class="mb-5">

                            <div class="space-y-2 mb-4 max-h-[180px] overflow-y-auto">
                                @forelse($categories as $category)
                                    <div class="flex items-center justify-between border border-gray-200 rounded-[10px] px-3 py-2">
                                        <span class="text-base">
                                            {{ $category->category }}
                                        </span>

                                        <button
                                            wire:click="deleteCategory({{ $category->id }})"
                                            type="button"
                                            class="text-[#c2433a] cursor-pointer mt-[-5px]">
                                            &times;
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">
                                        Категорий пока нет
                                    </div>
                                @endforelse
                            </div>

                            <input wire:model="newCategory"
                                placeholder="Категория"
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
                </div>
                <div class="flex justify-end gap-2.5 mt-2.5 pr-[20px]">
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
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[500px] max-w-full max-h-[90vh]
                    p-[25px] pr-0 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)]
                    flex flex-col text-2xl animate-modalFade overflow-hidden">
                <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">Добавить корпус</h2>

                <div class="flex-1 overflow-y-auto pr-[20px]">
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
                        <div class="mb-5">

                            <div class="space-y-2 mb-4 max-h-[180px] overflow-y-auto">
                                @forelse($buildingTypes as $type)
                                    <div class="flex items-center justify-between border border-gray-200 rounded-[10px] px-3 py-2">
                                        <span class="text-base">
                                            {{ $type->type }}
                                        </span>

                                        <button
                                            wire:click="deleteBuildingType({{ $type->id }})"
                                            type="button"
                                            class="text-[#c2433a] cursor-pointer mt-[-5px]">
                                            &times;
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">
                                        Типов пока нет
                                    </div>
                                @endforelse
                            </div>

                            <input wire:model="newType"
                                placeholder="Тип корпуса"
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
                </div>
                <div class="flex justify-end gap-2.5 mt-2.5 pr-[20px]">
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

    {{-- BUILDINGS LIST MODAL --}}
    @if($showBuildingListModal)
        <div class="fixed inset-0 z-[1000] bg-[rgba(26,42,108,0.35)] backdrop-blur-[4px]">
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                bg-white w-[900px] max-w-full max-h-[85vh]
                rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)]
                flex flex-col overflow-hidden">

                <div class="p-[25px] pb-0">
                    <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2">
                        Управление корпусами
                    </h2>
                </div>

                {{-- Прокручиваемая область --}}
                <div class="flex-1 overflow-y-auto px-[25px] pt-5">
                    @forelse($buildings as $building)
                        <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.1)] p-5 mb-5">

                            <div class="text-xl font-bold text-[#1a2a6c] mb-[5px]">
                                {{ $building->name }}
                            </div>

                            <div class="my-2 flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Тип:</div>
                                <div>{{ $building->type->type }}</div>
                            </div>

                            <div class="my-2 flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Адрес:</div>
                                <div>{{ $building->address }}</div>
                            </div>

                            <div class="my-2 flex justify-between">
                                <div class="font-bold text-[#7f8c8d]">Описание:</div>
                                <div>{{ $building->description }}</div>
                            </div>

                            <div class="w-full flex mt-5">
                                <button wire:click="openBuildingEditModal({{ $building->id }})"
                                        class="mr-2.5 px-6 py-3 text-white rounded-[10px]
                                        font-semibold transition-all duration-300
                                        shadow-[0_4px_15px_rgba(37,117,252,0.4)]
                                        bg-gradient-to-br from-[#1A2A6C] to-[#3456DB]
                                        hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]
                                        hover:-translate-y-0.5 cursor-pointer">
                                    ✏️ Редактировать
                                </button>

                                <button wire:click="deleteBuilding({{ $building->id }})"
                                        class="px-6 py-3 text-white rounded-[10px]
                                        font-semibold transition-all duration-300
                                        shadow-[0_4px_15px_rgba(231,76,60,0.35)]
                                        bg-gradient-to-br from-[#c2433a] to-[#EB4C42]
                                        hover:shadow-[0_6px_20px_rgba(231,76,60,0.5)]
                                        hover:-translate-y-0.5 cursor-pointer">
                                    🗑 Удалить
                                </button>
                            </div>
                        </div>

                    @empty
                        <div class="text-center text-[#7f8c8d] py-10">
                            Корпусов пока нет
                        </div>
                    @endforelse
                </div>

                <div class="sticky bottom-0 bg-white px-[25px] py-4 shadow-[0_-4px_15px_rgba(0,0,0,0.05)]">
                    <div class="flex justify-end">
                        <button wire:click="closeBuildingListModal"
                                class="px-6 py-3 text-white rounded-[10px]
                                font-semibold transition-all duration-300
                                bg-gradient-to-br from-[#c2433a] to-[#EB4C42]
                                shadow-[0_4px_15px_rgba(231,76,60,0.35)]
                                hover:shadow-[0_6px_20px_rgba(231,76,60,0.5)]
                                hover:-translate-y-0.5 cursor-pointer">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- BUILDING EDIT MODAL --}}
    @if($showBuildingEditModal)
        <div class="fixed inset-0 z-[1100] bg-[rgba(26,42,108,0.35)] backdrop-blur-[4px]">

            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white
                    w-[500px] max-w-full rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] p-[25px]">

                <h2 class="font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">Редактировать корпус</h2>

                <p class="mb-[5px] text-lg font-semibold">Название</p>
                <input wire:model="editBuildingName"
                        class="w-full p-3 border-2 border-gray-300 rounded-[10px] focus:outline-none
                        transition-all duration-300
                        focus:border-[#3456db] focus:ring-4 focus:ring-[rgba(52,86,219,0.15)] mb-4">
                
                <p class="mb-[5px] text-lg font-semibold">Тип корпуса</p>
                <select wire:model="editBuildingTypeId"
                        class="w-full p-3 border-2 border-gray-300 rounded-[10px] focus:outline-none
                        focus:border-[#3456db] mb-4 transition-all duration-300">
                    @foreach($buildingTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                    @endforeach
                </select>

                <p class="mb-[5px] text-lg font-semibold">Адрес</p>
                <input wire:model="editBuildingAddress"
                        class="w-full p-3 border-2 border-gray-300 rounded-[10px] transition-all duration-300
                        focus:outline-none focus:border-[#3456db] mb-4">

                <p class="mb-[5px] text-lg font-semibold">Описание</p>
                <textarea wire:model="editBuildingDescription"
                            class="w-full p-3 border-2 border-gray-300 rounded-[10px] min-h-[120px]
                            transition-all duration-300
                            focus:outline-none focus:border-[#3456db] mb-4">
                </textarea>

                <div class="flex justify-end gap-2.5">
                    <button wire:click="updateBuilding" class="px-6 py-3 text-white rounded-[10px] font-semibold
                            bg-gradient-to-br from-[#1A2A6C] to-[#3456DB] cursor-pointer transition-all duration-300
                            hover:shadow-[0_6px_20px_rgba(37,117,252,0.6)]  hover:-translate-y-0.5">
                        Сохранить
                    </button>

                    <button wire:click="closeBuildingEditModal" class="px-6 py-3 text-white rounded-[10px] font-semibold
                            bg-gradient-to-br from-[#c2433a] to-[#EB4C42] transition-all duration-300
                            hover:shadow-[0_6px_20px_rgba(231,76,60,0.5)] hover:-translate-y-0.5 cursor-pointer
                            shadow-[0_4px_15px_rgba(231,76,60,0.35)] hover:-translate-y-0.5">
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