<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing users
        $users = User::all();

        // Create 50 photos distributed across users
        foreach ($users as $user) {
            // Each user gets between 3-8 photos
            $photoCount = fake()->numberBetween(3, 8);
            Photo::factory()->count($photoCount)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
