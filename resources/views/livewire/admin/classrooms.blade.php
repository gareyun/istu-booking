<div class="flex-1 p-[30px] overflow-y-auto">
    <h1 class="text-2xl mb-5 pb-2.5 border-b-2 border-[#1a2a6c] text-[#1a2a6c]">Пространства</h1>

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

    <div class="mb-5 flex gap-3">
        <x-button onclick="window.location.href='{{ route('admin.index') }}'">Заявки</x-button>
        <x-button wire:click="openCreateModal">+ Добавить пространство</x-button>
    </div>

    <div class="applications-list">
        @foreach ($classrooms as $classroom)
            <div class="bg-white rounded-[10px] shadow-[0_4px_15px_rgba(0,0,0,0.1)]
                            p-5 mb-5 transition-transform duration-300 ease-in-out">

                <div class="flex items-center gap-3 mb-[5px]">
                    @if($classroom->photo)
                        <img src="{{ str_starts_with($classroom->photo, 'http') ? $classroom->photo : asset('storage/' . $classroom->photo) }}"
                            alt="{{ $classroom->room }}" loading="lazy" width="64" height="48"
                            class="h-12 w-16 rounded-lg object-cover border border-[#e3e6f0]">
                    @endif
                    <div class="text-xl font-bold text-[#1a2a6c]">{{ $classroom->room }}</div>
                </div>

                <div class="my-2 flex justify-between">
                    <div class="font-bold text-[#7f8c8d]">Категория пространства:</div>
                    <div>{{ $classroom->category->category }}</div>
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

                <div class="w-full flex mt-5 gap-3">
                    <x-button wire:click="openEditModal({{ $classroom->id }})">✏️ Редактировать</x-button>
                    <x-button wire:click="delete({{ $classroom->id }})" color="red">🗑 Удалить</x-button>
                </div>
            </div>
        @endforeach
    </div>

    @if($showCreateModal)
        <x-modal title="Добавить пространство" :error="$errorMessage">
            <p class="mb-[5px] text-lg font-semibold">Пространство <span class="text-danger">*</span></p>
            <x-input wire:model="room" placeholder="Название пространства" class="mb-4"/>

            <p class="mb-[5px] text-lg font-semibold">Категория <span class="text-danger">*</span></p>
            <div class="flex justify-between items-center mb-4">
                <x-select wire:model="classroom_category_id">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </x-select>

                <x-button wire:click="toggleCategoryModal" class="ml-2">
                    {!! $showCategoryModal ? '&times;' : '+' !!}
                </x-button>
            </div>

            @if($showCategoryModal)
                <div class="mb-5">
                    <div class="space-y-2 mb-4 max-h-[180px] overflow-y-auto">
                        @forelse($categories as $category)
                            <div class="flex items-center justify-between border border-gray-200 rounded-[10px] px-3 py-2">
                                <span class="text-base">{{ $category->category }}</span>
                                <button wire:click="deleteCategory({{ $category->id }})"
                                        type="button"
                                        class="text-[#c2433a] cursor-pointer mt-[-5px]">
                                    &times;
                                </button>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">Категорий пока нет</div>
                        @endforelse
                    </div>

                    <x-input wire:model="newCategory" placeholder="Категория" class="mb-4"/>
                    <x-button wire:click="createCategory" class="w-full">Сохранить</x-button>
                </div>
            @endif

            <p class="mb-[5px] text-lg font-semibold">Описание <span class="text-danger">*</span></p>
            <x-input wire:model="description" type="textarea" placeholder="Описание" class="mb-4"/>

            <p class="mb-[5px] text-lg font-semibold">Оборудование <span class="text-danger">*</span></p>
            <x-input wire:model="equipment" type="textarea" placeholder="Оборудование, имеющееся в пространстве" class="mb-4"/>

            <p class="mb-[5px] text-lg font-semibold">Вместимость <span class="text-danger">*</span></p>
            <x-input wire:model="capacity" type="number" placeholder="Количество человек" class="mb-4"/>

            <x-photo-field :photo="$photo"/>

            <p class="mb-[5px] text-lg font-semibold">Идентификатор Google Calendar <span class="text-danger">*</span></p>
            <x-input wire:model="google_calendar_id" placeholder="Ссылка на календарь" class="mb-4"/>

            <x-slot name="footer">
                <x-button wire:click="save">Сохранить</x-button>
                <x-button wire:click="closeModal" color="red">Отмена</x-button>
            </x-slot>
        </x-modal>
    @endif

    @if($showEditModal)
        <x-modal title="Редактировать пространство" :error="$errorMessage">
            <p class="mb-[5px] text-lg font-semibold">Пространство</p>
            <x-input wire:model="room" class="mb-3" />

            <p class="mb-[5px] text-lg font-semibold">Категория</p>
            <x-select wire:model="classroom_category_id" wrapper-class="mb-3">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                @endforeach
            </x-select>

            <p class="mb-[5px] text-lg font-semibold">Описание</p>
            <x-input wire:model="description" type="textarea" class="mb-2" />

            <p class="mb-[5px] text-lg font-semibold">Оборудование</p>
            <x-input wire:model="equipment" type="textarea" class="mb-2" />

            <p class="mb-[5px] text-lg font-semibold">Вместимость</p>
            <x-input wire:model="capacity" type="number" class="mb-3" />

            <x-photo-field :photo="$photo" :current-photo="$currentPhoto" :remove-photo="$removePhoto"/>

            <p class="mb-[5px] text-lg font-semibold">Идентификатор Google Calendar</p>
            <x-input wire:model="google_calendar_id" class="mb-3" />

            <x-slot name="footer">
                <x-button wire:click="update">Сохранить</x-button>
                <x-button wire:click="closeModal" color="red">Отмена</x-button>
            </x-slot>
        </x-modal>
    @endif
</div>