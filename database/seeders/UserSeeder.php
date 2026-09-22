<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['name' => 'Pakde Kuwat'],
            ['pin' => '1234', 'role' => 'operator']
        );

        User::updateOrCreate(
            ['name' => 'Naurah'],
            ['pin' => '1234', 'role' => 'operator']
        );
    }
}