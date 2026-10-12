<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\ClassroomCategory;

use Livewire\Attributes\Layout;
#[Layout('components.layout')]

class Classrooms extends Component
{
    public $classrooms;
    public $categories;

    public $newCategory;

    public $showCategoryModal = false;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;

    public $room, $description, $equipment, $capacity, $google_calendar_id;
    public $classroom_category_id;

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

        $this->editingId = $id;
        $this->room = $classroom->room;
        $this->description = $classroom->description;
        $this->equipment = $classroom->equipment;
        $this->capacity = $classroom->capacity;
        $this->google_calendar_id = $classroom->google_calendar_id;
        $this->classroom_category_id = $classroom->classroom_category_id;

        $this->successMessage = null;
        $this->errorMessage = null;
        $this->showEditModal = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
            'classroom_category_id' => 'required|exists:classroom_categories,id',
        ]);

        Classroom::create($validated);

        $this->closeModal();
        $this->loadData();
        $this->successMessage = 'Пространство добавлено';
    }

    public function update()
    {
        $validated = $this->validate([
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
            'classroom_category_id' => 'required|exists:classroom_categories,id',
        ]);

        Classroom::findOrFail($this->editingId)->update($validated);

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

        $classroom->delete();
        $this->loadData();
        $this->successMessage = 'Пространство успешно удалено.';
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
    }

    private function resetFields()
    {
        $this->reset([
            'room', 'description', 'equipment', 'capacity',
            'google_calendar_id', 'classroom_category_id'
        ]);
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