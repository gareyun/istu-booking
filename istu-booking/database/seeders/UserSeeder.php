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
                'name' => 'Иван Иванов',
                'email' => 'mail1@example.com',
                'group' => 'Б22',
                'password' => bcrypt('password')
            ]),
            User::create([
                'name' => 'Александров Александр',
                'email' => 'mail2@example.com',
                'group' => 'Б22',
                'password' => bcrypt('password')
            ]),
            User::create([
                'name' => 'Джонов Джон',
                'email' => 'mail3@example.com',
                'group' => 'Б22',
                'password' => bcrypt('password')
            ])
        ] : User::get();
    }
}
