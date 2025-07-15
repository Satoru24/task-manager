<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'sayu.kavin@gmail.com'],
            [
                'name' => 'Sayu Kavin',
                'password' => Hash::make('yourpassword123'),
            ]
        );
    }
}
