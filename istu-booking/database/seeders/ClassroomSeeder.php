<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Classroom;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $classrooms = Classroom::count() < 3 ? [
            Classroom::create([
                'room' => '9-2',
                'description' => 'Конференц-зал в Интеграле',
                'equipment' => 'Ноутбук, проектор, флипчарт',
                'capacity' => '30'
            ]),
            Classroom::create([
                'room' => '106',
                'description' => 'Волонтёрский кабинет',
                'equipment' => 'Канцелярия, флипчарт',
                'capacity' => '21'
            ]),
            Classroom::create([
                'room' => 'Холл 2-го этажа',
                'description' => 'Холл для проведения ярмарок или выступлений',
                'equipment' => 'Ноутбук, аудиосистема, цифровой экран, микрофоны',
                'capacity' => '100'
            ])
        ] : Classroom::take(3)->get();
    }
}
