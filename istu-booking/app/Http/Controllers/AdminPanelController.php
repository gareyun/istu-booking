<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\ClassroomCategory;

class AdminPanelController extends Controller
{
    public function classrooms()
    {
        $classrooms = Classroom::with(['category', 'building'])->get();
        $categories = ClassroomCategory::all();
        $buildings = Building::with('type')->get();
        $buildingTypes = BuildingType::all();

        return view('admin-classrooms', compact(
            'classrooms',
            'categories',
            'buildings',
            'buildingTypes'
        ));
    }

    public function storeClassroom(Request $request)
    {
        $validated = $request->validate([
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
            'classroom_category_id' => 'required|exists:classroom_categories,id',
            'building_id' => 'required|exists:buildings,id',
        ]);

        Classroom::create($validated);

        return back()->with('success', 'Аудитория добавлена');
    }

    public function updateClassroom(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'room' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'google_calendar_id' => 'required|string|max:100',
            'classroom_category_id' => 'required|exists:classroom_categories,id',
            'building_id' => 'required|exists:buildings,id',
        ]);

        $classroom->update($validated);

        return back()->with('success', 'Аудитория обновлена');
    }

    public function destroyClassroom(Classroom $classroom)
    {
        if ($classroom->bookings()->exists()) {
            return back()->with('error', 'Нельзя удалить аудиторию, есть связанные заявки');
        }

        $classroom->delete();

        return back()->with('success', 'Аудитория удалена');
    }

    public function storeBuilding(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'description' => 'nullable',
            'building_type_id' => 'required|exists:building_types,id'
        ]);

        Building::create($validated);

        return back()->with('success', 'Корпус добавлен');
    }

    public function storeBuildingType(Request $request)
    {
        $request->validate([
            'type' => 'required'
        ]);

        BuildingType::create([
            'type' => $request->type
        ]);

        return back()->with('success', 'Тип корпуса добавлен');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'category' => 'required'
        ]);

        ClassroomCategory::create([
            'category' => $request->category
        ]);

        return back()->with('success', 'Категория добавлена');
    }
}