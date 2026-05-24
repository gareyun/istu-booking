<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\ClassroomCategory;

use Illuminate\Support\Facades\Session;

class Classrooms extends Component
{
    public $classrooms;
    public $categories;
    public $buildings;
    public $buildingTypes;

    // формы
    public $newCategory;
    public $newBuildingName;
    public $newBuildingAddress;
    public $newBuildingDescription;
    public $newBuildingTypeId;
    public $newType;

    public $showCategoryModal = false;
    public $showBuildingModal = false;
    public $showTypeModal = false;

    public $showBuildingListModal = false;
    public $showBuildingEditModal = false;

    public $editingBuildingId = null;

    public $editBuildingName;
    public $editBuildingAddress;
    public $editBuildingDescription;
    public $editBuildingTypeId;

    public $showCreateModal = false;
    public $showEditModal = false;

    public $editingId = null;

    public $room, $description, $equipment, $capacity, $google_calendar_id;
    public $classroom_category_id, $building_id;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->classrooms = Classroom::with(['category', 'building'])->get();
        $this->categories = ClassroomCategory::all();
        $this->buildings = Building::with('type')->get();
        $this->buildingTypes = BuildingType::all();
    }

    public function openCreateModal()
    {
        $this->resetFields();
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
        $this->building_id = $classroom->building_id;

        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate([
            'room' => 'required',
            'capacity' => 'required|integer',
        ]);

        Classroom::create($this->getData());

        $this->closeModal();
        $this->loadData();
    }

    public function update()
    {
        Classroom::find($this->editingId)->update($this->getData());

        $this->closeModal();
        $this->loadData();
    }

    public function delete($id)
    {
        $classroom = Classroom::find($id);

        if ($classroom->bookings()->exists()) {
            return;
        }

        $classroom->delete();
        $this->loadData();
    }

    private function getData()
    {
        return [
            'room' => $this->room,
            'description' => $this->description,
            'equipment' => $this->equipment,
            'capacity' => $this->capacity,
            'google_calendar_id' => $this->google_calendar_id,
            'classroom_category_id' => $this->classroom_category_id,
            'building_id' => $this->building_id,
        ];
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
    }

    public function closeBuildingModal()
    {
        $this->showBuildingModal = false;
    }

    private function resetFields()
    {
        $this->reset([
            'room', 'description', 'equipment', 'capacity',
            'google_calendar_id', 'classroom_category_id', 'building_id'
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

    public function openBuildingModal()
    {
        $this->showBuildingModal = true;
    }

    public function toggleTypeModal() {
        $this->showTypeModal = !$this->showTypeModal;
    }

    public function createBuildingType()
    {
        $this->validate([
            'newType' => 'required|string|max:255',
        ]);

        BuildingType::create([
            'type' => $this->newType
        ]);

        $this->showTypeModal = false;
        $this->newType = null;
        $this->loadData();
    }

    public function createBuilding()
    {
        $this->validate([
            'newBuildingName' => 'required|string|max:255',
            'newBuildingAddress' => 'required|string|max:255',
            'newBuildingDescription' => 'nullable|string',
            'newBuildingTypeId' => 'required|exists:building_types,id',
        ]);

        Building::create([
            'name' => $this->newBuildingName,
            'address' => $this->newBuildingAddress,
            'description' => $this->newBuildingDescription,
            'building_type_id' => $this->newBuildingTypeId,
        ]);

        session()->flash(
            'success',
            'Корпус успешно создан'
        );

        $this->closeBuildingModal();

        $this->reset([
            'newBuildingName',
            'newBuildingAddress',
            'newBuildingDescription',
            'newBuildingTypeId'
        ]);

        $this->loadData();
    }

    public function openBuildingListModal()
    {
        $this->loadData();

        $this->showBuildingListModal = true;
    }

    public function closeBuildingListModal()
    {
        $this->showBuildingListModal = false;
    }

    public function openBuildingEditModal($id)
    {
        $building = Building::findOrFail($id);

        $this->editingBuildingId = $id;

        $this->editBuildingName = $building->name;
        $this->editBuildingAddress = $building->address;
        $this->editBuildingDescription = $building->description;
        $this->editBuildingTypeId = $building->building_type_id;

        $this->showBuildingEditModal = true;
    }

    public function closeBuildingEditModal()
    {
        $this->showBuildingEditModal = false;

        $this->reset([
            'editingBuildingId',
            'editBuildingName',
            'editBuildingAddress',
            'editBuildingDescription',
            'editBuildingTypeId'
        ]);
    }

    public function updateBuilding()
    {
        $this->validate([
            'editBuildingName' => 'required',
            'editBuildingAddress' => 'required',
            'editBuildingTypeId' => 'required',
        ]);

        Building::findOrFail(
            $this->editingBuildingId
        )->update([
            'name' => $this->editBuildingName,
            'address' => $this->editBuildingAddress,
            'description' => $this->editBuildingDescription,
            'building_type_id' => $this->editBuildingTypeId,
        ]);

        $this->closeBuildingEditModal();

        $this->loadData();
    }

    public function deleteBuilding($id)
    {
        $building = Building::findOrFail($id);

        if ($building->classrooms()->exists()) {
            session()->flash(
                'error',
                'Нельзя удалить корпус с аудиториями.'
            );

            return;
        }

        $building->delete();

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.classrooms');
    }
}