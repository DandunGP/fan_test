<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::insert([
            'name' => 'Ananda Bayu',
            'email' => 'bayu@gmail.com',
            'npp' => '12345',
            'npp_supervisor' => '11111',
            'password' => Hash::make('password')
        ]);

        User::insert([
            'name' => 'Supervisor',
            'email' => 'spv@gmail.com',
            'npp' => '11111',
            'npp_supervisor' => '',
            'password' => Hash::make('password')
        ]);
    }
}
