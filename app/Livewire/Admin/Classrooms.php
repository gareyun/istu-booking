<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Classroom;
use App\Models\ClassroomCategory;
use Illuminate\Support\Facades\Storage;

use Livewire\Attributes\Layout;
#[Layout('components.layout')]

class Classrooms extends Component
{
    use WithFileUploads;

    public $classrooms;
    public $categories;

    public $newCategory;

    public $showCategoryModal = false;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;

    public $room, $description, $equipment, $capacity, $google_calendar_id;
    public $classroom_category_id;

    public $photo;
    public $currentPhoto = null;
    public $removePhoto = false;

    public $is_hidden = false;

    public $successMessage = null;
    public $errorMessage = null;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->classrooms = Classroom::with(['category'])->get();
        $this->categories = ClassroomCategory::all();
    }

    public function openCreateModal()
    {
        $this->resetFields();

        $this->classroom_category_id = $this->categories->first()->id ?? null;

        $this->successMessage = null;
        $this->errorMessage = null;
        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $classroom = Classroom::findOrFail($id);

        $this->resetValidation();
        $this->reset(['photo', 'removePhoto']);

        $this->editingId = $id;
        $this->room = $classroom->room;
        $this->description = $classroom->description;
        $this->equipment = $classroom->equipment;
        $this->capacity = $classroom->capacity;
        $this->google_calendar_id = $classroom->google_calendar_id;
        $this->classroom_category_id = $classroom->classroom_category_id;
        $this->currentPhoto = $classroom->photo;
        $this->is_hidden = (bool) $classroom->is_hidden;

        $this->successMessage = null;
        $this->errorMessage = null;
        $this->showEditModal = true;
    }

    protected function rules(): array
    {
        return [
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
            'classroom_category_id' => 'required|exists:classroom_categories,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'is_hidden' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'required'           => 'Поле «:attribute» обязательно для заполнения.',
            'string'             => 'Поле «:attribute» должно быть текстом.',
            'integer'            => 'Поле «:attribute» должно быть целым числом.',
            'min.numeric'        => 'Поле «:attribute» должно быть не меньше :min.',
            'max.string'         => 'Поле «:attribute» не должно быть длиннее :max символов.',
            'exists'             => 'Выбранная категория не существует.',
            'photo.image'        => 'Файл должен быть изображением.',
            'photo.mimes'        => 'Допустимые форматы: JPG, PNG, WEBP.',
            'photo.max'          => 'Размер фотографии не должен превышать 10 МБ.',
            'photo.uploaded'     => 'Не удалось загрузить фотографию. Проверьте, что размер файла не превышает 10 МБ, и попробуйте ещё раз.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'room'                  => 'Пространство',
            'description'           => 'Описание',
            'equipment'             => 'Оборудование',
            'capacity'              => 'Вместимость',
            'google_calendar_id'    => 'Идентификатор Google Calendar',
            'classroom_category_id' => 'Категория',
            'photo'                 => 'Фотография',
            'newCategory'           => 'Категория',
        ];
    }

    public function updatedPhoto()
    {
        $this->validateOnly('photo');
    }

    public function save()
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        $validated = $this->validate();

        $data = collect($validated)->except('photo')->all();

        if ($this->photo) {
            $data['photo'] = $this->photo->store('classrooms', 'public');
        }

        Classroom::create($data);

        $this->closeModal();
        $this->loadData();
        $this->successMessage = 'Пространство добавлено';
    }

    public function update()
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        $validated = $this->validate();

        $classroom = Classroom::findOrFail($this->editingId);

        $data = collect($validated)->except('photo')->all();

        if ($this->photo) {
            $data['photo'] = $this->photo->store('classrooms', 'public');
            $this->deletePhotoFile($classroom->photo);
        } elseif ($this->removePhoto) {
            $data['photo'] = null;
            $this->deletePhotoFile($classroom->photo);
        }

        $classroom->update($data);

        $this->closeModal();
        $this->loadData();
        $this->successMessage = 'Пространство обновлено';
    }

    public function delete($id)
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            $this->errorMessage = 'Пространство не найдено.';
            return;
        }

        if ($classroom->bookings()->whereIn('status', ['approved', 'pending'])->exists()) {
            $this->errorMessage = 'Нельзя удалить пространство – есть связанные заявки.';
            return;
        }

        $this->deletePhotoFile($classroom->photo);

        $classroom->delete();
        $this->loadData();
        $this->successMessage = 'Пространство успешно удалено.';
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;

        $this->reset(['photo', 'currentPhoto', 'removePhoto']);
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->reset([
            'room', 'description', 'equipment', 'capacity',
            'google_calendar_id', 'classroom_category_id',
            'photo', 'currentPhoto', 'removePhoto', 'is_hidden'
        ]);

        $this->resetValidation();
    }

    public function toggleHidden($id)
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        $classroom = Classroom::findOrFail($id);
        $classroom->update(['is_hidden' => !$classroom->is_hidden]);

        $this->loadData();
        $this->successMessage = $classroom->is_hidden
            ? 'Пространство скрыто для бронирования.'
            : 'Пространство снова доступно для бронирования.';
    }

    private function deletePhotoFile(?string $path): void
    {
        if ($path && !str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }

    public function toggleCategoryModal() {
        $this->showCategoryModal = !$this->showCategoryModal;
    }

    public function createCategory()
    {
        $this->validate([
            'newCategory' => 'required|string|max:255',
        ]);

        ClassroomCategory::create([
            'category' => $this->newCategory
        ]);

        $this->showCategoryModal = false;
        $this->newCategory = null;
        $this->loadData();
    }

    public function deleteCategory($id)
    {
        $category = ClassroomCategory::findOrFail($id);

        if ($category->classrooms()->exists()) {
            $this->errorMessage = 'Нельзя удалить категорию, так как есть связанные пространства';
            return;
        }

        $category->delete();

        if ($this->classroom_category_id == $id) {
            $this->classroom_category_id = $this->categories
                ->where('id', '!=', $id)
                ->first()?->id;
        }

        $this->successMessage = 'Категория удалена';

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.classrooms');
    }
}