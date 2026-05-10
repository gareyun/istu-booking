<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::count() < 3 ? [
            User::create([
                'name' => 'Иванов Александр Михайлович',
                'email' => 'mail1@example.com',
                'group' => 'Б22-191-2',
                'password' => bcrypt('password')
            ]),
            User::create([
                'name' => 'Александров Иван Алексеевич',
                'email' => 'mail2@example.com',
                'group' => 'Б22-191-1',
                'password' => bcrypt('password')
            ]),
            User::create([
                'name' => 'Алексеев Анатолий Юрьевич',
                'email' => 'mail3@example.com',
                'group' => 'Б22-191-3',
                'password' => bcrypt('password')
            ])
        ] : User::get();
    }
}
