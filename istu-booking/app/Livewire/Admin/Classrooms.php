<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\ClassroomCategory;

class Classrooms extends Component
{
    public $classrooms;
    public $categories;
    public $buildings;
    public $buildingTypes;

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

    private function resetFields()
    {
        $this->reset([
            'room', 'description', 'equipment', 'capacity',
            'google_calendar_id', 'classroom_category_id', 'building_id'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.classrooms');
    }
}