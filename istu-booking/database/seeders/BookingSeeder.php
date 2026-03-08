<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Classroom;
use \App\Models\Booking;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $classrooms = Classroom::all();

        $bookings = Booking::count() < 3 ? [
            Booking::create([
                'user_id' => $users->random()->id,
                'classroom_id' => $classrooms->random()->id,
                'date' => '2026-03-10',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'purpose' => 'Собрание',
                'equipment' => 'Проектор',
                'is_tech_support' => true,
                'comment' => 'Хотим устроить чаепитие'
            ]),
            Booking::create([
                'user_id' => $users->random()->id,
                'classroom_id' => $classrooms->random()->id,
                'date' => '2026-03-10',
                'start_time' => '14:00',
                'end_time' => '16:00',
                'purpose' => 'Собрание',
                'equipment' => 'Проектор',
                'is_tech_support' => true,
                'comment' => 'Перевыборы волонтёрского центра'
            ]),
            Booking::create([
                'user_id' => $users->random()->id,
                'classroom_id' => $classrooms->random()->id,
                'date' => '2026-03-10',
                'start_time' => '17:30',
                'end_time' => '19:00',
                'purpose' => 'Собрание',
                'equipment' => '',
                'is_tech_support' => true,
                'comment' => 'Собрание студ совета'
            ])
        ] : Booking::take(3)->get();
    }
}
