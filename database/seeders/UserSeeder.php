<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 admin users
        User::factory()->admin()->count(2)->create();

        // Create 5 regular users
        User::factory()->user()->count(5)->create();

        // Create 3 guest users
        User::factory()->guest()->count(3)->create();
    }
}
